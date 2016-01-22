<?php
/**
 * Класс FCache для кэширования данных с использованием файлов,
 * реализует шаблон проектирования «Одиночка»
 */
class FCache {

    /**
     * для хранения единственного экземпляра данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    private static $instance;

    /**
     * время жизни кэша в секундах
     */
    private $cacheTime;

    /**
     * директория для хранения файлов кэша
     */
    private $dir;

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
        $this->cacheTime = Config::getInstance()->cache->file->time;
        // директория для хранения файлов кэша
        $this->dir = Config::getInstance()->cache->file->dir;
        // максимальное время блокировки на чтение в секундах
        $this->maxLockTime = Config::getInstance()->cache->file->lock;
    }

    /**
     * Функция получает из кэша значение по ключу доступа $key
     */
    public function getValue($key) {
        // если стоит блокировка, ждем когда она будет снята
        while ($this->isLocked($key)) {
            usleep(1);
        }
        if ( ! $this->isExists($key)) {
            throw new Exception('Ошибка при чтении значения из кэша');
        }
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        $temp = unserialize(file_get_contents($file));
        $value = $temp[1];

// file_put_contents('get-cache.txt', $key . ' ' . $file . PHP_EOL, FILE_APPEND);

        return $value;
    }

    /**
     * Функция записывает в кэш значение $value с ключом доступа $key; если
     * $time не равен нулю, $value будет храниться $time секунд, если $time
     * равен нулю, $value будет храниться $this->cacheTime секунд
     */
    public function setValue($key, $value, $time = 0) {
        if (0 === $time) {
            $expire = time() + $this->cacheTime;
        } else {
            $expire = time() + $time;
        }
        $value = array($expire, $value);
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        file_put_contents($file, serialize($value), LOCK_EX);

// file_put_contents('set-cache.txt', $key . ' ' . $file . PHP_EOL, FILE_APPEND);
    }

    /**
     * Функция поверяет существование значения с ключом доступа $key
     */
    public function isExists($key) {
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        if ( ! is_file($file)) {
            return false;
        }
        $temp = unserialize(file_get_contents($file));
        $expire = $temp[0];
        return time() < $expire;
    }

    /**
     * Функция удаляет значение с ключом доступа $key
     */
    public function removeValue($key) {
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        if(is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Функция блокирует на чтение значение с ключом доступа $key
     */
    public function lockValue($key) {
        // если блокировка уже стоит, ничего не делаем
        if ($this->isLocked($key)) {
            return;
        }
        $name = md5('lock-' . $key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        file_put_contents($file, '');
    }

    /**
     * Функция разблокирует на чтение значение с ключом доступа $key
     */
    public function unlockValue($key) {
        $name = md5('lock-' . $key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        if(is_file($file)) {
            unlink($file);
        }
    }

    /**
     * Функция возвращает true, если значение с ключом доступа $key
     * заблокировано на чтение
     */
    public function isLocked($key) {
        $name = md5('lock-' . $key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        return is_file($file) && ((time() - filemtime($file)) < $this->maxLockTime);
    }

    /**
     * Функция очищает кэш, удаляя все сохраненные данные
     */
    public function clearCache() {
        $dirs = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        foreach ($dirs as $dir1) {
            foreach($dirs as $dir2) {
                foreach($dirs as $dir3) {
                    $dir = $this->dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3;
                    if (is_dir($dir)) {
                        $files = scandir($dir);
                        foreach ($files as $file) {
                            if ($file == '.' || $file == '..') {
                                continue;
                            }
                            unlink($dir . '/' . $file);
                        }
                    }
                }
            }
        }
    }

    private function __clone() {}

}