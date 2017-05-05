<?php
/**
 * Абстрактный класс Base_Model, родительский для всех моделей
 */
abstract class Base_Model extends Base {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция принимает на вход линейный массив элеменов, связанных отношениями
     * parent-child, и возвращает массив в виде дерева
     */
    protected function makeTree($data = array()) {
        if (count($data) == 0) {
            return array();
        }
        $dataset = array();
        foreach ($data as $value) {
            $dataset[$value['id']] = $value;
        }
        $tree = array();
        foreach ($dataset as $id => &$node) {
            if ($node['parent'] == 0) {
                $tree[$id] = &$node;
            } else {
                $dataset[$node['parent']]['childs'][$id] = &$node;
            }
        }
        return $tree;
    }

    /**
     * Функция возвращает абсолютный URL вида http://www.server.com/frontend/controller/action/param/value,
     * принимая на вход относительный URL вида frontend/controller/action/param/value
     */
    public function getURL($url) {
        return $this->config->site->url . trim($url, '/');
    }

    /**
     * Функция возвращает единицы измерения товаров в каталоге
     */
    public function getUnits() {
        $units = array(
            0 => '-',
            1 => 'шт',
            2 => 'компл',
            3 => 'упак',
            4 => 'метр',
            5 => 'пара',
            6 => 'кг',
            7 => 'метр' /* в 1С в км */
        );
        return $units;
    }

    /**
     * Функция возвращает список офисов для самовывоза товара со склада
     */
    public function getOffices() {
        $offices = array(
            1 => 'Центральный офис',
            2 => 'Офис продаж «Сокол»',
            3 => 'Офис продаж «Мещанский»',
            4 => 'Офис продаж «Нагорный»',
        );
        return $offices;
    }

}