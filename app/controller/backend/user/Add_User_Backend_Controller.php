<?php
/**
 * Класс Add_User_Backend_Controller формирует страницу с формой для добавления
 * нового пользователя, добавляет новую запись в таблицу БД users, работает с
 * моделью User_Backend_Model, административная часть сайта
 */
class Add_User_Backend_Controller extends User_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления нового пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Add_User_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->ValidateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->userBackendModel->getURL('backend/user/add'));
            } else {
                $this->redirect($this->userBackendModel->getURL('backend/user/index'));
            }
        }

        $this->title = 'Новый пользователь. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Пользователи',
                'url'  => $this->userBackendModel->getURL('backend/user/index')
            ),
        );

        // типы пользователей, для возможности выбора
        $types = $this->userBackendModel->getUserTypes();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->userBackendModel->getURL('backend/user/add'),
            // типы пользователей, для возможности выбора
            'types'       => $types,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('addNewUserForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addNewUserForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addNewUserForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет пользователя и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['surname']    = trim(iconv_substr($_POST['surname'], 0, 32));    // фамилия пользователя
        $data['name']       = trim(iconv_substr($_POST['name'], 0, 32));       // имя пользователя
        $data['patronymic'] = trim(iconv_substr($_POST['patronymic'], 0, 32)); // отчество пользователя
        $data['email']      = trim(iconv_substr($_POST['email'], 0, 64));      // электронная почта
        $data['password']   = trim(iconv_substr($_POST['password'], 0, 32));   // пароль
        $confirm            = trim(iconv_substr($_POST['confirm'], 0, 32));    // подтверждение пароля

        $data['type'] = (int)$_POST['type']; // тип пользователя

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя»';
        }
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия»';
        }
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail»';
        } elseif ( ! preg_match('#^[0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        } elseif ($this->userBackendModel->isUserExists($data['email'])) {
            $errorMessage[] = 'Пользователь с таким e-mail уже зарегистрирован';
        }
        if (empty($data['password'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Пароль»';
        }
        if (empty($confirm)) {
            $errorMessage[] = 'Не заполнено обязательное поле «Подтвердите пароль»';
        }
        if ( (!empty($data['password'])) && (!empty($confirm)) && ($data['password'] != $confirm) ) {
            $errorMessage[] = 'Не совпадают пароли';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addNewUserForm', $data);
            return false;
        }

        // добавляем к паролю префикс и хэшируем пароль
        $data['password'] = md5($this->config->user->prefix . $data['password']);

        // обращаемся к модели для обновления личных данных пользователя
        $this->userBackendModel->addNewUser($data);

        return true;
    }
}
