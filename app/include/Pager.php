<?php
/**
 * Класс Pager отвечает за построение постраничной навигации при выводе
 * товаров, новостей, заказов и т.п.
 */
class Pager {

    /**
     * базовый URL, на основе которого создаются ссылки
     */
    private $baseURL;

    /**
     * номер текущей страницы
     */
    private $currentPage;

    /**
     * количество записей на одной странице
     */
    private $recordsPerPage;

    /**
     * количество ссылок слева и справа
     */
    private $leftRightLinks;

    /**
     * общее количество записей
     */
    private $totalRecords;

    /**
     * общее количество страниц
     */
    private $totalPages;


    /**
     * Конструктор класса
     */
    public function __construct($baseURL, $currentPage, $totalRecords, $recordsPerPage = 10, $leftRightLinks = 2) {
        $this->baseURL        = $baseURL;        // базовый URL
        $this->currentPage    = $currentPage;    // номер текущей страницы
        $this->totalRecords   = $totalRecords;   // общее количество записей
        $this->recordsPerPage = $recordsPerPage; // количество записей на одной странице
        $this->leftRightLinks = $leftRightLinks; // количество ссылок слева и справа

        $this->totalPages = 1; // общее количество страниц
        if ($this->totalRecords) {
            $this->totalPages = ceil($this->totalRecords/$this->recordsPerPage);
        }
    }

    /**
     * Возвращает массив элементов постраничной навигации
     */
    public function getNavigation() {

        if ($this->currentPage > $this->totalPages) { // недопустимое значение $currentPage
            return false;
        }
        if ($this->totalRecords == 0) { // постраничная навигация не нужна
            return array();
        }
        if ($this->totalRecords <= $this->recordsPerPage) { // постраничная навигация не нужна
            return array();
        }

        $result = array(); // массив элементов постраничной навигации

        // ссылки на первую и предыдущую страницу
        if ($this->currentPage != 1) {
            $result['first'] = array(
                'num' => 1,
                'url' => $this->baseURL
            );
            $url = ($this->currentPage-1 == 1) ? $this->baseURL : $this->baseURL . '/page/' . ($this->currentPage - 1);
            $result['prev'] = array(
                'num' => $this->currentPage - 1,
                'url' => $url
            );
        }

        // ссылка на текущую страницу
        $url = ($this->currentPage == 1) ? $this->baseURL : $this->baseURL . '/page/' . $this->currentPage;
        $result['current'] = array(
            'num' => $this->currentPage,
            'url' => $url
        );

        // ссылки на последнюю и следующую страницу
        if ($this->currentPage < $this->totalPages) {
            $result['last'] = array(
                'num' => $this->totalPages,
                'url' => $this->baseURL . '/page/' . $this->totalPages
            );
            $result['next'] = array(
                'num' => $this->currentPage + 1,
                'url' => $this->baseURL . '/page/' . ($this->currentPage + 1)
            );
        }

        // ссылки на несколько предыдущих страниц
        $temp = array();
        if ($this->currentPage > $this->leftRightLinks + 1) {
            for ($i = $this->currentPage - $this->leftRightLinks; $i < $this->currentPage; $i++) {
                $url = ($i == 1) ? $this->baseURL : $this->baseURL . '/page/' . $i;
                $temp[] = array(
                    'num' => $i,
                    'url' => $url
                );
            }
        } else {
            for ($i = 1; $i < $this->currentPage; $i++) {
                $url = ($i == 1) ? $this->baseURL : $this->baseURL . '/page/' . $i;
                $temp[] = array(
                    'num' => $i,
                    'url' => $url
                );
            }
        }
        $result['left'] = $temp;

        // ссылки на несколько следующих страниц
        $temp = array();
        if ($this->currentPage + $this->leftRightLinks < $this->totalPages) {
            for ($i = $this->currentPage + 1; $i <= $this->currentPage + $this->leftRightLinks; $i++) {
                $temp[] = array(
                    'num' => $i,
                    'url' => $this->baseURL . '/page/' . $i
                );
            }
        } else {
            for ($i = $this->currentPage + 1; $i <= $this->totalPages; $i++) {
                $temp[] = array(
                    'num' => $i,
                    'url' => $this->baseURL . '/page/' . $i
                );
            }
        }
        $result['right'] = $temp;

        return $result;
    }
}