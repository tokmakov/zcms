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
     * формой для добавления бренда
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
            array(
                'name' => 'Главная',
                'url'  => $this->brandBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Бренды',
                'url'  => $this->brandBackendModel->getURL('backend/brand/index') 
            ),
        );
        
        // все буквы, для возможности выбора
        $letters = array(
            'A-Z' => $this->brandBackendModel->getLatinLetters(),
            'А-Я' => $this->brandBackendModel->getCyrillicLetters()
        );
        
        // все производители, для возможности выбора
        $makers = $this->brandBackendModel->getAllMakers();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->brandBackendModel->getURL('backend/brand/add'),
            // все буквы, для возможности выбора
            'letters'     => $letters,
            // все производители, для возможности выбора
            'makers'      => $makers,
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
         
        // наименование бренда
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 32));

        // первая буква бренда
        $data['letter'] = '';
        if (
            isset($_POST['letter'])
            &&
            (
                in_array($_POST['letter'], $this->brandBackendModel->getLatinLetters())
                ||
                in_array($_POST['letter'], $this->brandBackendModel->getCyrillicLetters())
            )
        ) {
            $data['letter'] = $_POST['letter'];
        }
        
        // популярный бренд?
        $data['popular'] = false;
        if (isset($_POST['popular'])) {
            $data['popular'] = true;
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['letter'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Буква»';
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