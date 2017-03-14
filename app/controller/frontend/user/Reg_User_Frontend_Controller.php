<?php
/**
 * Класс Reg_User_Frontend_Controller для регистрации на сайте нового пользователя,
 * формирует страницу с формой для регистрации, добавляет запись в таблицу БД
 * users, работает с моделью User_Frontend_Model, общедоступная часть сайта
 */
class Reg_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для регистрации нового пользователя (в данном конкретном случае
     * от модели получать ничего не нужно)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Reg_User_Frontend_Controller
         */
        parent::input();

        // если пользователь авторизован, ему здесь делать нечего
        if ($this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->userFrontendModel->getURL('frontend/user/reg'));
            } else { // ошибок не было, регистрация прошла успешно
                $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
            }
        }

        $this->title = 'Регистрация. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index'),
            ),
            array(
                'name' => 'Личный кабинет',
                'url'  => $this->userFrontendModel->getURL('frontend/user/index'),
            )
        );

        // проверка на робота
        $number1 = rand(1, 5);
        $number2 = rand(1, 4);
        $this->setSessionData('checkRobot', $number1 + $number2);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->userFrontendModel->getURL('frontend/user/reg'),
            // проверка на робота
            'numbers'     => array($number1, $number2),
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
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * добавляет нового пользователя и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['surname']    = trim(iconv_substr($_POST['surname'], 0, 32));    // фамилия пользователя
        $data['name']       = trim(iconv_substr($_POST['name'], 0, 32));       // имя пользователя
        $data['patronymic'] = trim(iconv_substr($_POST['patronymic'], 0, 32)); // отчество пользователя
        $data['email']      = trim(iconv_substr($_POST['email'], 0, 64));      // электронная почта
        $data['password']   = trim(iconv_substr($_POST['password'], 0, 32));   // пароль
        $confirm            = trim(iconv_substr($_POST['confirm'], 0, 32));    // подтверждение пароля

        $answer = 0; // проверка на робота
        if (isset($_POST['answer']) && in_array($_POST['answer'], array(1,2,3,4,5,6,7,8,9))) {
            $answer = (int)$_POST['answer'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия»';
        } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $data['surname'])) {
            $errorMessage[] = 'Поле «Фамилия» содержит недопустимые символы';
        }
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя»';
        } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $data['name'])) {
            $errorMessage[] = 'Поле «Имя» содержит недопустимые символы';
        }
        if ( ! empty($data['patronymic'])) {
            if ( ! preg_match('#^[a-zA-Zа-яА-ЯёЁ]+$#u', $data['patronymic'])) {
                $errorMessage[] = 'Поле «Отчество» содержит недопустимые символы';
            }
        }
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail»';
        } elseif ( ! preg_match('#^[_0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*[0-9a-z]\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        } elseif ($this->userFrontendModel->isUserExists($data['email'])) {
            $errorMessage[] = 'Пользователь с таким e-mail уже зарегистрирован';
        }
        if (empty($data['password'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Пароль»';
        }
        if (empty($confirm)) {
            $errorMessage[] = 'Не заполнено обязательное поле «Пароль еще раз»';
        }
        if ( ( ! empty($data['password'])) && ( ! empty($confirm)) && ($data['password'] != $confirm) ) {
            $errorMessage[] = 'Не совпадают пароли';
        }
        if (empty($answer)) {
            $errorMessage[] = 'Выберите из списка ответ на вопрос';
        } elseif ($answer != $this->getSessionData('checkRobot')) {
            $errorMessage[] = 'Неправильный ответ на вопрос';
        }

        if ($this->issetSessionData('checkRobot')) {
            $this->unsetSessionData('checkRobot');
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addNewUserForm', $data);
            return false;
        }

        // добавляем к паролю префикс и хэшируем пароль
        $data['password'] = md5($this->config->user->prefix . $data['password']);

        // обращаемся к модели для добавления нового пользователя
        $this->userFrontendModel->addNewUser($data);

        return true;

    }
}
