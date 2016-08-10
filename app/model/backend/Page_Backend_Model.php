<?php
/**
 * Класс Page_Backend_Model для работы со страницами сайта,
 * взаимодействует с базой данных, административная часть сайта
 */
class Page_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает данные о странице с уникальным идентификатором $id
     */
    public function getPage($id) {
        $query = "SELECT
                      `sefurl`, `name`, `title`, `description`, `keywords`, `parent`, `body`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция возвращает массив всех страниц сайта в виде дерева
     * для контроллера, отвечающего за вывод всех страниц
     */
    public function getAllPages() {
        // получаем все страницы
        $query = "SELECT
                      `id`, `name`, `parent`
                  FROM
                      `pages`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования, удаления, перемещения вверх/вниз
        foreach($data as $key => $value) {
            $data[$key]['url'] = array(
                'moveup'   => $this->getURL('backend/page/moveup/id/' . $value['id']),
                'movedown' => $this->getURL('backend/page/movedown/id/' . $value['id']),
                'edit'     => $this->getURL('backend/page/edit/id/' . $value['id']),
                'remove'   => $this->getURL('backend/page/remove/id/' . $value['id'])
            );
        }
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает массив страниц двух верхних уровней в виде дерева,
     * для контроллеров, отвечающих за добавление и редактирование страниц
     */
    public function getPages() {
        // получаем все страницы
        $query = "SELECT
                      `id`, `name`, `parent`
                  FROM
                      `pages`
                  WHERE
                      `parent` = 0 OR `parent` IN (SELECT `id` FROM `pages` WHERE `parent` = 0)
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);

        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция добавляет новую страницу (запись в таблице pages базы данных)
     */
    public function addPage($data) {
        // порядок сортировки
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `pages`
                  WHERE
                      `parent` = :parent";
        $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;

        $query = "INSERT INTO `pages`
                    (
                        `sefurl`,
                        `name`,
                        `title`,
                        `description`,
                        `keywords`,
                        `parent`,
                        `body`,
                        `sortorder`
                    )
                    VALUES
                    (
                        :sefurl,
                        :name,
                        :title,
                        :description,
                        :keywords,
                        :parent,
                        :body,
                        :sortorder
                    )";
        $this->database->execute($query, $data);
        return $this->database->lastInsertId();
    }

    /**
     * Функция обновляет страницу (запись в таблице pages базы данных)
     */
    public function updatePage($data) {
        // получаем идентификатор родителя страницы
        $oldParent = $this->getPageParent($data['id']);
        // если был изменен родитель обновляемой страницы
        if ($oldParent != $data['parent']) {
            // добавляем обновляемую страницу в конец списка дочерних страниц нового родителя
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `pages`
                      WHERE
                          `parent` = :parent";
            $sortorder = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
            $query = "UPDATE
                          `pages`
                      SET
                          `parent`    = :parent,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'parent'    => $data['parent'],
                    'sortorder' => $sortorder,
                    'id'        => $data['id']
                )
            );
            // изменяем порядок сортировки страниц, которые были с обновленной страницей
            // на одном уровне до того, как она поменяла родителя
            $query = "SELECT
                          `id`
                      FROM
                          `pages`
                      WHERE
                          `parent` = :parent
                      ORDER BY
                          `sortorder`";
            $childs = $this->database->fetchAll($query, array('parent' => $oldParent));
            $sortorder = 1;
            foreach ($childs as $child) {
                $query = "UPDATE
                              `pages`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $child['id']));
                $sortorder++;
            }
        }
        unset($data['parent']);
        $query = "UPDATE
                      `pages`
                  SET
                      `sefurl`      = :sefurl,
                      `name`        = :name,
                      `title`       = :title,
                      `description` = :description,
                      `keywords`    = :keywords,
                      `body`        = :body
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция загружает новые файлы и удаляет старые для страницы
     * с уникальным идентификатором $id,
     */
    public function uploadFiles($id) {

        // удаляем файлы, загруженные ранее
        if (isset($_POST['remove'])) {
            foreach ($_POST['remove'] as $name) {
                if (is_file('./files/page/' . $id . '/' . $name)) {
                    unlink('./files/page/' . $id . '/' . $name);
                }
            }
        }

        // загружаем новые файлы
        if (!is_dir('./files/page/' . $id)) {
            mkdir('./files/page/' . $id);
        }
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
                './files/page/' . $id . '/' . $_FILES['files']['name'][$i]
            );
        }
    }

    /**
     * Функция возвращает массив идентификаторов прямых потомков (дочерние страницы)
     * страницы с уникальным идентификатором $id
     */
    public function getChildPages($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `pages`
                  WHERE
                      `parent` = :id";
        $res = $this->database->fetchAll($query, array('id' => $id));
        return $res;
    }

    /**
     * Функция возвращает массив идентификаторов всех потомков (дочерние страницы
     * и дочерние дочерних) страницы с уникальным идентификатором $id
     */
    public function getAllChildPages($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `pages`
                  WHERE
                      `parent` = :parent1 OR `parent` IN (SELECT `id` FROM `pages` WHERE `parent` = :parent2)";
        $items = $this->database->fetchAll($query, array('parent1' => $id, 'parent2' => $id));
        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

    /**
     * Функция возвращает идентификатор родителя страницы с уникальным идентификатором $id
     */
    public function getPageParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id";
        $res = $this->database->fetchOne($query, array('id' => $id));
        return $res;
    }

    /**
     * Функция опускает страницу вниз
     */
    public function movePageDown($id) {
        $id_item_down = $id;
        // порядок следования страницы, которая опускается вниз
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id страницы, которая находится ниже и будет поднята вверх,
        // поменявшись местами со страницей, которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `pages`
                  WHERE
                      `parent` = :parent AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('parent' => $parent, 'order_down' => $order_down));
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами страницы
            $query = "UPDATE `pages` SET `sortorder` = :order_down WHERE `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE `pages` SET `sortorder` = :order_up WHERE `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
        }
    }

    /**
     * Функция поднимает страницу вверх
     */
    public function movePageUp($id) {
        $id_item_up = $id;
        // порядок следования страницы, которая поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `pages`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id страницы, которая находится выше и будет опущена вниз,
        // поменявшись местами со страницей, которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `pages`
                  WHERE
                      `parent` = :parent AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('parent' => $parent, 'order_up' => $order_up));
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами страницы
            $query = "UPDATE `pages` SET `sortorder` = :order_down WHERE `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE `pages` SET `sortorder` = :order_up WHERE `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
        }
    }

    /**
     * Функция удаляет страницу (запись в таблице pages базы данных)
     */
    public function removePage($id) {
        // нельзя удалить страницу, у которой есть дочерние страницы
        $query = "SELECT 1 FROM `pages` WHERE `parent` = :id LIMIT 1";
        if ($this->database->fetchOne($query, array('id' => $id))) {
            return;
        }
        // получаем идентификатор родителя удаляемой страницы
        $query = "SELECT `parent` FROM `pages` WHERE `id` = :id";
        $parent = $this->database->fetchOne($query, array('id' => $id));
        // удаляем страницу
        $query = "DELETE FROM `pages` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // изменяем порядок сортировки страниц, которые с удаленной на одном уровне
        $query = "SELECT `id` FROM `pages` WHERE `parent` = :parent ORDER BY `sortorder`";
        $childs = $this->database->fetchAll($query, array('parent' => $parent));
        if (count($childs) > 0) {
            $sortorder = 1;
            foreach ($childs as $child) {
                $query = "UPDATE `pages` SET `sortorder` = :sortorder WHERE `id` = :id";
                $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $child['id']));
                $sortorder++;
            }
        }
        // удаляем файлы
        $dir = 'files/page/' . $id;
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;
                unlink($dir . '/' . $file);
            }
            rmdir($dir);
        }
    }
}
