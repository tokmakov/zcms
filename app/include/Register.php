<?php
/*
 * Для хранения всех объектов приложения, чтобы везде иметь к ним доступ,
 * реализует шаблоны проектирования «Реестр» и «Одиночка»
 */
class Register {

    /**
     * объекты приложения
     */
    private $data = array();

    /**
     * для хранения единственного экземпляра данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    private static $instance;


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
    private function __construct() {}

    /**
     * Магические методы
     */
    public function __get($key) {
        if ( ! isset($this->data[$key])) {
            throw new Exception('Объект с ключом '.__CLASS__.'::data['.$key.'] не существует');
        }
        return $this->data[$key];
    }

    public function __set($key, $value) {
        if (array_key_exists($key, $this->data)){
            throw new Exception('Объект с ключом '.__CLASS__.'::data['.$key.'] уже существует');
        }
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    private function __clone() {}

}