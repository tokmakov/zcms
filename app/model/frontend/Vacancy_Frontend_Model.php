<?php
/**
 * Класс Vacancy_Frontend_Model для работы с вакансиями компании,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Vacancy_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает массив всех вакансий компании
     */
    public function getAllVacancies() {
        $query = "SELECT
                      `name`, `details`
                  FROM
                      `vacancies`
                  WHERE
                      `visible` = 1
                  ORDER BY
                      `sortorder`";
        $vacancies = $this->database->fetchAll($query);
        // добавляем в массив URL картинки сертификата
        foreach($vacancies as $key => $value) {
            $vacancies[$key]['details'] = unserialize($value['details']);
        }
        return $vacancies;
    }

}