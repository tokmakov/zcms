<?php
/**
 * Класс Edit_User_Frontend_Controller для редактирования личных данных (имя, пароль),
 * формирует страницу с формой для редактирования, обновляет запись в таблице БД
 * users, работает с моделью User_Frontend_Model, общедоступная часть сайта
 */
class Edit_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования личных данных пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Edit_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if (!$this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->ValidateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->userFrontendModel->getURL('frontend/user/edit'));
            } else { // ошибок не было, перенаправляем на главную страницу личного кабинета
                $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
            }
        }

        $this->title = 'Редактирование личных данных. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->userFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->userFrontendModel->getURL('frontend/user/index'), 'name' => 'Личный кабинет')
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action' => $this->userFrontendModel->getURL('frontend/user/edit'),
            // имя пользователя
            'name' => $this->user['name'],
            // фамилия пользователя
            'surname' => $this->user['surname'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('editUserForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editUserForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editUserForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет личные данные пользователя и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']     = trim(utf8_substr($_POST['name'], 0, 32));    // имя пользователя
        $data['name']     = preg_replace('#\s+#u', ' ', $data['name']);
        $data['surname']  = trim(utf8_substr($_POST['surname'], 0, 32)); // фамилия пользователя
        $data['change']   = false;
        if (isset($_POST['change'])) { // изменить пароль?
            $data['change']   = true;
            $data['password'] = trim(utf8_substr($_POST['password'], 0, 32)); // пароль
            $confirm          = trim(utf8_substr($_POST['confirm'], 0, 32));  // подтверждение пароля
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия»';
        } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $data['surname'])) {
            $errorMessage[] = 'Поле «Фамилия» содержит недопустимые символы';
        }
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя»';
        } elseif ( ! preg_match('#^[ a-zA-Zа-яА-ЯёЁ]+$#u', $data['name'])) {
            $errorMessage[] = 'Поле «Имя» содержит недопустимые символы';
        }
        if ($data['change']) { // изменить пароль?
            if (empty($data['password'])) {
                $errorMessage[] = 'Не заполнено поле «Пароль»';
            }
            if (empty($confirm)) {
                $errorMessage[] = 'Не заполнено поле «Подтвердите пароль»';
            }
            if ( (!empty($data['password'])) && (!empty($confirm)) && ($data['password'] != $confirm) ) {
                $errorMessage[] = 'Не совпадают пароли';
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editUserForm', $data);
            return false;
        }

        if ($data['change']) { // изменить пароль?
            // добавляем к паролю префикс и хэшируем пароль
            $data['password'] = md5($this->config->user->prefix . $data['password']);
        }

        // обращаемся к модели для обновления личных данных пользователя
        $this->userFrontendModel->updateUser($data);

        return true;
    }
}
