<?php
/**
 * Абстрактный класс Frontend_View, родительский для всех представлений
 * общедоступной части сайта
 */
abstract class Frontend_View extends Base_View {

    public function __construct() {
        parent::__construct();
    }

}