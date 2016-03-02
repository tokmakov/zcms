<?php
/**
 * Класс Addprof_User_Frontend_Controller формирует страницу с формой для добавления
 * нового профиля, добавляет запись в таблицу БД profiles, получает данные от модели
 * User_Frontend_Model, общедоступная часть сайта
 */
class Addprof_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления нового профиля пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addprof_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if ( ! $this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->userFrontendModel->getURL('frontend/user/addprof'));
            } else { // ошибок не было, профиль добавлен, перенаправляем пользователя на страницу со списком прфилей
                $this->redirect($this->userFrontendModel->getURL('frontend/user/allprof'));
            }
        }

        $this->title = 'Новый профиль. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Личный кабинет',
                'url'  => $this->userFrontendModel->getURL('frontend/user/index')
            ),
            array(
                'name' => 'Ваши профили',
                'url'  => $this->userFrontendModel->getURL('frontend/user/allprof')
            ),
        );
        
        // если пользователь авторизован, получаем информацию о нем
        $surname    = ''; // фамилия контактного лица по умолчанию
        $name       = ''; // имя контактного лица по-умолчанию
        $patronymic = ''; // отчество контактного лица по-умолчанию
        $email      = ''; // электронная почта контактного лица по умолчанию
        if ($this->authUser) {
            $user       = $this->userFrontendModel->getUser();
            $surname    = $user['surname'];
            $name       = $user['name'];
            $patronymic = $user['patronymic'];
            $email      = $user['email'];
        }

        // получаем от модели список офисов для самовывоза товара со склада
        $offices = $this->userFrontendModel->getOffices();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->userFrontendModel->getURL('frontend/user/addprof'),
            // фамилия контактного лица по умолчанию
            'surname'     => $surname,
            // имя контактного лица по-умолчанию
            'name'        => $name,
            // отчество контактного лица по-умолчанию
            'patronymic'  => $patronymic,
            // электронная почта контактного лица по умолчанию
            'email'       => $email,
            // массив офисов для самовывоза
            'offices'     => $offices,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('addUserProfileForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addUserProfileForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addUserProfileForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет новый профиль и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['title']      = trim(utf8_substr(strip_tags($_POST['title']), 0, 32));      // название профиля
        $data['surname']    = trim(utf8_substr(strip_tags($_POST['surname']), 0, 32));    // фамилия контактного лица
        $data['name']       = trim(utf8_substr(strip_tags($_POST['name']), 0, 16));       // имя контактного лица
        $data['patronymic'] = trim(utf8_substr(strip_tags($_POST['patronymic']), 0, 16)); // отчество контактного лица
        $data['phone']      = trim(utf8_substr(strip_tags($_POST['phone']), 0, 32));      // телефон контактного лица
        $data['email']      = trim(utf8_substr(strip_tags($_POST['email']), 0, 32));      // e-mail контактного лица

        if (isset($_POST['shipping'])) { // самовывоз со склада
            $data['shipping']         = 1;
            if (isset($_POST['office']) && in_array($_POST['office'], array(1,2,3,4))) {
                $data['shipping'] = $_POST['office'];
            }
            $data['shipping_address'] = '';
            $data['shipping_city']    = '';
            $data['shipping_index']   = '';
        } else { // доставка по адресу
            $data['shipping']         = 0;
            $data['shipping_address'] = trim(utf8_substr(strip_tags($_POST['shipping_address']), 0, 250)); // адрес доставки
            $data['shipping_city']    = trim(utf8_substr(strip_tags($_POST['shipping_city']), 0, 32));     // город доставки
            $data['shipping_index']   = trim(utf8_substr(strip_tags($_POST['shipping_index']), 0, 6));     // почтовый индекс
        }

        $data['company']         = 0;
        $data['company_name']    = '';
        $data['company_ceo']     = '';
        $data['company_address'] = '';
        $data['company_inn']     = '';
        $data['company_kpp']     = '';
        $data['bank_name']       = '';
        $data['bank_bik']        = '';
        $data['settl_acc']       = '';
        $data['corr_acc']        = '';
 
        if (isset($_POST['company'])) { // юридическое лицо?
            $data['company']         = 1;
            $data['company_name']    = trim(utf8_substr(strip_tags($_POST['company_name']), 0, 64));     // название компании
            $data['company_ceo']     = trim(utf8_substr(strip_tags($_POST['company_ceo']), 0, 64));      // генеральный директор
            $data['company_address'] = trim(utf8_substr(strip_tags($_POST['company_address']), 0, 250)); // юридический адрес
            $data['company_inn']     = trim(utf8_substr(strip_tags($_POST['company_inn']), 0, 12));      // ИНН компании
            $data['company_kpp']     = trim(utf8_substr(strip_tags($_POST['company_kpp']), 0, 9));       // КПП компании
            $data['bank_name']       = trim(utf8_substr(strip_tags($_POST['bank_name']), 0, 64));        // название банка
            $data['bank_bik']        = trim(utf8_substr(strip_tags($_POST['bank_bik']), 0, 9));          // БИК банка
            $data['settl_acc']       = trim(utf8_substr(strip_tags($_POST['settl_acc']), 0, 20));        // номер расчетного счета в банке
            $data['corr_acc']        = trim(utf8_substr(strip_tags($_POST['corr_acc']), 0, 20));         // номер корреспондентского счета
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['title'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название профиля»';
        }
        if ($data['company']) { // для юридического лица
            if (empty($data['company_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название компании»';
            }
            if (empty($data['company_ceo'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор»';
            }
            if (empty($data['company_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес»';
            }
            if (empty($data['company_inn'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «ИНН»';
            } elseif ( ! preg_match('#^(\d{10}|\d{12})$#i', $data['company_inn'])) {
                $errorMessage[] = 'Поле «ИНН» должно содержать 10 или 12 цифр';
            }
            if ( ! empty($data['company_kpp'])) {
                if ( ! preg_match('#^\d{9}$#i', $data['company_kpp'])) {
                    $errorMessage[] = 'Поле «КПП» должно содержать 9 цифр';
                }
            }
            if (empty($data['bank_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название банка»';
            }
            if (empty($data['bank_bik'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «БИК банка»';
            } elseif ( ! preg_match('#^\d{9}$#i', $data['bank_bik'])) {
                $errorMessage[] = 'Поле «БИК банка» должно содержать 9 цифр';
            }
            if (empty($data['settl_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет»';
            } elseif ( ! preg_match('#^\d{20}$#i', $data['settl_acc'])) {
                $errorMessage[] = 'Поле «Расчетный счет» должно содержать 20 цифр';
            }
            if (empty($data['corr_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет»';
            } elseif ( ! preg_match('#^\d{20}$#i', $data['corr_acc'])) {
                $errorMessage[] = 'Поле «Корреспондентский счет» должно содержать 20 цифр';
            }
        }
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица»';
        }
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица»';
        }
        if (empty($data['phone'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Телефон контактного лица»';
        }
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица»';
        } elseif ( ! preg_match('#^[0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        }
        if ( ! $data['shipping']) {
            if (empty($data['shipping_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
            if ( ! empty($data['shipping_index'])) {
                if ( ! preg_match('#^\d{6}$#i', $data['shipping_index'])) {
                    $errorMessage[] = 'Поле «Почтовый индекс» должно содержать 6 цифр';
                }
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addUserProfileForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового профиля
        $this->userFrontendModel->addProfile($data);

        return true;
    }

}
