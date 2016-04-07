<?php
/**
 * Класс Sitemap_Backend_Model для работы с картой сайта, взаимодействует
 * с базой данных, административная часть сайта
 */
class Sitemap_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает данные об элементе карты сайта с уникальным идентификатором $id
     */
    public function getSitemapItem($id) {
        $query = "SELECT
                      `name`, `capurl`, `parent`
                  FROM
                      `sitemap`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция возвращает массив всех элементов карты сайта в виде дерева,
     * для контроллера, отвечающего за вывод всех элементов карты сайта
     */
    public function getAllSitemapItems() {
        // получаем все элементы карты сайта
        $query = "SELECT
                      `id`, `name`, `parent`, `sortorder`
                  FROM
                      `sitemap`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования, удаления, перемещения вверх и вниз
        foreach($data as $key => $value) {
            $data[$key]['url'] = array(
                'up'     => $this->getURL('backend/sitemap/itemup/id/' . $value['id']),
                'down'   => $this->getURL('backend/sitemap/itemdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/sitemap/edititem/id/' . $value['id']),
                'remove' => $this->getURL('backend/sitemap/rmvitem/id/' . $value['id'])
            );
        }
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает массив элементов карты сайта двух верхних уровней в виде дерева,
     * для контроллеров, отвечающих за добавление и редактирование отдельного элемента
     */
    public function getSitemapItems() {
        $query = "SELECT
                      `id`, `name`, `capurl`, `parent`
                  FROM
                      `sitemap`
                  WHERE
                      `parent` = 0 OR `parent` IN (SELECT `id` FROM `sitemap` WHERE `parent` = 0)
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Функция возвращает массив всех страниц сайта в виде дерева для контроллеров,
     * отвечающих за добавление/редактирование элемента карты сайта
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
     * отвечающих за добавление/редактирование элемента карты сайта
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
     * Возвращает массив категорий блога, для контроллеров, отвечающих
     * за добавление/редактирование элемента карты сайта
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
     * отвечающих за добавление/редактирование элемента карты сайта
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
     * Функция добавляет новый элемент карты сайта
     */
    public function addSitemapItem($data) {
        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `sitemap`
                  WHERE
                      `parent` = :parent";
        $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;

        $query = "INSERT INTO `sitemap`
                  (
                      `name`,
                      `capurl`,
                      `parent`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :capurl,
                      :parent,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет элемент карты сайта
     */
    public function updateSitemapItem($data) {
        // получаем идентификатор родителя обновляемого элемента карты сайта
        $oldParent = $this->getSitemapItemParent($data['id']);
        // если был изменен родитель обновляемого элемента карты сайта
        if ($oldParent != $data['parent']) {
            // добавляем обновляемый элемент карты сайта в конец списка дочерних элементов нового родителя
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `sitemap`
                      WHERE
                          `parent` = :parent";
            $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
            $query = "UPDATE
                          `sitemap`
                      SET
                          `name`      = :name,
                          `capurl`    = :capurl,
                          `parent`    = :parent,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, $data);
            // изменяем порядок сортировки элементов карты сайта, которые были с обновленным элементом
            // на одном уровне до того, как он поменял родителя
            $query = "SELECT
                          `id`
                      FROM
                          `sitemap`
                      WHERE
                          `parent` = :parent
                      ORDER BY
                          `sortorder`";
            $childs = $this->database->fetchAll($query, array('parent' => $oldParent));
            $sortorder = 1;
            foreach ($childs as $child) {
                $query = "UPDATE
                              `sitemap`
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
                          `sitemap`
                      SET
                          `name`   = :name,
                          `capurl` = :capurl
                      WHERE
                          `id` = :id";
            $this->database->execute($query, $data);
        }
    }

    /**
     * Функция возвращает массив идентификаторов дочерних элементов (прямых потомков)
     * элемента карты сайта с уникальным идентификатором $id
     */
    public function getChildItems($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `sitemap`
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
     * дочерних) элемента карты сайта с уникальным идентификатором $id
     */
    public function getAllChildItems($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `sitemap`
                  WHERE
                      `parent` = :parent1 OR `parent` IN (SELECT `id` FROM `sitemap` WHERE `parent` = :parent2)";
        return $this->database->fetchAll($query, array('parent1' => $id, 'parent2' => $id));
    }

    /**
     * Функция возвращает идентификатор родителя элемента карты сайта
     * с уникальным идентификатором $id
     */
    public function getSitemapItemParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `sitemap`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция опускает элемент карты сайта вниз
     */
    public function moveSitemapItemDown($id) {
        $id_item_down = $id;
        // порядок следования элемента карты сайта, который опускается вниз
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `sitemap`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id элемента карты сайта, который находится ниже и будет поднят вверх,
        // поменявшись местами с элементом карты сайта, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sitemap`
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
            // меняем местами элементы карты сайта
            $query = "UPDATE
                          `sitemap`
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
                          `sitemap`
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
     * Функция поднимает элемент карты сайта вверх
     */
    public function moveSitemapItemUp($id) {
        $id_item_up = $id;
        // порядок следования элемента карты сайта, который поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `sitemap`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['parent'];
        // порядок следования и id элемента карты сайта, который находится выше и будет опущен вниз,
        // поменявшись местами с элементом карты сайта, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sitemap`
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
            // меняем местами элементы карты сайта
            $query = "UPDATE
                          `sitemap`
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
                          `sitemap`
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
     * Функция удаляет элемент карты сайта
     */
    public function removeSitemapItem($id) {
        // нельзя удалить элемент карты сайта, у которого есть дочерние элементы
        $query = "SELECT 1 FROM `sitemap` WHERE `parent` = :id LIMIT 1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if (false === $res) {
            // получаем идентификатор родителя удаляемого элемента карты сайта
            $query = "SELECT `parent` FROM `sitemap` WHERE `id` = :id";
            $parent = $this->database->fetchOne($query, array('id' => $id));
            // удаляем элемент карты сайта
            $query = "DELETE FROM `sitemap` WHERE `id` = :id";
            $this->database->execute($query, array('id' => $id));
            // изменяем порядок сортировки элементов карты сайта, которые с удаленным на одном уровне
            $query = "SELECT `id` FROM `sitemap` WHERE `parent` = :parent ORDER BY `sortorder`";
            $childs = $this->database->fetchAll($query, array('parent' => $parent));
            if (count($childs) > 0) {
                $sortorder = 1;
                foreach ($childs as $child) {
                    $query = "UPDATE `sitemap` SET `sortorder` = :sortorder WHERE `id` = :id";
                    $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $child['id']));
                    $sortorder++;
                }
            }
        }
    }

}
