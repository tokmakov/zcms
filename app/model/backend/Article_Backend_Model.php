<?php
/**
 * Класс Article_Backend_Model для работы со статьями, взаимодействует
 * с базой данных, административная часть сайта
 */
class Article_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив статей категории с уникальным идентификатором $id
     */
    public function getCategoryArticles($id, $start) {
        $query = "SELECT
                      `id`, `name`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `articles`
                  WHERE
                      `category` = :id
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->article->perpage;
        return $this->database->fetchAll($query, array('id' => $id));
    }

    /**
     * Возвращает количество статей в категории с уникальным идентификатором $id
     */
    public function getCountCategoryArticles($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `articles`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает массив всех статей (во всех категориях)
     */
    public function getAllArticles($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `articles` `a` INNER JOIN `articles_categories` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->article->perpage;
        $articles = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($articles as $key => $value) {
            $articles[$key]['url'] = array(
                'edit'   => $this->getURL('backend/article/edititem/id/' . $value['id']),
                'remove' => $this->getURL('backend/article/rmvitem/id/' . $value['id'])
            );
        }
        return $articles;
    }

    /**
     * Возвращает общее количество статей (во всех категориях)
     */
    public function getCountAllArticles() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `articles`
                  WHERE
                      1";
        return $this->database->fetchOne($query);
    }

    /**
     * Возвращает информацию о статье с уникальным идентификатором $id
     */
    public function getArticle($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`,
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
     * Функция добавляет статью (новую запись в таблицу `articles` базы данных)
     */
    public function addArticle($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "INSERT INTO `articles`
                  (
                      `category`,
                      `name`,
                      `keywords`,
                      `description`,
                      `excerpt`,
                      `body`,
                      `added`
                  )
                  VALUES
                  (
                      :category,
                      :name,
                      :keywords,
                      :description,
                      :excerpt,
                      :body,
                      :added
                  )";
        $this->database->execute($query, $data);
        // уникальный идентификатор добавленной статьи
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

        // загружаем файлы статьи
        $this->uploadFiles($id);

    }

    /**
     * Функция обновляет статью (запись в таблице `articles` базы данных)
     */
    public function updateArticle($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "UPDATE
                      `articles`
                  SET
                      `category`    = :category,
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description,
                      `excerpt`     = :excerpt,
                      `body`        = :body,
                      `added`       = :added
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

        // загружаем файлы статьи
        $this->uploadFiles($data['id']);

    }

    /**
     * Функция загружает файл изображения для статьи с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // создаем директорию для хранения файлов статьи
        if ( ! is_dir('files/article/' . $id)) {
            mkdir('files/article/' . $id);
        }

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/article/' . $id . '/' . $id . '.jpg')) {
                unlink('files/article/' . $id . '/' . $id . '.jpg');
            }
        }

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['image']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if (0 == $_FILES['image']['error']) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['image']['type'], $mimetypes)) {
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['image']['tmp_name'],
                        'files/article/' . $id . '/' . $id . '.jpg',
                        100,
                        100,
                        'jpg'
                    );
                }
            }
        }

    }

    /**
     * Функция загружает новые файлы и удаляет старые для статьи
     * с уникальным идентификатором $id
     */
    private function uploadFiles($id) {

        // создаем директорию для хранения файлов статьи
        if (!is_dir('files/article/' . $id)) {
            mkdir('files/article/' . $id);
        }

        // удаляем файлы, загруженные ранее
        if (isset($_POST['remove_files'])) {
            foreach ($_POST['remove_files'] as $name) {
                if (is_file('files/article/' . $id . '/' . $name)) {
                    unlink('files/article/' . $id . '/' . $name);
                }
            }
        }

        // загружаем новые файлы
        $mimeTypes = array(
            'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'application/x-zip-compressed', 'application/pdf');
        $exts = array(
            'jpg', 'jpeg', 'gif', 'png', 'bmp', 'doc', 'docx',
            'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'pdf'
        );
        $count = count($_FILES['files']['name']);
        // цикл по всем загруженным файлам
        for ($i = 0; $i < $count; $i++) {
            $ext = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
            // недопустимое расширение файла
            if ( ! in_array($ext, $exts)) {
                continue;
            }
            // недопустимый mime-тип файла
            if ( ! in_array($_FILES['files']['type'][$i], $mimeTypes) ) {
                continue;
            }
            // загружаем файл
            move_uploaded_file(
                $_FILES['files']['tmp_name'][$i],
                'files/article/' . $id . '/' . $_FILES['files']['name'][$i]
            );
        }

    }

    /**
     * Функция удаляет статью с уникальным идентификатором $id
     */
    public function removeArticle($id) {
        // удаляем запись в таблице `articles` БД
        $query = "DELETE FROM
                      `articles`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файлы и директорию
        $dir = 'files/article/' . $id;
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                unlink($dir . '/' . $file);
            }
            rmdir($dir);
        }
    }

    /**
     * Возвращает массив категорий статей для контроллера, отвечающего
     * за вывод всех категорий
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `articles_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = array(
                'up'     => $this->getURL('backend/article/ctgup/id/' . $value['id']),
                'down'   => $this->getURL('backend/article/ctgdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/article/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/article/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;
    }

    /**
     * Возвращает массив категорий статей для контроллеров, отвечающих
     * за добавление и редактирование статей
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `articles_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `keywords`, `description`
                  FROM
                      `articles_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию (новую запись в таблицу articles_categories базы данных)
     */
    public function addCategory($data) {
        // TODO: установить порядок сортировки
        $query = "INSERT INTO `articles_categories`
                  (
                      `name`,
                      `keywords`,
                      `description`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :keywords,
                      :description,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет категорию (запись в таблице `articles_categories` базы данных)
     */
    public function updateCategory($data) {
        $query = "UPDATE
                      `articles_categories`
                  SET
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция опускает категорию вниз в списке
     */
    public function moveCategoryDown($id) {
        $id_item_down = $id;
        // порядок следования категории, которая опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `articles_categories`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id категории, которая находится ниже и будет поднята вверх,
        // поменявшись местами с категорией, которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `articles_categories`
                  WHERE
                      `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_down' => $order_down));
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `articles_categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `articles_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up' => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

    /**
     * Функция поднимает категорию вверх в списке
     */
    public function moveCategoryUp($id) {
        $id_item_up = $id;
        // порядок следования баннера, который поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `articles_categories`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id категории, которая находится выше и будет опущена вниз,
        // поменявшись местами с категорией, которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `articles_categories`
                  WHERE
                      `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_up' => $order_up));
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `articles_categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `articles_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up' => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

    /**
     * Функция удаляет категорию (запись в таблице `articles_categories` базы данных)
     */
    public function removeCategory($id) {
        // проверяем, что не существует статей этой категории
        $query = "SELECT
                      1
                  FROM
                      `articles`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // удаляем запись в таблице `articles_categories` БД
        $query = "DELETE FROM
                      `articles_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // TODO: установить порядок сортировки
        return true;
    }
}