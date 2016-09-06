<?php
/**
 * Класс Forgot_User_Frontend_Controller формирует страницу с формой для
 * восстановления забытого пароля, получает данные от модели User_Frontend_Model,
 * общедоступная часть сайта
 */
class Forgot_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * восстановления забытого пароля
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Forgot_User_Frontend_Controller
         */
        parent::input();

        // если пользователь авторизован, здесь ему делать нечего
        if ($this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // если при заполнении формы не были допущены ошибки
                $this->setSessionData('successNewPassword', true);
            }
            $this->redirect($this->userFrontendModel->getURL('frontend/user/forgot'));
        }

        $this->title = 'Восстановление пароля. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Личный кабинет',
                'url'  => $this->userFrontendModel->getURL('frontend/user/index')
            )
        );

        $success = false;
        // если восстановление пароля прошло успешно
        if ($this->issetSessionData('successNewPassword')) {
            $success = true;
            $this->unsetSessionData('successNewPassword');
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->userFrontendModel->getURL('frontend/user/forgot'),
            // восстановление пароля прошло успешно?
            'success'     => $success,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('forgotPasswordForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('forgotPasswordForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('forgotPasswordForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция изменяет пароль пользователя, отправляет
     * письмо с новым паролем на e-mail пользователя и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['email']    = trim(utf8_substr($_POST['email'], 0, 64)); // электронная почта

        // были допущены ошибки при заполнении формы?
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail»';
        } elseif (!preg_match('#^[0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        } elseif (!$this->userFrontendModel->isUserExists($data['email'])) {
            $errorMessage[] = 'Пользователь с таким e-mail не зарегистрирован';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('forgotPasswordForm', $data);
            return false;
        }

        // обращаемся к модели для изменения пароля и отправки e-mail
        $this->userFrontendModel->newPassword($data['email']);

        return true;
    }
}
