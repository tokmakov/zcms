<?php
/**
 * Класс Page_Frontend_Model для показа гланой страницы,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Index_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    public function getIndexPage() {
        // если не включено кэширование данных
        if (!$this->enableDataCache) {
            return $this->indexPage();
        }

        // данные сохранены в кэше?
        $key = __METHOD__ . '()';
        if ($this->register->cache->isExists($key)) {
            // получаем данные из кэша
            $page = $this->register->cache->getValue($key);
        } else {
            $page = $this->indexPage();
            // сохраняем данные в кэше
            $this->register->cache->setValue($key, $page);
        }
        return $page;
    }

    /**
     * Возвращает информацию о главной (стартовой) странице сайта
     */
    private function indexPage() {
        $query = "SELECT `name`, `title`, `description`, `keywords`, `body`
                  FROM `start`
                  WHERE `id` = 1";
        return $this->database->fetch($query, array(), $this->enableDataCache);
    }

    /**
     * Возвращает массив всех баннеров для главной (стартовой) страницы сата
     */
    public function getAllBanners() {
        // если не включено кэширование данных
        if (!$this->enableDataCache) {
            return $this->allBanners();
        }

        // данные сохранены в кэше?
        $key = __METHOD__ . '()';
        if ($this->register->cache->isExists($key)) {
            // получаем данные из кэша
            $banners = $this->register->cache->getValue($key);
        } else {
            $banners = $this->allBanners();
            // сохраняем данные в кэше
            $this->register->cache->setValue($key, $banners);
        }
        return $banners;
    }

    /**
     * Возвращает массив всех баннеров для главной (стартовой) страницы сата
     */
    private function allBanners() {
        $query = "SELECT `id`, `name`, `url`, `alttext`
                  FROM `banners`
                  WHERE `visible` = 1
                  ORDER BY `sortorder`";
        return $this->database->fetchAll($query);
    }

}
