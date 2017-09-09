<?php
/*
 * Для хранения настроек приложения, реализует шаблоны проектирования
 * «Реестр» и «Одиночка», интерфейсы Countable, Iterator
 */
class Config implements Countable, Iterator {

    /**
     * собственно данные, настройки приложения
     */
    private $data = array();

    /**
     * для хранения единственного экземпляра данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    private static $instance;

    /**
     * Метод инициализации, необходимо запускать перед началом работы
     */
    public static function init($data){
        self::$instance = new self($data);
    }

    /**
     * Функция возвращает ссылку на экземпляр данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    public static function getInstance(){
        if (is_null(self::$instance)) {
            throw new Exception ('Конфигурация должна быть инициализирована перед использованием');
        }
        return self::$instance;
    }

    /**
     * Закрытый конструктор, необходим для реализации шаблона
     * проектирования «Одиночка»
     */
    private function __construct($data) {
        foreach($data as $key => $value) {
            if (is_array($value) && count($value) == 0) {
                continue;
            }
            $this->data[$key] = $value;
        }
    }

    public function getValue($key) {
        if ( ! isset($this->data[$key])) {
            throw new Exception('Объект с ключом '.__CLASS__.'::data['.$key.'] не существует');
        }
        return $this->data[$key];
    }

    /**
     * Магический метод __get()
     */
    public function __get($key) {
        if ( ! isset($this->data[$key])) {
            throw new Exception('Объект с ключом '.__CLASS__.'::data['.$key.'] не существует');
        }
        // если массив, то создаём ещё один экземпляр класса конфигурации, инициализируем
        // его данными этого массива, ставим на его место в массиве данных и возвращаем
        if (is_array($this->data[$key])) {
            return $this->data[$key] = new self($this->data[$key]);
        }
        return $this->data[$key];
    }

    /**
     * Магический метод __set()
     */
    public function __set($key, $value) {
        if (array_key_exists($key, $this->data)){
            throw new Exception('Объект с ключом '.__CLASS__.'::data['.$key.'] уже существует');
        }
        $this->data[$key] = $value;
    }

    /**
     * Магический метод __isset()
     */
    public function __isset($key) {
        return isset($this->data[$key]);
    }

    /**
     * Магический метод __unset()
     */
    public function __unset($key) {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    /**
     * Магический метод __clone()
     */
    private function __clone() {}


    /**
     * Реализация интерфейсов Countable, Iterator
     */
    public function count() {
        return count($this->data);
    }

    public function rewind() {
        reset($this->data);
    }

    public function current() {
        return current($this->data);
    }

    public function key() {
        return key($this->data);
    }

    public function next(){
        return next($this->data);
    }

    public function valid() {
        $key = key($this->data);
        return ((false !== $key) && (null !== $key));
    }

}