<?php
/**
 * Класс Banner_Backend_Model для работы с баннерами, взаимодействует
 * с базой данных, административная часть сайта
 */
class Banner_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех баннеров
     */
    public function getAllBanners() {
        $query = "SELECT
                      `id`, `name`, DATE_FORMAT(`added`, '%d.%m.%Y') AS `date`,
                      `visible`, `sortorder`
                  FROM
                      `banners`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $banners = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($banners as $key => $value) {
            $banners[$key]['url'] = array(
                'up'     => $this->getURL('backend/banner/moveup/id/' . $value['id']),
                'down'   => $this->getURL('backend/banner/movedown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/banner/edit/id/' . $value['id']),
                'remove' => $this->getURL('backend/banner/remove/id/' . $value['id'])
            );
        }
        return $banners;
    }

    /**
     * Возвращает информацию о баннере с уникальным идентификатором $id
     */
    public function getBanner($id) {
        $query = "SELECT
                      `id`, `name`, `url`, `alttext`, `added`, `visible`, `sortorder`
                  FROM
                      `banners`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет беннер (новую запись в таблицу banners базы данных)
     */
    public function addBanner($data) {

        // порядок сортировки
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `banners`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query, array()) + 1;

        $query = "INSERT INTO `banners`
                  (
                      `name`,
                      `url`,
                      `alttext`,
                      `added`,
                      `visible`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :url,
                      :alttext,
                      NOW(),
                      :visible,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

    }

    /**
     * Функция обновляет баннер (запись в таблице banners базы данных)
     */
    public function updateBanner($data) {

        $query = "UPDATE
                      `banners`
                  SET
                      `name`    = :name,
                      `url`     = :url,
                      `alttext` = :alttext,
                      `visible` = :visible
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

    }

    /**
     * Функция загружает файл изображения для баннера с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/banner/' . $id . '.jpg')) {
                unlink('files/banner/' . $id . '.jpg');
            }
        }

        // проверяем, пришел ли файл изображения
        if (!empty($_FILES['image']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if ($_FILES['image']['error'] == 0) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['image']['type'], $mimetypes)) {
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['image']['tmp_name'],
                        'files/banner/'. $id . '.jpg',
                        250,
                        0,
                        'jpg'
                    );
                }
            }
        }
    }

    /**
     * Функция опускает баннер вниз в списке
     */
    public function moveBannerDown($id) {
        $id_item_down = $id;
        // порядок следования баннера, который опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `banners`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id баннера, который находится ниже и будет поднят вверх,
        // поменявшись местами с баннером, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `banners`
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
            // меняем местами баннеры
            $query = "UPDATE
                          `banners`
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
                          `banners`
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
     * Функция поднимает баннер вверх в списке
     */
    public function moveBannerUp($id) {
        $id_item_up = $id;
        // порядок следования баннера, который поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `banners`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id баннера, который находится выше и будет опущен вниз,
        // поменявшись местами с баннером, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `banners`
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
            // меняем местами баннеры
            $query = "UPDATE
                          `banners`
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
                          `banners`
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
     * Функция для удаления баннера с уникальным идентификатором $id
     */
    public function removeBanner($id) {
        // удаляем запись в таблице `banners` БД
        $query = "DELETE FROM `banners` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файл изображения
        if (is_file('files/banner/' . $id . '.jpg')) {
            unlink('files/banner/' . $id . '.jpg');
        }
        // обновляем порядок следования баннеров
        $query = "SELECT
                      `id`
                  FROM
                      `banners`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $banners = $this->database->fetchAll($query, array());
        $sortorder = 1;
        foreach ($banners as $banner) {
            $query = "UPDATE
                          `banners`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $banner['id']));
            $sortorder++;
        }
    }

}
