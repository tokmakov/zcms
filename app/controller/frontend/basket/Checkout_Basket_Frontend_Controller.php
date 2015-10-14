<?php
/**
 * Класс Checkout_Basket_Frontend_Controller формирует страницу с формой
 * для оформления заказа, получает данные от модели Basket_Frontend_Model,
 * общедоступная часть сайта
 */
class Checkout_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для оформления заказа
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Basket_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Checkout_Basket_Frontend_Controller
         */
        parent::input();

        // если корзина пуста, здесь делать нечего, за исключением того
        // случая, когда заказ только что был размещен
        if ($this->basketFrontendModel->isEmptyBasket() && (!$this->issetSessionData('successCheckoutOrder'))) {
            // перенаправляем пользователя на страницу корзины
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // заказ был создан, отмечаем этот факт
                $this->setSessionData('successCheckoutOrder', true);
            }
            // перенаправляем пользователя опять на страницу оформления заказа,
            // где он увидит либо форму с сообщениями об ошибках, допущенных при
            // заполнении формы, либо сообщение «Ваш заказ успешно создан»
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/checkout'));
        }

        $this->title = 'Оформление заказа. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->basketFrontendModel->getURL('frontend/index/index'), 'name' => 'Главная'),
            array('url' => $this->basketFrontendModel->getURL('frontend/catalog/index'), 'name' => 'Каталог'),
            array('url' => $this->basketFrontendModel->getURL('frontend/basket/index'), 'name' => 'Корзина'),
        );

        // получаем от модели массив профилей пользователя
        $profiles = array();
        if ($this->authUser) {
            $profiles = $this->userFrontendModel->getAllProfiles();
        }

        // если true, данные формы успешно отправлены, заказ размещен
        $success  = false;
        if ($this->issetSessionData('successCheckoutOrder')) {
            $success = true;
            $this->unsetSessionData('successCheckoutOrder');
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            // пользователь авторизован?
            'authUser'    => $this->authUser,
            // массив профилей пользователя
            'profiles'    => $profiles,
            // если true, заказ размещен
            'success'     => $success,
        );
        if ($success) { // заказ размещен, большинство переменных в шаблоне не нужны
            unset($this->centerVars['action'], $this->centerVars['authUser'], $this->centerVars['profiles']);
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('checkoutOrderForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('checkoutOrderForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('checkoutOrderForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция создает
     * заказ и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // имя контактного лица получателя
        $form['recipient_name']    = trim(utf8_substr(strip_tags($_POST['recipient_name']), 0, 32));
        // фамилия контактного лица получателя
        $form['recipient_surname'] = trim(utf8_substr(strip_tags($_POST['recipient_surname']), 0, 32));
        // e-mail контактного лица получателя
        $form['recipient_email']   = trim(utf8_substr(strip_tags($_POST['recipient_email']), 0, 32));
        // телефон контактного лица получателя
        $form['recipient_phone']   = trim(utf8_substr(strip_tags($_POST['recipient_phone']), 0, 32));

        if (isset($_POST['own_shipping'])) {          // самовывоз?
            $form['own_shipping']               = 1;
            $form['recipient_physical_address'] = ''; // адрес доставки
            $form['recipient_city']             = ''; // город (адрес доставки)
            $form['recipient_postal_index']     = ''; // почтовый индекс
        } else {
            $form['own_shipping']               = 0;
            $form['recipient_physical_address'] = trim(utf8_substr(strip_tags($_POST['recipient_physical_address']), 0, 250));
            $form['recipient_city']             = trim(utf8_substr(strip_tags($_POST['recipient_city']), 0, 32));
            $form['recipient_postal_index']     = trim(utf8_substr(strip_tags($_POST['recipient_postal_index']), 0, 32));
        }

        if (isset($_POST['recipient_legal_person'])) { // получатель - юридическое лицо?
            $form['recipient_legal_person']  = 1;
            $form['recipient_company']       = trim(utf8_substr(strip_tags($_POST['recipient_company']), 0, 64));
            $form['recipient_ceo_name']      = trim(utf8_substr(strip_tags($_POST['recipient_ceo_name']), 0, 64));
            $form['recipient_legal_address'] = trim(utf8_substr(strip_tags($_POST['recipient_legal_address']), 0, 250));
            $form['recipient_bank_name']     = trim(utf8_substr(strip_tags($_POST['recipient_bank_name']), 0, 64));
            $form['recipient_inn']           = trim(utf8_substr(strip_tags($_POST['recipient_inn']), 0, 32));
            $form['recipient_bik']           = trim(utf8_substr(strip_tags($_POST['recipient_bik']), 0, 32));
            $form['recipient_settl_acc']     = trim(utf8_substr(strip_tags($_POST['recipient_settl_acc']), 0, 32));
            $form['recipient_corr_acc']      = trim(utf8_substr(strip_tags($_POST['recipient_corr_acc']), 0, 32));
        } else {
            $form['recipient_legal_person']  = 0;
            $form['recipient_company']       = ''; // название компании получателя
            $form['recipient_ceo_name']      = ''; // генеральный директор компании получателя
            $form['recipient_legal_address'] = ''; // юридический адрес компании получателя
            $form['recipient_bank_name']     = ''; // название банка компании получателя
            $form['recipient_inn']           = ''; // ИНН компании получателя
            $form['recipient_bik']           = ''; // БИК компании получателя
            $form['recipient_settl_acc']     = ''; // номер расчетного счета в банке компании получателя
            $form['recipient_corr_acc']      = ''; // номер корреспондентского счета компании получателя
        }

        if (isset($_POST['recipient_payer_different'])) { // плательщик и получатель различаются
            $form['recipient_payer_different'] = 1;
            // имя контактного лица плательщика
            $form['payer_name']    = trim(utf8_substr(strip_tags($_POST['payer_name']), 0, 32));
            // фамилия контактного лица плательщика
            $form['payer_surname'] = trim(utf8_substr(strip_tags($_POST['payer_surname']), 0, 32));
            // e-mail контактного лица плательщика
            $form['payer_email']   = trim(utf8_substr(strip_tags($_POST['payer_email']), 0, 32));
            // телефон контактного лица плательщика
            $form['payer_phone']   = trim(utf8_substr(strip_tags($_POST['payer_phone']), 0, 32));

            if (isset($_POST['payer_legal_person'])) { // плательщик - юридическое лицо?
                $form['payer_legal_person']  = 1;
                $form['payer_company']       = trim(utf8_substr(strip_tags($_POST['payer_company']), 0, 64));
                $form['payer_ceo_name']      = trim(utf8_substr(strip_tags($_POST['payer_ceo_name']), 0, 64));
                $form['payer_legal_address'] = trim(utf8_substr(strip_tags($_POST['payer_legal_address']), 0, 250));
                $form['payer_bank_name']     = trim(utf8_substr(strip_tags($_POST['payer_bank_name']), 0, 64));
                $form['payer_inn']           = trim(utf8_substr(strip_tags($_POST['payer_inn']), 0, 32));
                $form['payer_bik']           = trim(utf8_substr(strip_tags($_POST['payer_bik']), 0, 32));
                $form['payer_settl_acc']     = trim(utf8_substr(strip_tags($_POST['payer_settl_acc']), 0, 32));
                $form['payer_corr_acc']      = trim(utf8_substr(strip_tags($_POST['payer_corr_acc']), 0, 32));
            } else {
                $form['payer_legal_person']  = 0;
                $form['payer_company']       = ''; // название компании плательщика
                $form['payer_ceo_name']      = ''; // генеральный директор компании плательщика
                $form['payer_legal_address'] = ''; // юридический адрес компании плательщика
                $form['payer_bank_name']     = ''; // название банка компании плательщика
                $form['payer_inn']           = ''; // ИНН компании плательщика
                $form['payer_bik']           = ''; // БИК компании плательщика
                $form['payer_settl_acc']     = ''; // номер расчетного счета в банке компании плательщика
                $form['payer_corr_acc']      = ''; // номер корреспондентского счета компании плательщика
            }
        } else {
            $form['recipient_payer_different'] = 0;
            // контактное лицо
            $form['payer_name']    = $form['recipient_name'];
            $form['payer_surname'] = $form['recipient_surname'];
            $form['payer_email']   = $form['recipient_email'];
            $form['payer_phone']   = $form['recipient_phone'];
            // юридическое лицо
            $form['payer_legal_person']  = $form['recipient_legal_person'];
            $form['payer_company']       = $form['recipient_company'];
            $form['payer_ceo_name']      = $form['recipient_ceo_name'];
            $form['payer_legal_address'] = $form['recipient_legal_address'];
            $form['payer_bank_name']     = $form['recipient_bank_name'];
            $form['payer_inn']           = $form['recipient_inn'];
            $form['payer_bik']           = $form['recipient_bik'];
            $form['payer_settl_acc']     = $form['recipient_settl_acc'];
            $form['payer_corr_acc']      = $form['recipient_corr_acc'];
        }

        $form['comment'] = trim(utf8_substr(strip_tags($_POST['comment']), 0, 250));

        /*
         * были допущены ошибки при заполнении формы?
         */
        if (empty($form['recipient_name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица получателя»';
        }
        if (empty($form['recipient_surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица получателя»';
        }
        if (empty($form['recipient_email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица получателя»';
        }
        if (!$form['own_shipping']) { // если не самовывоз, должно быть заполнено поле «Адрес»
            if (empty($form['recipient_physical_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
        }
        // если получатель - юридическое лицо
        if ($form['recipient_legal_person']) {
            if (empty($form['recipient_company'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название компании получателя»';
            }
            if (empty($form['recipient_ceo_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании получателя»';
            }
            if (empty($form['recipient_legal_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании получателя»';
            }
            if (empty($form['recipient_inn'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании получателя»';
            }
            if (empty($form['recipient_bank_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании получателя»';
            }
            if (empty($form['recipient_bik'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «БИК компании получателя»';
            }
            if (empty($form['recipient_settl_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании получателя»';
            }
            if (empty($form['recipient_corr_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет компании получателя»';
            }
        }
        // если плательщик и получатель различаются
        if ($form['recipient_payer_different']) {
            if (empty($form['payer_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица плательщика»';
            }
            if (empty($form['payer_surname'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица плательщика»';
            }
            if (empty($form['payer_email'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица плательщика»';
            }
            // если плательщик - юридическое лицо
            if ($form['payer_legal_person']) {
                if (empty($form['payer_company'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название компании плательщика»';
                }
                if (empty($form['payer_ceo_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании плательщика»';
                }
                if (empty($form['payer_legal_address'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании плательщика»';
                }
                if (empty($form['payer_inn'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании плательщика»';
                }
                if (empty($form['payer_bank_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании плательщика»';
                }
                if (empty($form['payer_bik'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «БИК компании плательщика»';
                }
                if (empty($form['payer_settl_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании плательщика»';
                }
                if (empty($form['payer_corr_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет компании плательщика»';
                }
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $form['errorMessage'] = $errorMessage;
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        // создать профиль получателя?
        if ($this->authUser && isset($_POST['make_recipient_profile'])) {
            $data = array(
                'title' => 'Профиль получателя',
                'name' => $form['recipient_name'],
                'surname' => $form['recipient_surname'],
                'email' => $form['recipient_email'],
                'phone' => $form['recipient_phone'],
                'own_shipping' => $form['own_shipping'],
                'physical_address' => $form['recipient_physical_address'],
                'city' => $form['recipient_city'],
                'postal_index' => $form['recipient_postal_index'],
                'legal_person' => $form['recipient_legal_person'],
                'company' => $form['recipient_company'],
                'ceo_name' => $form['recipient_ceo_name'],
                'legal_address' => $form['recipient_legal_address'],
                'bank_name' => $form['recipient_bank_name'],
                'inn' => $form['recipient_inn'],
                'bik' => $form['recipient_bik'],
                'settl_acc' => $form['recipient_settl_acc'],
                'corr_acc' => $form['recipient_corr_acc'],
            );
            $this->userFrontendModel->addProfile($data);
        }
        // создать профиль плательщика?
        if ($this->authUser && $form['recipient_payer_different'] && isset($_POST['make_payer_profile'])) {
            $data = array(
                'title' => 'Профиль плательщика',
                'name' => $form['payer_name'],
                'surname' => $form['payer_surname'],
                'email' => $form['payer_email'],
                'phone' => $form['payer_phone'],
                'own_shipping' => 1,
                'physical_address' => '',
                'city' => '',
                'postal_index' => '',
                'legal_person' => $form['payer_legal_person'],
                'company' => $form['payer_company'],
                'ceo_name' => $form['payer_ceo_name'],
                'legal_address' => $form['payer_legal_address'],
                'bank_name' => $form['payer_bank_name'],
                'inn' => $form['payer_inn'],
                'bik' => $form['payer_bik'],
                'settl_acc' => $form['payer_settl_acc'],
                'corr_acc' => $form['payer_corr_acc'],
            );
            $this->userFrontendModel->addProfile($data);
        }

        $data = array(
            'user_id' => $this->authUser ? $this->user['id'] : 0,
            'details' => serialize($form),
        );
        $email = $this->authUser ? $this->user['email'] : null;
        // обращаемся к модели корзины для создания заказа
        $result = $this->basketFrontendModel->createOrder($email, $data);

        if (!$result) {
            $form['errorMessage'] = array('Произошла ошибка при добавлении заказа, попробуйте еще раз');
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        return true;

    }

}