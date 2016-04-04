<?php
/**
 * Класс Vacancy_Backend_Model для работы с вакансиями, взаимодействует
 * с базой данных, административная часть сайта
 */
class Vacancy_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех вакансий компании
     */
    public function getAllVacancies() {
        $query = "SELECT
                      `id`, `name`, `visible`, `sortorder`
                  FROM
                      `vacancies`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $vacancies = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($vacancies as $key => $value) {
            $vacancies[$key]['url'] = array(
                'up'     => $this->getURL('backend/vacancy/moveup/id/' . $value['id']),
                'down'   => $this->getURL('backend/vacancy/movedown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/vacancy/edit/id/' . $value['id']),
                'remove' => $this->getURL('backend/vacancy/remove/id/' . $value['id'])
            );
        }
        return $vacancies;
    }

    /**
     * Возвращает информацию о вакансии с уникальным идентификатором $id
     */
    public function getVacancy($id) {
        $query = "SELECT
                      `id`, `name`, `details`, `visible`
                  FROM
                      `vacancies`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет вакансию (новую запись в таблицу vacancies базы данных)
     */
    public function addVacancy($data) {

        $data['details'] = serialize($data['details']);
        
        // порядок сортировки
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `vacancies`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query, array()) + 1;

        $query = "INSERT INTO `vacancies`
                  (
                      `name`,
                      `details`,
                      `visible`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :details,
                      :visible,
                      :sortorder
                  )";
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет вакансию (запись в таблице vacancies базы данных)
     */
    public function updateVacancy($data) {
        
        $data['details'] = serialize($data['details']);

        $query = "UPDATE
                      `vacancies`
                  SET
                      `name`    = :name,
                      `details` = :details,
                      `visible` = :visible
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

    }

    /**
     * Функция опускает вакансию вниз в списке
     */
    public function moveVacancyDown($id) {
        $id_item_down = $id;
        // порядок следования вакансии, которая опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `vacancies`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id вакансии, которая находится ниже и будет поднята вверх,
        // поменявшись местами с вакансией, которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `vacancies`
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
            // меняем местами вакансии
            $query = "UPDATE
                          `vacancies`
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
                          `vacancies`
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
     * Функция поднимает вакансию вверх в списке
     */
    public function moveVacancyUp($id) {
        $id_item_up = $id;
        // порядок следования вакансии, которая поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `vacancies`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id вакансии, которая находится выше и будет опущена вниз,
        // поменявшись местами с вакансией, которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `vacancies`
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
            // меняем местами вакансии
            $query = "UPDATE
                          `vacancies`
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
                          `vacancies`
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
     * Функция удаляет вакансию с уникальным идентификатором $id
     */
    public function removeVacancy($id) {
        // удаляем запись в таблице `vacancies` БД
        $query = "DELETE FROM `vacancies` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования вакансий
        $query = "SELECT
                      `id`
                  FROM
                      `vacancies`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $vacancies = $this->database->fetchAll($query);
        $sortorder = 1;
        foreach ($vacancies as $vacancy) {
            $query = "UPDATE
                          `vacancies`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $vacancy['id']));
            $sortorder++;
        }
    }

}
