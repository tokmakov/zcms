<?php
/**
 * Класс Pager отвечает за построение постраничной навигации при выводе
 * товаров, новостей, закозов и т.п.
 */
class Pager {

    /**
     * номер текущей страницы
     */
    protected $currentPage;

    /**
     * количество записей на одной странице
     */
    protected $recordsPerPage;

    /**
     * количество ссылок слева и справа
     */
    protected $leftRightLinks;

    /**
     * общее количество записей
     */
    protected $totalRecords;

    /**
     * общее количество страниц
     */
    protected $totalPages;


    /**
     * Конструктор класса
     */
    public function __construct($currentPage, $totalRecords, $recordsPerPage = 10, $leftRightLinks = 2) {
        $this->currentPage = $currentPage; // номер текущей страницы
        $this->totalRecords = $totalRecords; // общее количество записей
        $this->recordsPerPage = $recordsPerPage; // количество записей на одной странице
        $this->leftRightLinks = $leftRightLinks; // количество ссылок слева и справа

        $this->totalPages = ceil($this->totalRecords/$this->recordsPerPage); // общее количество страниц
    }

    /**
     * Возвращает массив элементов постраничной навигации
     */
    public function getNavigation() {

        if ($this->totalRecords == 0) { // постраничная навигация не нужна
            return false;
        }
        if ($this->totalRecords <= $this->recordsPerPage) { // постраничная навигация не нужна
            return false;
        }
        if ($this->currentPage > $this->totalPages) { // недопустимое значение $this->currentPage
            return null;
        }

        $result = array(); // массив элементов постраничной навигации

        // ссылки на первую и предыдущую страницу
        if ($this->currentPage != 1) {
            $result['first'] = 1;
            $result['prev'] = $this->currentPage - 1;
        }

        // ссылка на текущую страницу
        $result['current'] = $this->currentPage;

        // ссылки на последнюю и следующую страницу
        if ($this->currentPage < $this->totalPages) {
            $result['next'] = $this->currentPage + 1;
            $result['last'] = $this->totalPages;
        }

        // ссылки на несколько предыдущих страниц
        if ($this->currentPage > $this->leftRightLinks + 1) {
            for ($i = $this->currentPage - $this->leftRightLinks; $i < $this->currentPage; $i++) {
                $result['left'][] = $i;
            }
        } else {
            for ($i = 1; $i < $this->currentPage; $i++) {
                $result['left'][] = $i;
            }
        }

        // ссылки на несколько следующих страниц
        if ($this->currentPage + $this->leftRightLinks < $this->totalPages) {
            for ($i = $this->currentPage + 1; $i <= $this->currentPage + $this->leftRightLinks; $i++) {
                $result['right'][] = $i;
            }
        } else {
            for ($i = $this->currentPage + 1; $i <= $this->totalPages; $i++) {
                $result['right'][] = $i;
            }
        }

        return $result;
    }
}