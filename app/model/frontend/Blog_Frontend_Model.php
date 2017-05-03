<?php
/**
 * Класс Blog_Frontend_Model для работы с блогом, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Blog_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех записей (постов) блога; результаты работы кэшируются
     */
    public function getAllPosts($start) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allPosts($start);
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
     * Возвращает массив всех записей (постов) блога
     */
    protected function allPosts($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                       DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                       DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                       `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `blog_posts` `a` INNER JOIN `blog_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->blog->perpage;
        $posts = $this->database->fetchAll($query);
        // добавляем в массив постов блога информацию об URL поста, картинки, категории
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->blog) { // Content Delivery Network
            $host = $this->config->cdn->url;
        }
        foreach($posts as $key => $value) {
            // URL записи (поста) блога
            $posts[$key]['url']['post'] = $this->getURL('frontend/blog/post/id/' . $value['id']);
            // директория, где лежит файл превьюшки
            $temp = (string)$value['id'];
            $folder = $temp[0];
            // URL превьюшки записи (поста)
            if (is_file('files/blog/thumb/' . $folder . '/' . $value['id'] . '.jpg')) {
                $posts[$key]['url']['image'] = $host . 'files/blog/thumb/' . $folder . '/' . $value['id'] . '.jpg';
            } else {
                $posts[$key]['url']['image'] = $host . 'files/blog/thumb/default.jpg';
            }
            // URL категории записи (поста)
            $posts[$key]['url']['category'] = $this->getURL('frontend/blog/category/id/' . $value['ctg_id']);
        }
        return $posts;
    }

    /**
     * Возвращает общее количество записей (постов) блога (во всех категориях);
     * результат работы кэшируется
     */
    public function getCountAllPosts() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countAllPosts();
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
     * Возвращает общее количество записей (постов) блога (во всех категориях)
     */
    protected function countAllPosts() {
        $query = "SELECT COUNT(*) FROM `blog_posts` WHERE 1";
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Возвращает массив записай (постов) категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategoryPosts($id, $start) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if (!$this->enableDataCache) {
            return $this->categoryPosts($id, $start);
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
     * Возвращает массив записей (постов) категории с уникальным идентификатором $id
     */
    protected function categoryPosts($id, $start) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `blog_posts` `a` INNER JOIN `blog_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`category` = :id
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->blog->perpage;
        $posts = $this->database->fetchAll($query, array('id' => $id));
        /*
         * добавляем в массив постов блога информацию об URL записи (поста), картинки
         */
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->blog) { // Content Delivery Network
            $host = $this->config->cdn->url;
        }
        foreach($posts as $key => $value) {
            // URL записи (поста) блога
            $posts[$key]['url']['post'] = $this->getURL('frontend/blog/post/id/' . $value['id']);
            // директория, где лежит файл превьюшки
            $temp = (string)$value['id'];
            $folder = $temp[0];
            // URL превьюшки записи (поста) блога
            if (is_file('files/blog/thumb/' . $folder . '/' . $value['id'] . '.jpg')) {
                $posts[$key]['url']['image'] = $host . 'files/blog/thumb/' . $folder . '/' . $value['id'] . '.jpg';
            } else {
                $posts[$key]['url']['image'] = $host . 'files/blog/thumb/default.jpg';
            }
        }
        return $posts;
    }

    /**
     * Возвращает количество новостей в категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCountCategoryPosts($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->countCategoryPosts($id);
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
     * Возвращает количество новостей в категории с уникальным идентификатором $id
     */
    protected function countCategoryPosts($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `blog_posts`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о новости с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getPost($id) {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->post($id);
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
     * Возвращает информацию о записи блога с уникальным идентификатором $id
     */
    protected function post($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`,
                      `a`.`excerpt` AS `excerpt`, `a`.`body` AS `body`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `blog_posts` `a` INNER JOIN `blog_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает массив всех категорий блога; результат работы кэшируется
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
     * Возвращает массив всех категорий блога
     */
    protected function categories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `blog_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив информацию об URL категорий
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = $this->getURL('frontend/blog/category/id/' . $value['id']);
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
                      `blog_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

}