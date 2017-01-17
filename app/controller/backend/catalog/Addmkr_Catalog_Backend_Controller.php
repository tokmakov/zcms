<?php
/**
 * Класс Addmkr_Catalog_Backend_Controller для добавления нового производителя,
 * формирует страницу с формой для добавления производителя, добавляет запись в
 * таблицу БД makers, работает с моделью Catalog_Backend_Model, административная
 * часть сайта
 */
class Addmkr_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления производителя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addmkr_Catalog_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление производителя прошло успешно
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/allmkrs'));
            } else { // при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/addmkr'));
            }
        }

        $this->title = 'Новый производитель. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->catalogBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->catalogBackendModel->getURL('backend/catalog/index')
            ),
            array(
                'name' => 'Производители',
                'url'  => $this->catalogBackendModel->getURL('backend/catalog/allmkrs')
            ),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action' => $this->catalogBackendModel->getURL('backend/catalog/addmkr')
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addCatalogMakerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addCatalogMakerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addCatalogMakerForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет производителя и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 64)); // наименование
        $data['altname']     = trim(iconv_substr($_POST['altname'], 0, 64)); // альтернативное наименование
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // описание производителя

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addCatalogMakerForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового производителя
        $this->catalogBackendModel->addMaker($data);

        return true;

    }

}