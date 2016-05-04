<?php
/**
 * Класс Cache для кэширования данных, работает с классами FCache
 * (кэширование с использованием файлов) и MCache (кэширование в
 * оперативной памяти), реализует шаблон проектирования «Одиночка»
 */
class Cache {

    /**
     * для хранения единственного экземпляра данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    private static $instance;

    /**
     * экземпляр класса кэша с использованием файлов
     */
    private $instanceFileCache;

    /**
     * кэширование с использованием демона Memcached возможно?
     */
    private $enableMemCache;

    /**
     * экземпляр класса кэша с использованием демона Memcached
     */
    private $instanceMemCache;


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
        // экземпляр класса кэша с использованием файлов
        $this->instanceFileCache = FCache::getInstance();
        // кэширование с использованием демона Memcached возможно?
        try {
            // экземпляр класса кэша с использованием демона Memcached
            $this->instanceMemCache = MCache::getInstance();
            $this->enableMemCache = true;
        } catch (Exception $e) {
            $this->enableMemCache = false;
        }
    }

    /**
     * Функция получает из кэша значение по ключу доступа $key
     */
    public function getValue($key) {
        if ($this->enableMemCache) { // если доступен кэш в оперативной памяти
            try {
                // получаем данные из оперативной памяти
                $value = $this->instanceMemCache->getValue($key);
            } catch (Exception $e) {
                // в оперативной памяти данных нет, получаем данные из файла
                $value = $this->instanceFileCache->getValue($key);
                // записываем данные в оперативную память
                $this->instanceMemCache->setValue($key, $value);
            }
        } else { // получаем данные из файлового кэша
            $value = $this->instanceFileCache->getValue($key);
        }
        return $value;
    }

    /**
     * Функция записывает в кэш значение $value с ключом доступа $key; если
     * $time не равен нулю, $value будет храниться $time секунд, если $time
     * равен нулю, $value будет храниться количество секунд, указанных в
     * настройках приложения (см. файл app/settings.php)
     */
    public function setValue($key, $value, $time = 0) {
        if ($this->enableMemCache) { // если доступен кэш в оперативной памяти
            // записываем данные в оперативную память
            $this->instanceMemCache->setValue($key, $value, $time);
            /*
             * если задано время хранения $time, отличное от нуля, нет смысла
             * записывать данные в файловый кэш, потому как время хранения
             * одинаковое и файловый кэш устареет одновременно с кэшем в
             * оперативной памяти
             */
            if ($time) {
                return;
            }
        }
        // записываем данные в файловый кэш
        $this->instanceFileCache->setValue($key, $value, $time);
    }

    /**
     * Функция поверяет существование значения с ключом доступа $key
     */
    public function isExists($key) {
        if ($this->enableMemCache && $this->instanceMemCache->isExists($key)) {
            return true;
        }
        if ($this->instanceFileCache->isExists($key)) {
            return true;
        }
        return false;
    }

    /**
     * Функция удаляет значение с ключом доступа $key
     */
    public function removeValue($key) {
        if ($this->enableMemCache) {
            $this->instanceMemCache->removeValue($key);
        }
        $this->instanceFileCache->removeValue($key);
    }

    /**
     * Функция блокирует на чтение значение с ключом доступа $key
     */
    public function lockValue($key) {
        // если блокировка уже стоит, ничего не делаем
        if ($this->isLocked($key)) {
            return;
        }
        if ($this->enableMemCache) {
            $this->instanceMemCache->lockValue($key);
        }
        $this->instanceFileCache->lockValue($key);
    }

    /**
     * Функция разблокирует на чтение значение с ключом доступа $key
     */
    public function unlockValue($key) {
        if ($this->enableMemCache) {
            $this->instanceMemCache->unlockValue($key);
        }
        $this->instanceFileCache->unlockValue($key);
    }

    /**
     * Функция возвращает true, если значение с ключом доступа $key
     * заблокировано на чтение
     */
    public function isLocked($key) {
        if ($this->enableMemCache && $this->instanceMemCache->isLocked($key)) {
            return true;
        }
        if ($this->instanceFileCache->isLocked($key)) {
            return true;
        }
        return false;
    }

    /**
     * Функция очищает кэш, удаляя все сохраненные данные
     */
    public function clearCache() {
        if ($this->enableMemCache) {
            // удаляем кэш в оперативной памяти
            $this->instanceMemCache->clearCache();
        }
        // удаляем файловый кэш
        $this->instanceFileCache->clearCache();
    }

    private function __clone() {}

}