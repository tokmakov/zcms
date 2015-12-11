<?php
/**
 * Класс Login_User_Frontend_Controller формирует страницу с формой для
 * авторизации пользователя (если он не авторизован) или перенаправляет на
 * страницу личного кабинета, получает данные от модели User_Frontend_Model,
 * общедоступная часть сайта
 */
class Login_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);

        // добавляем наблюдателя за событием авторизации пользователя,
        // чтобы сразу после авторизации объеденить корзины (еще) не
        // авторизованного пользователя и (уже) авторизованного
        $this->userFrontendModel->attach($this->basketFrontendModel);

        // добавляем наблюдателя за событием авторизации пользователя, чтобы
        // сразу после авторизации объеденить списки отложенных товаров
        // (еще) не авторизованного пользователя и (уже) авторизованного
        $this->userFrontendModel->attach($this->wishedFrontendModel);

        // добавляем наблюдателя за событием авторизации пользователя, чтобы
        // сразу после авторизации объеденить списки товаров для сравнения
        // (еще) не авторизованного пользователя и (уже) авторизованного
        $this->userFrontendModel->attach($this->compareFrontendModel);

        // добавляем наблюдателя за событием авторизации пользователя, чтобы
        // сразу после авторизации объеденить списки просмотренных товаров
        // (еще) не авторизованного пользователя и (уже) авторизованного
        $this->userFrontendModel->attach($this->viewedFrontendModel);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для авторизации пользователя (в данном случае никаких данных
     * получать не надо)
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Login_User_Frontend_Controller
         */
        parent::input();

        // если пользователь уже авторизован, ему здесь делать нечего,
        //  перенаправляем его на страницу личного кабинета
        if ($this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // авторизация прошла успешено
                // перенаправляем пользователя на главную страницу личного кабинета
                $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
            } else { // при заполнении формы были допущены ошибки или неверный логин/пароль
                // перенаправляем пользователя опять на страницу с формой авторизации
                $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
            }
        }

        $this->title = 'Войти. ' . $this->title;

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
            'action' => $this->userFrontendModel->getURL('frontend/user/login'),
            // URL ссылки для регистрации на сайте
            'regUserUrl' => $this->userFrontendModel->getURL('frontend/user/reg'),
            // URL ссылки для восстановления пароля
            'forgotPasswordUrl' => $this->userFrontendModel->getURL('frontend/user/forgot'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('loginUserForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('loginUserForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('loginUserForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция пытается
     * авторизовать пользователя. Если пользователь с таким e-mail и паролем не
     * найден, функция возвращает false, иначе функция авторизует пользователя и
     * возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['email']    = trim(utf8_substr($_POST['email'], 0, 32));    // электронная почта
        $data['password'] = trim(utf8_substr($_POST['password'], 0, 32)); // пароль
        $data['remember'] = false;
        if (isset($_POST['remember'])) {
            $data['remember'] = true;
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail»';
        } elseif (!preg_match('#^[0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        }
        if (empty($data['password'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Пароль»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('loginUserForm', $data);
            return false;
        }

        // добавляем к паролю префикс и хэшируем пароль
        $data['password'] = md5($this->config->user->prefix . $data['password']);

        // обращаемся к модели для авторизации пользователя
        if (!$this->userFrontendModel->loginUser($data)) {
            // пользователь с таким e-mail и паролем не найден
            $data['errorMessage'] = array('Указан неверный логин или пароль');
            $this->setSessionData('loginUserForm', $data);
            return false;
        }

        return true;
    }

}
