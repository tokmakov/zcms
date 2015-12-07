<?php
/**
 * Класс News_Frontend_Model для работы с новостями, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class News_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех новостей; результаты работы кэшируются
     */
    public function getAllNews($start = 0) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->allNews($start);
        }

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
     * Возвращает массив всех новостей
     */
    protected function allNews($start = 0) {
        $query = "SELECT `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                         DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                         DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                         `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM `news` `a` INNER JOIN `news_ctgs` `b` ON `a`.`category` = `b`.`id`
                  WHERE 1
                  ORDER BY `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->news->perpage;
        $news = $this->database->fetchAll($query);
        // добавляем в массив новостей информацию об URL новости, картинки, категории
        foreach($news as $key => $value) {
            $news[$key]['url']['item'] = $this->getURL('frontend/news/item/id/' . $value['id']);
            if (is_file('./files/news/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/default.jpg';
            }
            $news[$key]['url']['category'] = $this->getURL('frontend/news/category/id/' . $value['ctg_id']);
        }
        return $news;
    }

    /**
     * Возвращает общее количество новостей (во всех категориях);
     * результат работы кэшируется
     */
    public function getCountAllNews() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countAllNews();
        }

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
     * Возвращает общее количество новостей (во всех категориях)
     */
    protected function countAllNews() {
        $query = "SELECT COUNT(*) FROM `news` WHERE 1";
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Возвращает массив новостей категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategoryNews($id, $start) {
        // если не включено кэширование данных
        if (!$this->enableDataCache) {
            return $this->categoryNews($id, $start);
        }

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
     * Возвращает массив новостей категории с уникальным идентификатором $id
     */
    protected function categoryNews($id, $start) {
        $query = "SELECT `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`excerpt` AS `excerpt`,
                         DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                         DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                         `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `news` `a` INNER JOIN `news_ctgs` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`category` = :id
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->frontend->news->perpage;
        $news = $this->database->fetchAll($query, array('id' => $id), $this->enableDataCache);
        // добавляем в массив новостей информацию об URL новости, картинки, категории
        foreach($news as $key => $value) {
            $news[$key]['url']['item'] = $this->getURL('frontend/news/item/id/' . $value['id']);
            if (is_file('./files/news/' . $value['id'] . '/' . $value['id'] . '.jpg')) {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/' . $value['id'] . '/' . $value['id'] . '.jpg';
            } else {
                $news[$key]['url']['image'] = $this->config->site->url . 'files/news/default.jpg';
            }
        }
        return $news;
    }

    /**
     * Возвращает количество новостей в категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCountCategoryNews($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countCategoryNews($id);
        }

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
    protected function countCategoryNews($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `news`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о новости с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getNewsItem($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->newsItem($id);
        }

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
     * Возвращает информацию о новости с уникальным идентификатором $id
     */
    protected function newsItem($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`, `a`.`description` AS `description`,
                      `a`.`excerpt` AS `excerpt`, `a`.`body` AS `body`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `news` `a` INNER JOIN `news_ctgs` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает массив всех категорий новостей; результат работы кэшируется
     */
    public function getCategories() {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->categories();
        }

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
     * Возвращает массив всех категорий новостей
     */
    protected function categories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `news_ctgs`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив информацию об URL категорий
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = $this->getURL('frontend/news/category/id/' . $value['id']);
        }
        return $categories;
    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id;
     * результат работы кэшируется
     */
    public function getCategory($id) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->category($id);
        }

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
                      `news_ctgs`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

}