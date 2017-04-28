<?php
/**
 * Класс Blog_Backend_Model для работы с блогом, взаимодействует
 * с базой данных, административная часть сайта
 */
class Blog_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив записей (постов) блога в категории с
     * уникальным идентификатором $id
     */
    public function getCategoryPosts($id, $start) {
        $query = "SELECT
                      `id`, `name,
                      DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`added`, '%H:%i:%s') AS `time`
                  FROM
                      `blog_posts`
                  WHERE
                      `category` = :id
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->blog->perpage;
        return $this->database->fetchAll($query, array('id' => $id));
    }

    /**
     * Возвращает количество записей (постов) блога в категории с
     * уникальным идентификатором $id
     */
    public function getCountCategoryPosts($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `blog_posts`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает массив всех записей (постов) блога
     */
    public function getAllPosts($start = 0) {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `blog_posts`
                  WHERE
                      1
                  ORDER BY
                      `added` DESC
                  LIMIT " . $start . ", " . $this->config->pager->backend->blog->perpage;
        $posts = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($posts as $key => $value) {
            $posts[$key]['url'] = array(
                'edit'   => $this->getURL('backend/blog/editpost/id/' . $value['id']),
                'remove' => $this->getURL('backend/blog/rmvpost/id/' . $value['id'])
            );
        }
        return $posts;
    }

    /**
     * Возвращает общее количество записей (постов) блога
     */
    public function getCountAllPosts() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `blog_posts`
                  WHERE
                      1";
        return $this->database->fetchOne($query);
    }

    /**
     * Возвращает запись (пост) блога с уникальным идентификатором $id
     */
    public function getPost($id) {
        $query = "SELECT
                      `a`.`name` AS `name`, `a`.`keywords` AS `keywords`,
                      `a`.`description` AS `description`,
                      `a`.`excerpt` AS `excerpt`, `a`.`body` AS `body`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `blog_posts` `a` INNER JOIN `blog_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает массив файлов за последние $count месяцев
     */
    public function getFoldersAndFiles($count = 5) {
        $year = date('Y');
        $month = date('n');
        while ($count) {
            $temp = $month;
            if (strlen($temp) == 1) {
                $temp = '0' . $temp;
            }
            $folder = 'files/blog/' . $year . '/' . $temp;
            $files[$folder] = $this->getFiles($folder);
            $count--;
            $month--;
            if ( ! $month) {
                $month = 12;
                $year--;
            }
        }
        return $files;
    }

    /**
     * Функция возвращает массив файлов в папке $folder
     */
    private function getFiles($folder) {
        if (!is_dir($folder)) {
            return array();
        }
        $items = scandir($folder);
        $files = array();
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            $type = null;
            $ext = pathinfo($item, PATHINFO_EXTENSION);
            if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
                $type = 'img';
            } elseif ($ext == 'pdf') {
                $type = 'pdf';
            } elseif ($ext == 'zip') {
                $type = 'zip';
            } elseif (in_array($ext, array('doc', 'docx'))) {
                $type = 'doc';
            } elseif (in_array($ext, array('xls', 'xlsx'))) {
                $type = 'xls';
            } elseif (in_array($ext, array('ppt', 'pptx'))) {
                $type = 'ppt';
            }
            $files[] = array(
                'name' => $item,
                'path' => $this->config->site->url . $folder . '/' . $item,
                'type' => $type
            );
        }
        return $files;
    }

    /**
     * Функция добавляет новую запись (пост) блога
     */
    public function addPost($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "INSERT INTO `blog_posts`
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

    }

    /**
     * Функция обновляет запись (пост) блога
     */
    public function updatePost($data) {

        $tmp           = explode( '.', $data['date'] );
        $data['added'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0].' '.$data['time']; // дата и время
        unset($data['date']);
        unset($data['time']);
        $query = "UPDATE
                      `blog_posts`
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

    }

    /**
     * Функция загружает файл изображения для записи (поста) блога с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // директория, куда будет загружен файл изображения
        $temp = (string)$id;
        $folfer = $temp[0];

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/blog/thumb/' . $folfer . '/' . $id . '.jpg')) {
                unlink('files/blog/thumb/' . $folfer . '/' . $id . '.jpg');
            }
        }

        // создаем директорию, если она еще не существует
        if ( ! is_dir('files/blog/thumb/' . $folfer)) {
            mkdir('files/blog/thumb/' . $folfer);
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
                        'files/blog/thumb/' . $folfer . '/' . $id . '.jpg',
                        100,
                        100,
                        'jpg'
                    );
                }
            }
        }

    }

    /**
     * Функция загружает файл для дальнейшей вставки его в запись (пост) блога
     */
    public function uploadFiles() {

        if (empty($_FILES['files'])) {
            return;
        }

        // создаем папку, если она еще не существует
        $year = date('Y');
        $folder = 'files/blog/' . $year;
        if ( ! is_dir($folder)) {
            mkdir($folder);
        }
        $month = date('m');
        $folder = $folder . '/' . $month;
        if ( ! is_dir($folder)) {
            mkdir($folder);
        }

        // допустимые mime-типы файлов
        $mimeTypes = array(
            'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'application/x-zip-compressed', 'application/pdf');
        // донустимые расширения файлов
        $exts = array(
            'jpg', 'jpeg', 'gif', 'png', 'bmp', 'doc', 'docx',
            'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'pdf'
        );
        $count = count($_FILES['files']['name']);
        // цикл по всем загруженным файлам
        for ($i = 0; $i < $count; $i++) {
            // произошла ошибка при загрузке файла
            if ($_FILES['files']['error'][$i]) {
                continue;
            }
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
                $folder . '/' . $_FILES['files']['name'][$i]
            );
        }

    }

    /**
     * Функция для удаления записи (поста) блога с уникальным идентификатором $id
     */
    public function removePost($id) {
        // удаляем запись в таблице `blog_posts` БД
        $query = "DELETE FROM
                      `blog_posts`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файл изображения
        $temp = (string)$id;
        $folfer = $temp[0];
        if (is_file('files/blog/thumb/' . $folfer . '/' . $id . '.jpg')) {
            unlink('files/blog/thumb/' . $folfer . '/' . $id . '.jpg');
        }
    }

    /**
     * Возвращает массив категорий записей (постов) блога для контроллера,
     * отвечающего за вывод всех категорий
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `blog_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = array(
                'up'     => $this->getURL('backend/blog/ctgup/id/' . $value['id']),
                'down'   => $this->getURL('backend/blog/ctgdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/blog/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/blog/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;
    }

    /**
     * Возвращает массив категорий для контроллеров, отвечающих за добавление
     * и редактирование записи (поста) блога, для возможности выбора родителя
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `blog_categories`
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
                      `blog_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию блога
     */
    public function addCategory($data) {
        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `blog_categories`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query) + 1;
        // добавляем новую категорию блога
        $query = "INSERT INTO `blog_categories`
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
     * Функция обновляет категорию блога
     */
    public function updateCategory($data) {
        $query = "UPDATE
                      `blog_categories`
                  SET
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция опускает категорию блога вниз в списке
     */
    public function moveCategoryDown($id) {
        $id_item_down = $id;
        // порядок следования категории, которая опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `blog_categories`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id категории, которая находится ниже
        // и будет поднята вверх, поменявшись местами с категорией,
        // которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `blog_categories`
                  WHERE
                      `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_down' => $order_down));
        // если запрос вернул false, значит категория и так самая последняя
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `blog_categories`
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
                          `blog_categories`
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
     * Функция поднимает категорию блога вверх в списке
     */
    public function moveCategoryUp($id) {
        $id_item_up = $id;
        // порядок следования категории, которая поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `blog_categories`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id категории, которая находится выше
        // и будет опущена вниз, поменявшись местами с категорией,
        // которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `blog_categories`
                  WHERE
                      `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_up' => $order_up));
        // если запрос вернул false, значит категория и так самая первая
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `blog_categories`
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
                          `blog_categories`
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
     * Функция удаляет категорию блога
     */
    public function removeCategory($id) {
        // проверяем, что не существует постов в этой категории
        $query = "SELECT
                      1
                  FROM
                      `blog_posts`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // удаляем запись в таблице `blog_categories` БД
        $query = "DELETE FROM
                      `blog_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // TODO: установить порядок сортировки
        return true;
    }
}