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
     * Возвращает массив постов категории с уникальным идентификатором $id
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
     * Возвращает количество постов в категории с уникальным идентификатором $id
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
     * Возвращает массив всех постов блога
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
     * Возвращает общее количество постов блога
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
     * Возвращает пост с уникальным идентификатором $id
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
     * Возвращает массив медиа-файлов, которые можно вставить в пост
     */
    public function getFiles() {
        $folder = 'files/blog/' . date('Y');
        if ( ! is_dir($folder)) {
            mkdir($folder);
        }
        $folder = $folder . '/' . date('m');
        if ( ! is_dir($folder)) {
            mkdir($folder);
        }
        $items = scandir($folder);
        $files = array();
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            $type = null;
            $ext = pathinfo($item, PATHINFO_EXTENSION);
            if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
                $type = 'img';
            } elseif (in_array($ext, array('doc', 'docx'))) {
                $type = 'doc';
            } elseif (in_array($ext, array('xls', 'xlsx'))) {
                $type = 'xls';
            } elseif (in_array($ext, array('ppt', 'pptx'))) {
                $type = 'ppt';
            } elseif ($ext == 'zip') {
                $type = 'zip';
            } elseif ($ext == 'pdf') {
                $type = 'pdf';
            }
            $files[] = array(
                'name' => $item,
                'path' => $this->config->site->url . $folder . '/' . $item,
                'type' => $type,
                'new'  => true,
            );
        }
        if (count($files) > 30) {
            return $files;
        }
        if (date('m') == '01') {
            $folder = 'files/blog/' . (date('Y')-1) . '/12';
        } else {
            $month = (int)date('m') - 1;
            if (strlen($month) == 1) $month = '0' . $month;
            $folder = 'files/blog/' . date('Y') . '/' . $month;
        }
        if (!is_dir($folder)) {
            return $files;
        }
        $items = scandir($folder);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            $type = null;
            $ext = pathinfo($item, PATHINFO_EXTENSION);
            if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
                $type = 'img';
            } elseif (in_array($ext, array('doc', 'docx'))) {
                $type = 'doc';
            } elseif (in_array($ext, array('xls', 'xlsx'))) {
                $type = 'xls';
            } elseif (in_array($ext, array('ppt', 'pptx'))) {
                $type = 'ppt';
            } elseif ($ext == 'zip') {
                $type = 'zip';
            } elseif ($ext == 'pdf') {
                $type = 'pdf';
            }
            $files[] = array(
                'name' => $item,
                'path' => $this->config->site->url . $folder . '/' . $item,
                'type' => $type,
                'new'  => false,
            );
        }
        return $files;
    }

    /**
     * Функция добавляет пост (новую запись в таблицу blog_posts базы данных)
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
     * Функция обновляет пост (запись в таблице blog_posts базы данных)
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
     * Функция загружает файл изображения для поста с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/blog/thumb/' . $id . '.jpg')) {
                unlink('files/blog/thumb/' . $id . '.jpg');
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
                        'files/blog/thumb/' . $id . '.jpg',
                        100,
                        100,
                        'jpg'
                    );
                }
            }
        }

    }

    /**
     * Функция для удаления новости с уникальным идентификатором $id
     */
    public function removePost($id) {
        // удаляем запись в таблице `blog_posts` БД
        $query = "DELETE FROM
                      `blog_posts`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файл изображения
        if (is_file('files/blog/thumb/' . $id . '.jpg')) {
            unlink('files/blog/thumb/' . $id . '.jpg');
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
     * и редактирование постов, для возможности выбора родителя
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
     * Функция добавляет новую категорию (новую запись в таблицу blog_categories
     * базы данных)
     */
    public function addCategory($data) {
        // TODO: установить порядок сортировки
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
     * Функция обновляет категорию (запись в таблице blog_categories базы данных)
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
     * Функция опускает категорию вниз в списке
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
     * Функция поднимает категорию вверх в списке
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
     * Функция удаляет категорию (запись в таблице blog_categories базы данных)
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