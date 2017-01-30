<?php
/**
 * Класс Article_Frontend_Model для работы со статьями, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Article_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех статей; результаты работы кэшируются
     */
    public function getAllArticles($start = 0) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allArticles($start);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Возвращает массив всех статей (во всех категориях)
     */
    protected function allArticles($start = 0) {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `articles` `a` INNER JOIN `articles_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT
                      :start, :limit";
        $articles = $this->database->fetchAll(
            $query,
            array(
                'start' => $start,
                'limit' => $this->config->pager->frontend->article->perpage
            )
        );

        // добавляем в массив статей информацию об URL статьи, картинки, категории
        foreach($articles as $key => $value) {
            $articles[$key]['url']['item'] = $this->getURL('frontend/article/item/id/' . $value['id']);
            if (is_file('files/article/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $articles[$key]['url']['image'] = $this->config->site->url . 'files/article/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $articles[$key]['url']['image'] = $this->config->site->url . 'files/article/default.jpg';
            }
            $articles[$key]['url']['category'] = $this->getURL('frontend/article/category/id/' . $value['ctg_id']);
        }

        return $articles;

    }

    /**
     * Возвращает общее количество статей (во всех категориях);
     * результат работы кэшируется
     */
    public function getCountAllArticles() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countAllArticles();
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
     * Возвращает общее количество статей (во всех категориях)
     */
    protected function countAllArticles() {
        $query = "SELECT COUNT(*) FROM `articles` WHERE 1";
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Возвращает массив статей категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategoryArticles($id, $start) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categoryArticles($id, $start);
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-start-' . $start;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Возвращает массив статей категории с уникальным идентификатором $id
     */
    protected function categoryArticles($id, $start) {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `articles` `a` INNER JOIN `articles_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`category` = :id
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT
                      :start, :limit";
        $articles = $this->database->fetchAll(
            $query,
            array(
                'id'    => $id,
                'start' => $start,
                'limit' => $this->config->pager->frontend->article->perpage
            )
        );

        // добавляем в массив статей информацию об URL статьи, картинки, категории
        foreach($articles as $key => $value) {
            $articles[$key]['url']['item'] = $this->getURL('frontend/article/item/id/' . $value['id']);
            if (is_file('files/article/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $articles[$key]['url']['image'] = $this->config->site->url . 'files/article/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $articles[$key]['url']['image'] = $this->config->site->url . 'files/article/default.jpg';
            }
        }

        return $articles;

    }

    /**
     * Возвращает количество статей в категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCountCategoryArticles($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countCategoryArticles($id);
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
     * Возвращает количество статей в категории с уникальным идентификатором $id
     */
    protected function countCategoryArticles($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `articles`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о статье с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getArticle($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->article($id);
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
     * Возвращает информацию о статье с уникальным идентификатором $id
     */
    protected function article($id) {

        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`, `a`.`description` AS `description`,
                      `a`.`excerpt` AS `excerpt`, `a`.`body` AS `body`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `articles` `a` INNER JOIN `articles_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        return $this->database->fetch($query, array('id' => $id));

    }

    /**
     * Возвращает массив всех категорий статей; результат работы кэшируется
     */
    public function getCategories() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->categories();
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
     * Возвращает массив всех категорий статей
     */
    protected function categories() {

        $query = "SELECT
                      `id`, `name`
                  FROM
                      `articles_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив информацию об URL категорий
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = $this->getURL('frontend/article/category/id/' . $value['id']);
        }

        return $categories;

    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategory($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->category($id);
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
     * Возвращает информацию о категории с уникальным идентификатором $id
     */
    protected function category($id) {

        $query = "SELECT
                      `name`, `description`, `keywords`
                  FROM
                      `articles_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));

    }

}