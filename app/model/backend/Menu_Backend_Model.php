<?php
/**
 * Класс Menu_Backend_Model для работы с главным меню сайта,
 * взаимодействует с базой данных, административная часть сайта
 */
class Menu_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает данные о пункте меню с уникальным идентификатором $id
     */
    public function getMenuItem($id) {
        $query = "SELECT
                      `name`, `url`, `parent`
                  FROM
                      `menu`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция возвращает массив всех пунктов меню в виде дерева,
     * для контроллера, отвечающего за вывод всех пунктов меню
     */
    public function getAllMenuItems() {
        // получаем все пункты меню
        $query = "SELECT
                      `id`, `name`, `parent`, `sortorder`
                  FROM
                      `menu`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования, удаления, перемещения вверх и вниз
        foreach($data as $key => $value) {
            $data[$key]['url'] = array(
                'up'     => $this->getURL('backend/menu/itemup/id/' . $value['id']),
                'down'   => $this->getURL('backend/menu/itemdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/menu/edititem/id/' . $value['id']),
                'remove' => $this->getURL('backend/menu/rmvitem/id/' . $value['id'])
            );
        }
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает массив пунктов меню двух верхних уровней в виде дерева,
     * для контроллеров, отвечающих за добавление и редактирование отдельного
     * пункта меню
     */
    public function getMenuItems() {
        // получаем все пункты меню
        $query = "SELECT
                      `id`, `name`, `url`, `parent`
                  FROM
                      `menu`
                  WHERE
                      `parent` = 0 OR `parent` IN (SELECT `id` FROM `menu` WHERE `parent` = 0)
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает массив всех страниц сайта в виде дерева
     * для контроллеров, отвечающих за добавление/редактирование
     * пункта меню
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
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает корневые категории каталога, для контроллеров,
     * отвечающих за добавление/редактирование пункта меню
     */
    public  function getRootCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `categories`
                  WHERE
                      `parent` = 0
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query, array());
    }

    /**
     * Возвращает массив категорий блога, для контроллеров,
     * отвечающих за добавление/редактирование пункта меню
     */
    public function getBlogCategories() {
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
     * Возвращает массив категорий типовых решений, для контроллеров,
     * отвечающих за добавление/редактирование пункта меню
     */
    public function getSolutionCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `solutions_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция добавляет новый пункт меню (запись в таблице menu базы данных)
     */
    public function addMenuItem($data) {
        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `menu`
                  WHERE
                      `parent` = :parent";
        $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;

        $query = "INSERT INTO `menu`
                  (
                      `name`,
                      `url`,
                      `parent`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :url,
                      :parent,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет пункт меню (запись в таблице menu базы данных)
     */
    public function updateMenuItem($data) {
        // получаем идентификатор родителя обновляемого пункта меню
        $oldParent = $this->getMenuItemParent($data['id']);
        // если был изменен родитель обновляемого пункта меню
        if ($oldParent != $data['parent']) {
            // добавляем обновляемый пункт меню в конец списка дочерних элементов нового родителя
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `menu`
                      WHERE
                          `parent` = :parent";
            $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
            $query = "UPDATE
                          `menu`
                      SET
                          `name`      = :name,
                          `url`       = :url,
                          `parent`    = :parent,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, $data);
            // изменяем порядок сортировки пунктов меню, которые были с обновленным пунктом меню
            // на одном уровне до того, как он поменял родителя
            $query = "SELECT
                          `id`
                      FROM
                          `menu`
                      WHERE
                          `parent` = :parent
                      ORDER BY
                          `sortorder`";
            $childs = $this->database->fetchAll($query, array('parent' => $oldParent));
            $sortorder = 1;
            foreach ($childs as $child) {
                $query = "UPDATE
                              `menu`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $child['id']));
                $sortorder++;
            }
        } else {
            unset($data['parent']);
            $query = "UPDATE
                          `menu`
                      SET
                          `name` = :name,
                          `url`  = :url
                      WHERE
                          `id` = :id";
            $this->database->execute($query, $data);
        }
    }

    /**
     * Функция возвращает массив идентификаторов дочерних элементов (прямых потомков)
     * пункта меню с уникальным идентификатором $id
     */
    public function getChildItems($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `menu`
                  WHERE
                      `parent` = :id";
        $res = $this->database->fetchAll($query, array('id' => $id));
        $ids = array();
        foreach ($res as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

    /**
     * Функция возвращает массив идентификаторов всех потомков (дочерние и дочерние
     * дочерних) пункта меню с уникальным идентификатором $id
     */
    public function getAllChildItems($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `menu`
                  WHERE
                      `parent` = :parent1 OR `parent` IN (SELECT `id` FROM `menu` WHERE `parent` = :parent2)";
        return $this->database->fetchAll($query, array('parent1' => $id, 'parent2' => $id));
    }

    /**
     * Функция возвращает идентификатор родительского элемента пункта меню
     * с уникальным идентификатором $id
     */
    public function getMenuItemParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `menu`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция опускает пункт меню вниз
     */
    public function moveMenuItemDown($id) {
        $id_item_down = $id;
        // порядок следования пункта меню, который опускается вниз
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `menu`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id пункта меню, который находится ниже и будет поднят вверх,
        // поменявшись местами с пунктом меню, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `menu`
                  WHERE
                      `parent` = :parent AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent' => $parent,
                'order_down' => $order_down
            )
        );
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами пункты меню
            $query = "UPDATE
                          `menu`
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
                          `menu`
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
     * Функция поднимает пункт меню вверх
     */
    public function moveMenuItemUp($id) {
        $id_item_up = $id;
        // порядок следования пункта меню, который поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `menu`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id пункта меню, который находится выше и будет опущен вниз,
        // поменявшись местами с пунктом меню, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `menu`
                  WHERE
                      `parent` = :parent AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent' => $parent,
                'order_up' => $order_up
            )
        );
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами пункты меню
            $query = "UPDATE
                          `menu`
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
                          `menu`
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
     * Функция удаляет пункт меню (запись в таблице menu базы данных)
     */
    public function removeMenuItem($id) {
        // нельзя удалить пункт меню, у которого есть дочерние элементы
        $query = "SELECT 1 FROM `menu` WHERE `parent` = :id LIMIT 1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if (false === $res) {
            // получаем идентификатор родителя удаляемого пункта меню
            $query = "SELECT `parent` FROM `menu` WHERE `id` = :id";
            $parent = $this->database->fetchOne($query, array('id' => $id));
            // удаляем пункт меню
            $query = "DELETE FROM `menu` WHERE `id` = :id";
            $this->database->execute($query, array('id' => $id));
            // изменяем порядок сортировки пунктов меню, которые с удаленным на одном уровне
            $query = "SELECT `id` FROM `menu` WHERE `parent` = :parent ORDER BY `sortorder`";
            $childs = $this->database->fetchAll($query, array('parent' => $parent));
            if (count($childs) > 0) {
                $sortorder = 1;
                foreach ($childs as $child) {
                    $query = "UPDATE `menu` SET `sortorder` = :sortorder WHERE `id` = :id";
                    $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $child['id']));
                    $sortorder++;
                }
            }
        }
    }

}
