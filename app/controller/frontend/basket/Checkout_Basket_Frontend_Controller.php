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
        if ( (!$this->basketFrontendModel->getBasketCount()) && (!$this->issetSessionData('successCheckoutOrder'))) {
            // перенаправляем пользователя на страницу корзины
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // заказ был создан успешно, отмечаем этот факт
                $this->setSessionData('successCheckoutOrder', true);
            }
            // перенаправляем пользователя опять на страницу оформления заказа,
            // где он увидит либо форму с сообщениями об ошибках, допущенных при
            // заполнении формы, либо сообщение «Ваш заказ успешно создан»
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/checkout'));
        }

        $this->title = 'Оформление заявки. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->basketFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->basketFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Корзина',
                'url'  => $this->basketFrontendModel->getURL('frontend/basket/index')
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

        // если пользователь авторизован, получаем от модели массив профилей
        $profiles = array();
        if ($this->authUser) {
            $profiles = $this->userFrontendModel->getUserProfiles();
        }

        /*
         * если пользователь не зарегистрирован на сайте, но уже делал заказы;
         * эти информация нам нужна, чтобы облегчить пользователю заполнение
         * формы при оформлении заказа; он может отметить checkbox «использовать
         * данные последнего заказа», при этом будет выполнен XmlHttpRequest и
         * форма будет заполнена полученными с сервера данными
         */
        $customer = false;
        if ( ! $this->authUser) {
            $customer = ! empty($this->userFrontendModel->getLastOrderData());
        }


        // получаем от модели список офисов для самовывоза товара со склада
        $offices = $this->basketFrontendModel->getOffices();

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
            'breadcrumbs'        => $breadcrumbs,
            // атрибут action тега form
            'action'             => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            // пользователь авторизован?
            'authUser'           => $this->authUser,
            // не зарегистрированный пользователь уже делал заказы ранее?
            'customer'           => $customer,
            // фамилия контактного лица получателя
            'buyer_name'         => $name,
            // имя контактного лица получателя
            'buyer_surname'      => $surname,
            // отчество контактного лица получателя
            'buyer_patronymic'   => $patronymic,
            // e-mail контактного лица получателя
            'buyer_email'        => $email,
            // массив профилей пользователя
            'profiles'           => $profiles,
            // массив офисов для самовывоза
            'offices'            => $offices,
            // если true, заказ размещен
            'success'            => $success,
            // сообщение об успешном размещении заказа
            'message'            => $this->config->message->checkout,
        );
        if ($success) { // заказ размещен, большинство переменных в шаблоне не нужны
            unset(
                $this->centerVars['action'],
                $this->centerVars['authUser'],
                $this->centerVars['profiles'],
                $this->centerVars['offices']
            );
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
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // фамилия контактного лица получателя
        $form['buyer_surname']    = trim(iconv_substr(strip_tags($_POST['buyer_surname']), 0, 32));
        // имя контактного лица получателя
        $form['buyer_name']       = trim(iconv_substr(strip_tags($_POST['buyer_name']), 0, 32));
        // отчество контактного лица получателя
        $form['buyer_patronymic'] = trim(iconv_substr(strip_tags($_POST['buyer_patronymic']), 0, 32));
        // телефон контактного лица получателя
        $form['buyer_phone']      = trim(iconv_substr(strip_tags($_POST['buyer_phone']), 0, 64));
        // e-mail контактного лица получателя
        $form['buyer_email']      = trim(iconv_substr(strip_tags($_POST['buyer_email']), 0, 64));

        if (isset($_POST['shipping'])) { // самовывоз со склада
            $form['shipping']               = 1;
            if (isset($_POST['office']) && in_array($_POST['office'], array(1,2,3,4))) {
                $form['shipping']           = (int)$_POST['office'];
            }
            $form['buyer_shipping_address'] = ''; // адрес доставки
            $form['buyer_shipping_city']    = ''; // город доставки
            $form['buyer_shipping_index']   = ''; // почтовый индекс
        } else { // доставка по адресу
            $form['shipping']               = 0;
            $form['buyer_shipping_address'] = trim(iconv_substr(strip_tags($_POST['buyer_shipping_address']), 0, 250));
            $form['buyer_shipping_city']    = trim(iconv_substr(strip_tags($_POST['buyer_shipping_city']), 0, 32));
            $form['buyer_shipping_index']   = trim(iconv_substr(strip_tags($_POST['buyer_shipping_index']), 0, 6));
        }

        if (isset($_POST['buyer_company'])) { // получатель - юридическое лицо?
            // получатель - юридическое лицо
            $form['buyer_company']         = 1;
            // название компании получателя
            $form['buyer_company_name']    = trim(iconv_substr(strip_tags($_POST['buyer_company_name']), 0, 64));
            // генеральный директор компании получателя
            $form['buyer_company_ceo']     = trim(iconv_substr(strip_tags($_POST['buyer_company_ceo']), 0, 64));
            // название компании получателя
            $form['buyer_company_address'] = trim(iconv_substr(strip_tags($_POST['buyer_company_address']), 0, 250));
            // ИНН компании получателя
            $form['buyer_company_inn']     = trim(iconv_substr(strip_tags($_POST['buyer_company_inn']), 0, 12));
            // КПП компании получателя
            $form['buyer_company_kpp']     = trim(iconv_substr(strip_tags($_POST['buyer_company_kpp']), 0, 9));
            // название банка компании получателя
            $form['buyer_bank_name']       = trim(iconv_substr(strip_tags($_POST['buyer_bank_name']), 0, 64));
            // БИК банка компании получателя
            $form['buyer_bank_bik']        = trim(iconv_substr(strip_tags($_POST['buyer_bank_bik']), 0, 9));
            // номер расчетного счета в банке компании получателя
            $form['buyer_settl_acc']       = trim(iconv_substr(strip_tags($_POST['buyer_settl_acc']), 0, 20));
            // номер корреспондентского счета банка компании получателя
            $form['buyer_corr_acc']        = trim(iconv_substr(strip_tags($_POST['buyer_corr_acc']), 0, 20));
        } else {
            $form['buyer_company']         = 0;  // получатель - не юридическое лицо
            $form['buyer_company_name']    = ''; // название компании получателя
            $form['buyer_company_ceo']     = ''; // генеральный директор компании получателя
            $form['buyer_company_address'] = ''; // юридический адрес компании получателя
            $form['buyer_company_inn']     = ''; // ИНН компании получателя
            $form['buyer_company_kpp']     = ''; // КПП компании получателя
            $form['buyer_bank_name']       = ''; // название банка компании получателя
            $form['buyer_bank_bik']        = ''; // БИК банка компании получателя
            $form['buyer_settl_acc']       = ''; // номер расчетного счета в банке компании получателя
            $form['buyer_corr_acc']        = ''; // номер корреспондентского счета банка компании получателя
        }

        // создать профиль получателя на основе введенных данных? (только
        // для авторизованного пользователя, у которого еще нет профилей)
        $form['make_buyer_profile'] = 0;
        if (isset($_POST['make_buyer_profile'])) {
            $form['make_buyer_profile'] = 1;
        }

        if (isset($_POST['buyer_payer_different'])) { // получатель и плательщик различаются?
            // получатель и плательщик различаются
            $form['buyer_payer_different'] = 1;
            // имя контактного лица плательщика
            $form['payer_name']       = trim(iconv_substr(strip_tags($_POST['payer_name']), 0, 32));
            // фамилия контактного лица плательщика
            $form['payer_surname']    = trim(iconv_substr(strip_tags($_POST['payer_surname']), 0, 32));
            // отчество контактного лица получателя
            $form['payer_patronymic'] = trim(iconv_substr(strip_tags($_POST['payer_patronymic']), 0, 32));
            // телефон контактного лица плательщика
            $form['payer_phone']      = trim(iconv_substr(strip_tags($_POST['payer_phone']), 0, 64));
            // e-mail контактного лица плательщика
            $form['payer_email']      = trim(iconv_substr(strip_tags($_POST['payer_email']), 0, 64));

            if (isset($_POST['payer_company'])) { // плательщик - юридическое лицо?
                // плательщик - юридическое лицо
                $form['payer_company']         = 1;
                // название компании плательщика
                $form['payer_company_name']    = trim(iconv_substr(strip_tags($_POST['payer_company_name']), 0, 64));
                // генеральный директор компании плательщика
                $form['payer_company_ceo']     = trim(iconv_substr(strip_tags($_POST['payer_company_ceo']), 0, 64));
                // юридический адрес компании плательщика
                $form['payer_company_address'] = trim(iconv_substr(strip_tags($_POST['payer_company_address']), 0, 250));
                // ИНН компании плательщика
                $form['payer_company_inn']     = trim(iconv_substr(strip_tags($_POST['payer_company_inn']), 0, 12));
                // КПП компании плательщика
                $form['payer_company_kpp']     = trim(iconv_substr(strip_tags($_POST['payer_company_kpp']), 0, 9));
                // название банка компании плательщика
                $form['payer_bank_name']       = trim(iconv_substr(strip_tags($_POST['payer_bank_name']), 0, 64));
                // БИК банка компании плательщика
                $form['payer_bank_bik']        = trim(iconv_substr(strip_tags($_POST['payer_bank_bik']), 0, 9));
                // номер расчетного счета в банке компании плательщика
                $form['payer_settl_acc']       = trim(iconv_substr(strip_tags($_POST['payer_settl_acc']), 0, 20));
                // номер корреспондентского счета банка компании плательщика
                $form['payer_corr_acc']        = trim(iconv_substr(strip_tags($_POST['payer_corr_acc']), 0, 20));
            } else {
                $form['payer_company']         = 0;  // плательщик - не юридическое лицо
                $form['payer_company_name']    = ''; // название компании плательщика
                $form['payer_company_ceo']     = ''; // генеральный директор компании плательщика
                $form['payer_company_address'] = ''; // юридический адрес компании плательщика
                $form['payer_company_inn']     = ''; // ИНН компании плательщика
                $form['payer_company_kpp']     = ''; // КПП компании плательщика
                $form['payer_bank_name']       = ''; // название банка компании плательщика
                $form['payer_bank_bik']        = ''; // БИК банка компании плательщика
                $form['payer_settl_acc']       = ''; // номер расчетного счета в банке компании плательщика
                $form['payer_corr_acc']        = ''; // номер корреспондентского счета банка компании плательщика
            }
        } else {
            // плательщик и получатель не различаются
            $form['buyer_payer_different'] = 0;
            // контактное лицо
            $form['payer_name']            = '';
            $form['payer_surname']         = '';
            $form['payer_patronymic']      = '';
            $form['payer_phone']           = '';
            $form['payer_email']           = '';
            // юридическое лицо
            $form['payer_company']         = 0;
            $form['payer_company_name']    = '';
            $form['payer_company_ceo']     = '';
            $form['payer_company_address'] = '';
            $form['payer_company_inn']     = '';
            $form['payer_company_kpp']     = '';
            $form['payer_bank_name']       = '';
            $form['payer_bank_bik']        = '';
            $form['payer_settl_acc']       = '';
            $form['payer_corr_acc']        = '';
        }

        // создать профиль плательщика на основе введенных данных? (только
        // для авторизованного пользователя, у которого еще нет профилей)
        $form['make_payer_profile'] = 0;
        if (isset($_POST['make_payer_profile'])) {
            $form['make_payer_profile'] = 1;
        }

        // комментарий к заказу
        $form['comment'] = trim(iconv_substr(strip_tags($_POST['comment']), 0, 250));

        /*
         * были допущены ошибки при заполнении формы?
         */
        if (empty($form['buyer_surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица получателя»';
        } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $form['buyer_surname'])) {
            $errorMessage[] = 'Поле «Фамилия контактного лица получателя» содержит недопустимые символы';
        }
        if (empty($form['buyer_name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица получателя»';
        } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $form['buyer_name'])) {
            $errorMessage[] = 'Поле «Имя контактного лица получателя» содержит недопустимые символы';
        }
        if ( ! empty($form['buyer_patronymic'])) {
            if ( ! preg_match('#^[a-zA-Zа-яА-ЯёЁ]+$#u', $form['buyer_patronymic'])) {
                $errorMessage[] = 'Поле «Отчество контактного лица получателя» содержит недопустимые символы';
            }
        }
        if (empty($form['buyer_phone'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Телефон контактного лица получателя»';
        }
        if (empty($form['buyer_email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица получателя»';
        } elseif ( ! preg_match('#^[_0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*[0-9a-z]\.[a-z]{2,}$#i', $form['buyer_email'])) {
            $errorMessage[] = 'Поле «E-mail контактного лица получателя» должно соответствовать формату somebody@mail.ru';
        }
        if ( ! $form['shipping']) { // если не самовывоз, должно быть заполнено поле «Адрес»
            if (empty($form['buyer_shipping_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
            if ( ! empty($form['buyer_shipping_index'])) {
                if ( ! preg_match('#^\d{6}$#i', $form['buyer_shipping_index'])) {
                    $errorMessage[] = 'Поле «Почтовый индекс» должно содержать 6 цифр';
                }
            }
        }
        // если получатель - юридическое лицо
        if ($form['buyer_company']) {
            if (empty($form['buyer_company_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название компании получателя»';
            }
            if (empty($form['buyer_company_ceo'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании получателя»';
            }
            if (empty($form['buyer_company_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании получателя»';
            }
            if (empty($form['buyer_company_inn'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании получателя»';
            } elseif ( ! preg_match('#^(\d{10}|\d{12})$#i', $form['buyer_company_inn'])) {
                $errorMessage[] = 'Поле «ИНН компании получателя» должно содержать 10 или 12 цифр';
            }
            if ( ! empty($form['buyer_company_kpp'])) {
                if ( ! preg_match('#^\d{9}$#i', $form['buyer_company_kpp'])) {
                    $errorMessage[] = 'Поле «КПП компании получателя» должно содержать 9 цифр';
                }
            }
            if (empty($form['buyer_bank_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании получателя»';
            }
            if (empty($form['buyer_bank_bik'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «БИК банка компании получателя»';
            } elseif ( ! preg_match('#^\d{9}$#i', $form['buyer_bank_bik'])) {
                $errorMessage[] = 'Поле «БИК банка компании получателя» должно содержать 9 цифр';
            }
            if (empty($form['buyer_settl_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании получателя»';
            } elseif ( ! preg_match('#^\d{20}$#i', $form['buyer_settl_acc'])) {
                $errorMessage[] = 'Поле «Расчетный счет компании получателя» должно содержать 20 цифр';
            }
            if (empty($form['buyer_corr_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Корр. счет банка компании получателя»';
            } elseif ( ! preg_match('#^\d{20}$#i', $form['buyer_corr_acc'])) {
                $errorMessage[] = 'Поле  «Корр. счет банка компании получателя» должно содержать 20 цифр';
            }
        }
        // если плательщик и получатель различаются
        if ($form['buyer_payer_different']) {
            if (empty($form['payer_surname'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица плательщика»';
            } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $form['payer_surname'])) {
                $errorMessage[] = 'Поле «Фамилия контактного лица плательщика» содержит недопустимые символы';
            }
            if (empty($form['payer_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица плательщика»';
            } elseif ( ! preg_match('#^[-a-zA-Zа-яА-ЯёЁ]+$#u', $form['payer_name'])) {
                $errorMessage[] = 'Поле «Имя контактного лица плательщика» содержит недопустимые символы';
            }
            if ( ! empty($form['payer_patronymic'])) {
                if ( ! preg_match('#^[a-zA-Zа-яА-ЯёЁ]+$#u', $form['payer_patronymic'])) {
                    $errorMessage[] = 'Поле «Отчество контактного лица плательщика» содержит недопустимые символы';
                }
            }
            if (empty($form['payer_phone'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Телефон контактного лица плательщика»';
            }
            if (empty($form['payer_email'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица плательщика»';
            } elseif ( ! preg_match('#^[_0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*[0-9a-z]\.[a-z]{2,}$#i', $form['payer_email'])) {
                $errorMessage[] = 'Поле «E-mail контактного лица плательщика» должно соответствовать формату somebody@mail.ru';
            }
            // если плательщик - юридическое лицо
            if ($form['payer_company']) {
                if (empty($form['payer_company_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название компании плательщика»';
                }
                if (empty($form['payer_company_ceo'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании плательщика»';
                }
                if (empty($form['payer_company_address'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании плательщика»';
                }
                if (empty($form['payer_company_inn'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании плательщика»';
                } elseif ( ! preg_match('#^(\d{10}|\d{12})$#i', $form['payer_company_inn'])) {
                    $errorMessage[] = 'Поле «ИНН компании плательщика» должно содержать 10 или 12 цифр';
                }
                if ( ! empty($form['payer_company_kpp'])) {
                    if ( ! preg_match('#^\d{9}$#i', $form['payer_company_kpp'])) {
                        $errorMessage[] = 'Поле «КПП компании плательщика» должно содержать 9 цифр';
                    }
                }
                if (empty($form['payer_bank_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании плательщика»';
                }
                if (empty($form['payer_bank_bik'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «БИК банка компании плательщика»';
                } elseif ( ! preg_match('#^\d{9}$#i', $form['payer_bank_bik'])) {
                    $errorMessage[] = 'Поле «БИК банка компании плательщика» должно содержать 9 цифр';
                }
                if (empty($form['payer_settl_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании плательщика»';
                } elseif ( ! preg_match('#^\d{20}$#i', $form['payer_settl_acc'])) {
                    $errorMessage[] = 'Поле «Расчетный счет компании плательщика» должно содержать 20 цифр';
                }
                if (empty($form['payer_corr_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Корр. счет банка компании плательщика»';
                } elseif ( ! preg_match('#^\d{20}$#i', $form['payer_corr_acc'])) {
                    $errorMessage[] = 'Поле «Корр. счет банка компании плательщика» должно содержать 20 цифр';
                }
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $form['errorMessage'] = $errorMessage;
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        // создать профиль получателя?
        if ($this->authUser && $form['make_buyer_profile']) {
            $data = array(
                'title'            => 'Профиль получателя',
                'name'             => $form['buyer_name'],
                'surname'          => $form['buyer_surname'],
                'patronymic'       => $form['buyer_patronymic'],
                'phone'            => $form['buyer_phone'],
                'email'            => $form['buyer_email'],
                'shipping'         => $form['shipping'],
                'shipping_address' => $form['buyer_shipping_address'],
                'shipping_city'    => $form['buyer_shipping_city'],
                'shipping_index'   => $form['buyer_shipping_index'],
                'company'          => $form['buyer_company'],
                'company_name'     => $form['buyer_company_name'],
                'company_ceo'      => $form['buyer_company_ceo'],
                'company_address'  => $form['buyer_company_address'],
                'company_inn'      => $form['buyer_company_inn'],
                'company_kpp'      => $form['buyer_company_kpp'],
                'bank_name'        => $form['buyer_bank_name'],
                'bank_bik'         => $form['buyer_bank_bik'],
                'settl_acc'        => $form['buyer_settl_acc'],
                'corr_acc'         => $form['buyer_corr_acc'],
            );
            // создаем профиль получателя
            $this->userFrontendModel->addProfile($data);
            unset($form['make_buyer_profile']);
        }
        // создать профиль плательщика?
        if ($this->authUser && $form['make_payer_profile'] && $form['buyer_payer_different']) {
            $data = array(
                'title'            => 'Профиль плательщика',
                'name'             => $form['payer_name'],
                'surname'          => $form['payer_surname'],
                'patronymic'       => $form['payer_patronymic'],
                'phone'            => $form['payer_phone'],
                'email'            => $form['payer_email'],
                'shipping'         => 1,
                'shipping_address' => '',
                'shipping_city'    => '',
                'shipping_index'   => '',
                'company'          => $form['payer_company'],
                'company_name'     => $form['payer_company_name'],
                'company_ceo'      => $form['payer_company_ceo'],
                'company_address'  => $form['payer_company_address'],
                'company_inn'      => $form['payer_company_inn'],
                'company_kpp'      => $form['payer_company_kpp'],
                'bank_name'        => $form['payer_bank_name'],
                'bank_bik'         => $form['payer_bank_bik'],
                'settl_acc'        => $form['payer_settl_acc'],
                'corr_acc'         => $form['payer_corr_acc'],
            );
            // создаем профиль плательщика
            $this->userFrontendModel->addProfile($data);
            unset($form['make_payer_profile']);
        }

        // обращаемся к модели корзины для создания заказа
        $result = $this->basketFrontendModel->createOrder($form);

        if ( ! $result) {
            $form['errorMessage'] = array('Произошла ошибка при добавлении заявки, попробуйте еще раз');
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        return true;

    }

}
