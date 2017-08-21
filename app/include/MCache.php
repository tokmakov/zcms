<?php
/**
 * Класс MCache для кэширования данных с использованием демона Memcached (http://memcached.org/),
 * реализует шаблон проектирования «Одиночка»; для работы класса необходимо установить расширение
 * memcache (http://php.net/manual/ru/book.memcache.php)
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
     * для доступа к настройкам приложения, экземпляр класса Config
     */
    protected $config;


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
        // настройки приложения, экземпляр класса Config
        $this->config = Config::getInstance();

        // установлено php-расширение memcache для работы с сервером Memcached?
        if ( ! class_exists('Memcache', false)) {
            throw new Exception('Расширение Memcache не установлено');
        }

        // создаем соединение с сервером Memcached
        $this->memcache = new Memcache();
        if ( ! @$this->memcache->connect($this->config->cache->mem->host, $this->config->cache->mem->port)) {
            throw new Exception('Не удалось подключиться к серверу Memcached');
        }

        // время жизни кэша в секундах
        $this->cacheTime = $this->config->cache->mem->time;
        // максимальное время блокировки на чтение в секундах
        $this->maxLockTime = $this->config->cache->mem->lock;
    }

    /**
     * Функция получает из кэша значение по ключу доступа $key
     */
    public function getValue($key) {
        // если стоит блокировка, ждем когда она будет снята
        while ($this->isLocked($key)) {
            usleep(10);
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
     * Функция поверяет существование значения с ключом доступа $key; считается,
     * что значение существует, если оно не просто записано в память, но и не
     * установлена блокировка на чтение
     */
    public function isExists($key) {
        /*
         * Здесь важен порядок операций: значение может еще не существовать, а блокировка
         * на него уже стоять. Например, некий процесс обнаружил, что данных в кэше нет,
         * он ставит блокировку и выполняет (тяжелый) запрос к БД. Блокировка нужна, чтобы
         * другие процессы не пытались выполнять тот же самый запрос одновременно с ним.
         * Между моментом установки блокироки и записью значения в кэш может пройти много
         * времени, для примера см. файл app/include/Database.php
         */
        if ($this->isLocked($key)) {
            return false;
        }
        $md5key = md5($key);
        return false !== $this->memcache->get($md5key);
    }

    /**
     * Функция удаляет значение с ключом доступа $key
     */
    public function removeValue($key) {
        // снимаем блокировку
        if ($this->isLocked($key)) {
            $this->unlockValue($key);
        }
        // удаляем значение
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

    private function __clone() {}

}