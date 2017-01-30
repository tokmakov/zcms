<?php
/**
 * Класс Page_Frontend_Model для показа страниц сайта,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Page_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает информацию о странице с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getPage($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->page($id);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Возвращает информацию о странице с уникальным идентификатором $id
     */
    protected function page($id) {
        $query = "SELECT
                      `name`, `title`, `description`, `keywords`, `parent`, `body`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция возвращает путь до страницы с уникальным идентификатором $id;
     * результат работы кэшируется
     * ВАЖНО! Этот метод был нужен для формирования хлебных крошек, сейчас
     * не используется и будет скоро уделён. Хлебные крошки формирует метод
     * Sitemap_Frontend_Model::getBreadcrumbs()
     */
    public function getPagePath($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запросов к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->pagePath($id);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будут выполнены запросы к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает путь до страницы с уникальным идентификатором $id
     * ВАЖНО! См. комментарий к методу Page_Frontend_Model::getPagePath()
     */
    protected function pagePath($id) {

        $query = "SELECT
                      `parent`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id";
        $parent = $this->database->fetchOne($query, array('id' => $id));
        $path = array();
        if ($parent) {
            $query = "SELECT
                          `id`, `name`, `parent`
                      FROM
                          `pages`
                      WHERE
                          `id` = :id";
            $result = $this->database->fetch($query, array('id' => $parent));
            $path[] = array('url' => $this->getURL('frontend/page/index/id/' . $result['id']), 'name' => $result['name']);
            if ($result['parent']) {
                $query = "SELECT
                              `id`, `name`, `parent`
                          FROM
                              `pages`
                          WHERE
                              `id` = :id";
                $res = $this->database->fetch($query, array('id' => $result['parent']));
                $path[] = array('url' => $this->getURL('frontend/page/index/id/' . $res['id']), 'name' => $res['name']);
            }
        }
        $path[] = array('url' => $this->getURL('frontend/index/index'), 'name' => 'Главная');
        $path = array_reverse($path);

        return $path;

    }

    /**
     * Функция возвращает массив всех страниц сайта в виде дерева;
     * результат работы кэшируется
     * ВАЖНО! Этот метод был нужен для формирования карты сайта, сейчас
     * не используется и будет скоро уделён
     */
    public function getAllPages() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allPages();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()';
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив всех страниц сайта в виде дерева
     * ВАЖНО! См. комментарий к методу Page_Frontend_Model::getAllPages()
     */
    protected function allPages() {

        // получаем все страницы
        $query = "SELECT
                      `id`, `name`, `parent`
                  FROM
                      `pages`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $pages = $this->database->fetchAll($query);
        // добавляем в массив ссылки на страницы
        foreach($pages as $key => $value) {
            $pages[$key]['url'] = $this->getURL('frontend/page/index/id/' . $value['id']);
        }
        // строим дерево
        $tree = $this->makeTree($pages);

        return $tree;

    }

    /**
     * Функция возвращает массив SEF URL всех страниц сайта; результат работы кэшируется
     * на уровне базы данных
     */
    protected function getAllPagesSEF() {

        // получаем все страницы
        $query = "SELECT
                      `id`, `sefurl`
                  FROM
                      `pages`
                  WHERE
                      1";
        return $this->database->fetchAll($query, array(), $this->enableDataCache);

    }

}
