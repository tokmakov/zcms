<?php
/**
 * Класс Partner_Frontend_Model для работы с партнерами компании,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Partner_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех партнеров компании; результат
     * работы кэшируется
     */
    public function getAllPartners() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->allPartners();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()';
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив всех партнеров компании
     */
    protected function allPartners() {

        $query = "SELECT
                      `id`, `name`, `image`, `alttext`, `sortorder`
                  FROM
                      `partners`
                  WHERE
                      `image` <> ''
                  ORDER BY
                      `sortorder`";
        $partners = $this->database->fetchAll($query);
        // добавляем в массив URL картинки сертификата
        foreach($partners as $key => $value) {
            $partners[$key]['url']['image'] = $this->config->site->url . 'files/partner/images/' . $value['image'] . '.jpg';
            $partners[$key]['url']['thumb'] = $this->config->site->url . 'files/partner/thumbs/' . $value['image'] . '.jpg';
        }
        return $partners;

    }

}