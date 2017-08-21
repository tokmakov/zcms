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

        // время жизни кэша в секундах
        $this->cacheTime = $this->config->cache->file->time;
        // директория для хранения файлов кэша
        $this->dir = $this->config->cache->file->dir;
        // максимальное время блокировки на чтение в секундах
        $this->maxLockTime = $this->config->cache->file->lock;
    }

    /**
     * Функция получает из кэша значение по ключу доступа $key
     */
    public function getValue($key) {
        // если стоит блокировка, ждем когда она будет снята
        while ($this->isLocked($key)) {
            usleep(10);
        }
        if ( ! $this->isExists($key)) {
            throw new Exception('Ошибка при чтении значения из кэша');
        }
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        $temp = unserialize(file_get_contents($file));
        $value = $temp[1];

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
    }

    /**
     * Функция поверяет существование значения с ключом доступа $key; считается,
     * что значение существует, если существует файл, данные не устарели и не
     * стоит блокировка на чтение
     */
    public function isExists($key) {
        /*
         * Здесь важен порядок операций: файл может еще не существовать, а блокировка на
         * него уже стоять. Например, некий процесс обнаружил, что данных в кэше нет, он
         * ставит блокировку и выполняет (тяжелый) запрос к БД. Блокировка нужна, чтобы
         * другие процессы не пытались выполнять тот же самый запрос одновременно с ним.
         * Между моментом установки блокироки и записью значения в кэш может пройти много
         * времени, для примера см. файл app/include/Database.php
         */
        if ($this->isLocked($key)) {
            return false;
        }
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
        // снимаем блокировку
        if ($this->isLocked($key)) {
            $this->unlockValue($key);
        }
        // удаляем значение
        $name = md5($key) . '.txt';
        $file = $this->dir . '/' . $name[0] . '/' . $name[1] . '/' . $name[2] . '/' . $name;
        if(is_file($file)) {
            @unlink($file);
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
            @unlink($file);
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
                            @unlink($dir . '/' . $file);
                        }
                    }
                }
            }
        }
    }

    private function __clone() {}

}