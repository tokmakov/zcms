<?php
/**
 * Абстрактный класс Sitemap_Frontend_Controller, родительский для всех
 * контроллеров, работающих с картой сайта, общедоступная часть сайта
 */
abstract class Sitemap_Frontend_Controller extends Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

}
