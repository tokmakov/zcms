<?php
/**
 * Класс Add_Brand_Backend_Controller для добавления нового бренда, формирует страницу
 * с формой для добавления бренда, добавляет запись в таблицу БД brands, работает с
 * моделью Brand_Backend_Model, административная часть сайта
 */
class Add_Brand_Backend_Controller extends Brand_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы с
     * формой для добавления брэнда; в данном случае никаких данных получать не нужно
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Brand_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Add_Brand_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление бренда прошло успешно
                $this->redirect($this->brandBackendModel->getURL('backend/brand/index'));
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->brandBackendModel->getURL('backend/brand/add'));
            }
        }

        $this->title = 'Новый бренд. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->brandBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->brandBackendModel->getURL('backend/brand/index'), 'name' => 'Бренды'),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->brandBackendModel->getURL('backend/brand/add'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('addBrandForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addBrandForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addBrandForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет бренд и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']   = trim(utf8_substr($_POST['name'], 0, 100)); // наименование бренда
        $data['letter'] = $_POST['letter'];                          // первая буква бренда

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
            $this->setSessionData('addBrandForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового бренда
        $this->brandBackendModel->addBrand($data);

        return true;

    }

}