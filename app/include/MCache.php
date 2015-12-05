<?php
/**
 * Класс MCache для кэширования данных с использованием кеширующего
 * демона Memcached, реализует шаблон проектирования «Одиночка»
 */
class MCache {

    /**
     * для хранения единственного экземпляра данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    private static $instance;

    /**
     * для хранения соединения с сервером Memcached
     */
    private $memcache;

    /**
     * время жизни кэша в секундах
     */
    private $cacheTime;

    /**
     * максимальное время блокировки на чтение в секундах;
     * если вызывающий код не снял блокировку, она будет
     * снята через $maxLockTime секунд
     */
    private $maxLockTime;


    /**
     * Функция возвращает ссылку на экземпляр данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Закрытый конструктор, необходим для реализации шаблона
     * проектирования «Одиночка»
     */
    private function __construct() {
        // время жизни кэша в секундах
        $this->cacheTime = Config::getInstance()->cache->mem->time;
        // максимальное время блокировки на чтение в секундах
        $this->maxLockTime = Config::getInstance()->cache->mem->lock;
        // создаем соединение с сервером Memcached
        $this->memcache = new Memcache();
        if (!$this->memcache->connect(Config::getInstance()->cache->mem->host, Config::getInstance()->cache->mem->port)) {
            throw new Exception('Не удалось подключиться к сереверу Memcached');
        }
    }

    /**
     * Функция получает из кэша значение по ключу доступа $key
     */
    public function getValue($key) {
        // если стоит блокировка, ждем когда она будет снята
        while ($this->isLocked($key)) {
            usleep(1);
        }
        $md5key = md5($key);
        $value = $this->memcache->get($md5key);
        if (false === $value) {
            throw new Exception('Ошибка при чтении значения из кэша');
        }
        if ('{%b:f%}' === $value) {
            return false;
        }
        return $value;
    }

    /**
     * Функция записывает в кэш значение $value с ключом доступа $key; если
     * $time не равен нулю, $value будет храниться $time секунд, если $time
     * равен нулю, $value будет храниться $this->cacheTime секунд
     */
    public function setValue($key, $value, $time = 0) {
        $md5key = md5($key);
        if (0 === $time) {
            $time = $this->cacheTime;
        }
        if (false === $value) {
            $value = '{%b:f%}';
        }
        $compress = is_bool($value) || is_int($value) || is_float($value) ? false : MEMCACHE_COMPRESSED;
        $this->memcache->set($md5key, $value, $compress, $time);
    }

    /**
     * Функция поверяет существование значения с ключом доступа $key
     */
    public function isExists($key) {
        $md5key = md5($key);
        return false !== $this->memcache->get($md5key);
    }

    /**
     * Функция удаляет значение с ключом доступа $key
     */
    public function removeValue($key) {
        $md5key = md5($key);
        $this->memcache->delete($md5key);
    }

    /**
     * Функция блокирует на чтение значение с ключом доступа $key
     */
    public function lockValue($key) {
        $md5key = md5('lock-' . $key);
        $this->memcache->add($md5key, true, false, $this->maxLockTime);
    }

    /**
     * Функция разблокирует на чтение значение с ключом доступа $key
     */
    public function unlockValue($key) {
        $md5key = md5('lock-' . $key);
        $this->memcache->delete($md5key);
    }

    /**
     * Функция возвращает true, если значение с ключом доступа $key
     * заблокировано на чтение
     */
    public function isLocked($key) {
        $md5key = md5('lock-' . $key);
        return $this->memcache->get($md5key);
    }

    /**
     * Функция очищает кэш, удаляя все сохраненные данные
     */
    public function clearCache() {
        $this->memcache->flush();
    }
}