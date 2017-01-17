<?php
/**
 * Класс Editmkr_Catalog_Backend_Controller для редактирования производителя, формирует
 * страницу с формой для редактирования производителя, обновляет запись в таблице БД makers,
 * работает с моделью Catalog_Backend_Model, административная часть сайта
 */
class Editmkr_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования производителя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editmkr_Catalog_Backend_Controller
         */
        parent::input();

        // если не передан id производителя или id производителя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление производителя прошло успешно
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/allmkrs'));
            } else { // при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/editmkr/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование производителя. ' . $this->title;

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

        // получаем от модели информацию о производителе
        $maker = $this->catalogBackendModel->getMaker($this->params['id']);
        // если запрошенный производитель не найден в БД
        if (empty($maker)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/editmkr/id/' . $this->params['id']),
            // уникальный идентификатор производителя
            'id'          => $this->params['id'],
            // наименование производителя
            'name'        => $maker['name'],
            // альтернативное наименование производителя
            'altname'     => $maker['altname'],
            // мета-тег keywords
            'keywords'    => $maker['keywords'],
            // мета-тег description
            'description' => $maker['description'],
            // описание производителя
            'body'        => $maker['body'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editCatalogMakerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editCatalogMakerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editCatalogMakerForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет производителя и возвращает true
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
         * заполненную введенными ранее данными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editCatalogMakerForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор производителя

        // обращаемся к модели для обновления производителя
        $this->catalogBackendModel->updateMaker($data);

        return true;
    }

}