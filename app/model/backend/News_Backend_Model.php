<?php
/**
 * Класс News_Backend_Model для работы с новостями, взаимодействует
 * с базой данных, административная часть сайта
 */
class News_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив новостей категории с уникальным идентификатором $id
     */
    public function getCategoryNews($id, $start) {
        $query = "SELECT
                      `id`, `name`,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `news`
                  WHERE
                      `category` = :id
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->news->perpage;
        return $this->database->fetchAll($query, array('id' => $id));
    }

    /**
     * Возвращает количество новостей в категории с уникальным идентификатором $id
     */
    public function getCountCategoryNews($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `news`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает массив всех новостей
     */
    public function getAllNews($start = 0) {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `news` `a` INNER JOIN `news_ctgs` `b` ON `a`.`category` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->news->perpage;
        $news = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($news as $key => $value) {
            $news[$key]['url'] = array(
                'edit'   => $this->getURL('backend/news/editnews/id/' . $value['id']),
                'remove' => $this->getURL('backend/news/rmvnews/id/' . $value['id'])
            );
        }
        return $news;
    }

    /**
     * Возвращает общее количество новостей (во всех категориях)
     */
    public function getCountAllNews() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `news`
                  WHERE
                      1";
        return $this->database->fetchOne($query);
    }

    /**
     * Возвращает информацию о новости с уникальным идентификатором $id
     */
    public function getNewsItem($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`,
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
     * Функция добавляет новость (новую запись в таблицу news базы данных)
     */
    public function addNewsItem($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "INSERT INTO `news`
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
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

        // загружаем файлы новости
        $this->uploadFiles($id);

    }

    /**
     * Функция обновляет новость (запись в таблице news базы данных)
     */
    public function updateNewsItem($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "UPDATE
                      `news`
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

        // загружаем файлы новости
        $this->uploadFiles($data['id']);

    }

    /**
     * Функция загружает файл изображения для новости с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // создаем директорию для хранения файлов новости
        if ( ! is_dir('files/news/' . $id)) {
            mkdir('files/news/' . $id);
        }

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/news/' . $id . '/' . $id . '.jpg')) {
                unlink('files/news/' . $id . '/' . $id . '.jpg');
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
                        'files/news/' . $id . '/' . $id . '.jpg',
                        100,
                        100,
                        'jpg'
                    );
                }
            }
        }

    }

    /**
     * Функция загружает новые файлы и удаляет старые для новости
     * с уникальным идентификатором $id
     */
    private function uploadFiles($id) {

        // создаем директорию для хранения файлов новости
        if (!is_dir('files/news/' . $id)) {
            mkdir('files/news/' . $id);
        }

        // удаляем файлы, загруженные ранее
        if (isset($_POST['remove_files'])) {
            foreach ($_POST['remove_files'] as $name) {
                if (is_file('files/news/' . $id . '/' . $name)) {
                    unlink('files/news/' . $id . '/' . $name);
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
            if (!in_array($ext, $exts)) {
                continue;
            }
            // недопустимый mime-тип файла
            if (!in_array($_FILES['files']['type'][$i], $mimeTypes) ) {
                continue;
            }
            // загружаем файл
            move_uploaded_file(
                $_FILES['files']['tmp_name'][$i],
                'files/news/' . $id . '/' . $_FILES['files']['name'][$i]
            );
        }

    }

    /**
     * Функция для удаления новости с уникальным идентификатором $id
     */
    public function removeNewsItem($id) {
        // удаляем запись в таблице `news` БД
        $query = "DELETE FROM
                      `news`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файлы и директорию
        $dir = 'files/news/' . $id;
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
     * Возвращает массив категорий новостей для контроллера, отвечающего
     * за вывод всех категорий
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `news_ctgs`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = array(
                'edit'   => $this->getURL('backend/news/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/news/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;
    }

    /**
     * Возвращает массив категорий новостей для контроллеров, отвечающих
     * за добавление и редактирование новостей
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `news_ctgs`
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
                      `news_ctgs`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию (новую запись в таблицу news_ctgs базы данных)
     */
    public function addCategory($data) {
        $query = "INSERT INTO `news_ctgs`
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
     * Функция обновляет категорию (запись в таблице news_ctgs базы данных)
     */
    public function updateCategory($data) {
        $query = "UPDATE
                      `news_ctgs`
                  SET
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет категорию (запись в таблице news_ctgs базы данных)
     */
    public function removeCategory($id) {
        // проверяем, что не существует новостей этой категории
        $query = "SELECT
                      1
                  FROM
                      `news`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // удаляем запись в таблице `news_ctgs` БД
        $query = "DELETE FROM
                      `news_ctgs`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        return true;
    }
}