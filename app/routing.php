<?php
$routing = array( // поддержка ЧПУ (SEF) для общедоступной части сайта
    'enable'  => true,
    'cap2sef' => array( // Controller/Action/Params => Search Engines Friendly
        /*
         * главная страница сайта
         */
        '~^frontend/index/index$~i' =>
        '',
        /*
         * каталог
         */
        // главная страница каталога
        '~^frontend/catalog/index$~i' =>
        'catalog',
        // категория каталога
        '~^frontend/catalog/category/id/(\d+)$~i' =>
        'catalog/category/$1',
        // категория каталога, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/page/$2',
        // категория каталога, фильтр по производителю
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)$~i' =>
        'catalog/category/$1/maker/$2',
        // категория каталога, фильтр по производителю, сортировка
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/maker/$2/sort/$3',
        // категория каталога, фильтр по производителю, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/page/$3',
        // категория каталога, фильтр по производителю, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/maker/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/maker/$2/sort/$3/page/$4',
        // категория каталога, сортировка
        '~^frontend/catalog/category/id/(\d+)/sort/(\d)$~i' =>
        'catalog/category/$1/sort/$2',
        // категория каталога, сортировка, постраничная навигация
        '~^frontend/catalog/category/id/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/category/$1/sort/$2/page/$3',
        // страница товара каталога
        '~^frontend/catalog/product/id/(\d+)$~i' =>
        'catalog/product/$1',
        // страница со списком производителей
        '~^frontend/catalog/allmkrs$~i' =>
        'catalog/all-makers',
        // страница со списком товаров выбранного производителя
        '~^frontend/catalog/maker/id/(\d+)$~i' =>
        'catalog/maker/$1',
        // страница со списком товаров выбранного производителя, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/page/(\d+)$~i' =>
        'catalog/maker/$1/page/$2',
        // страница со списком товаров выбранного производителя, сортировка
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)$~i' =>
        'catalog/maker/$1/sort/$2',
        // страница со списком товаров выбранного производителя, сортировка, постраничная навигация
        '~^frontend/catalog/maker/id/(\d+)/sort/(\d)/page/(\d+)$~i' =>
        'catalog/maker/$1/sort/$2/page/$3',
        // страница поиска по каталогу
        '~^frontend/catalog/search$~i'=>
        'catalog/search',
        // страница результатов поиска по каталогу
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'catalog/search/query/$1/page/$2',
        // поиск по каталогу, XmlHttpRequest
        '~^frontend/catalog/ajax$~i' => 'catalog/ajax',
        /*
         * просмотренные товары
         */
        // страница со списком всех просмотренных товаров
        '~^frontend/viewed/index$~i' =>
        'viewed',
        // просмотренные товары, постраничная навигация
        '~^frontend/viewed/index/page/(\d+)$~i' =>
        'viewed/page/$1',
        /*
         * отложенные товары
         */
        // страница со списком всех отложенных товаров
        '~^frontend/wished/index$~i' =>
        'wished',
        // отложенные товары, постраничная навигация
        '~^frontend/wished/index/page/(\d+)$~i' =>
        'wished/page/$1',
        // добавить товар в список отложенных
        '~^frontend/wished/addprd$~i' =>
        'wished/addprd',
        // удалить товар из списка отложенных
        '~^frontend/wished/rmvprd$~i' =>
        'wished/rmvprd',
        // добавить товар в список отложенных, XmlHttpRequest
        '~^frontend/wished/ajax/action/addprd$~i' =>
        'wished/ajax/addprd/',
        // удалить товар из списка отложенных, XmlHttpRequest
        '~^frontend/wished/ajax/action/rmvprd$~i' =>
        'wished/ajax/rmvprd',
        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^frontend/compared/index$~i' =>
        'compared',
        // товары для сравнения, постраничная навигация
        '~^frontend/compared/index/page/(\d+)$~i' =>
        'compared/page/$1',
        // добавить товар в список сравнения
        '~^frontend/compared/addprd$~i' =>
        'compared/addprd',
        // удалить товар из списка сравнения
        '~^frontend/compared/rmvprd$~i' =>
        'compared/rmvprd',
        // добавить товар в список сравнения, XmlHttpRequest
        '~^frontend/compared/ajax/action/addprd$~i' =>
        'compared/ajax/addprd',
        // удалить товар из списка сравнения, XmlHttpRequest
        '~^frontend/compared/ajax/action/rmvprd$~i' =>
        'compared/ajax/rmvprd',
        /*
         * корзина
         */
        // страница покупательской корзины
        '~^frontend/basket/index$~i' =>
        'basket',
        // добавить товар в корзину
        '~^frontend/basket/addprd$~i' =>
        'basket/addprd',
        // удалить товар из корзины
        '~^frontend/basket/rmvprd/id/(\d+)$~i' =>
        'basket/rmvprd/$1',
        // оформление заказ
        '~^frontend/basket/checkout$~i' =>
        'basket/checkout',
        // добавить товар в корзину, XmlHttpRequest
        '~^frontend/basket/ajax/action/addprd$~i' =>
        'basket/ajax/addprd',
        /*
         * пользователи
         */
        // личный кабинет пользователя
        '~^frontend/user/index$~i' =>
        'user/index',
        // страница авторизации пользователя
        '~^frontend/user/login$~i' =>
        'user/login',
        // выйти из личного кабинета
        '~^frontend/user/logout$~i' =>
        'user/logout',
        // страница регистрации нового пользователя
        '~^frontend/user/reg$~i' =>
        'user/reg',
        // страница редактирования личных данных
        '~^frontend/user/edit$~i' =>
        'user/edit',
        // страница со списком профилей
        '~^frontend/user/allprof$~i' =>
        'user/allprof',
        // страница с формой для добавления профиля
        '~^frontend/user/addprof$~i' =>
        'user/addprof',
        // страница с формой для редактирования профиля
        '~^frontend/user/editprof/id/(\d+)$~i' =>
        'user/editprof/$1',
        // удаление профиля
        '~^frontend/user/rmvprof/id/(\d+)$~i' =>
        'user/rmvprof/$1',
        // восстановление пароля
        '~^frontend/user/forgot$~i' =>
        'user/forgot',
        // история заказов
        '~^frontend/user/allorders$~i' => 'user/all-orders',
        // история заказов, постраничная навигация
        '~^frontend/user/allorders/page/(\d+)$~i' =>
        'user/all-orders/page/$1',
        // подробная информация о заказе
        '~^frontend/user/order/id/(\d+)$~i' =>
        'user/order/$1',
        // повторить заказ
        '~^frontend/user/repeat/id/(\d+)$~i' =>
        'user/repeat/$1',
        // получение профиля, XmlHttpRequest
        '~^frontend/user/ajax/id/(\d+)$~i' =>
        'user/ajax/profile/$1',
        /*
         * новости
         */
        // главная страница новостей
        '~^frontend/news/index$~i' =>
        'news',
        // главная страница новостей, постраничная навигация
        '~^frontend/news/index/page/(\d+)$~i' =>
        'news/page/$1',
        // отдельная новость
        '~^frontend/news/item/id/(\d+)$~i' =>
        'news/item/$1',
        // список новостей выбранной категории
        '~^frontend/news/category/id/(\d+)$~i' =>
        'news/ctg/$1',
        // список новостей выбранной категории, постраничная навигация
        '~^frontend/news/category/id/(\d+)/page/(\d+)$~i' =>
        'news/ctg/$1/page/$2',
        /*
         * страницы
         */
        '~^frontend/page/index/id/(\d+)$~i' =>
        'page/$1',
        /*
         * карта сайта
         */
        '~^frontend/sitemap/index$~i' =>
        'sitemap',
    ),
    'sef2cap' => array( // Search Engines Friendly => Controller/Action/Params
        '~^$~'                                                         => 'frontend/index/index', // главная страница сайта
        /* каталог */
        '~^catalog$~i'                                                 => 'frontend/catalog/index', // главная страница каталога
        '~^catalog/category/(\d+)$~i'                                  => 'frontend/catalog/category/id/$1', // категория каталога
        '~^catalog/category/(\d+)/page/(\d+)$~i'                       => 'frontend/catalog/category/id/$1/page/$2', // категория каталога, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)$~i'                      => 'frontend/catalog/category/id/$1/maker/$2', // категория каталога, фильтр по производителю
        '~^catalog/category/(\d+)/maker/(\d+)/sort/(\d)$~i'            => 'frontend/catalog/category/id/$1/maker/$2/sort/$3', // категория каталога, фильтр по производителю, сортировка
        '~^catalog/category/(\d+)/maker/(\d+)/page/(\d+)$~i'           => 'frontend/catalog/category/id/$1/maker/$2/page/$3', // категория каталога, фильтр по производителю, постраничная навигация
        '~^catalog/category/(\d+)/maker/(\d+)/sort/(\d)/page/(\d+)$~i' => 'frontend/catalog/category/id/$1/maker/$2/sort/$3/page/$4', // категория каталога, фильтр по производителю, сортировка, постраничная навигация
        '~^catalog/category/(\d+)/sort/(\d)$~i'                        => 'frontend/catalog/category/id/$1/sort/$2', // категория каталога, сортировка
        '~^catalog/category/(\d+)/sort/(\d)/page/(\d+)$~i'             => 'frontend/catalog/category/id/$1/sort/$2/page/$3', // категория каталога, сортировка, постраничная навигация
        '~^catalog/product/(\d+)$~i'                                   => 'frontend/catalog/product/id/$1', // страница товара каталога
        '~^catalog/all-makers$~i'                                      => 'frontend/catalog/allmkrs', // страница со списком всех производителей
        '~^catalog/maker/(\d+)$~i'                                     => 'frontend/catalog/maker/id/$1', // страница со списком товаров выбранного производителя
        '~^catalog/maker/(\d+)/page/(\d+)$~i'                          => 'frontend/catalog/maker/id/$1/page/$2', // страница со списком товаров выбранного производителя, постраничная навигация
        '~^catalog/maker/(\d+)/sort/(\d+)$~i'                          => 'frontend/catalog/maker/id/$1/sort/$2', // страница со списком товаров выбранного производителя, сортировка
        '~^catalog/maker/(\d+)/sort/(\d)/page/(\d+)$~i'                => 'frontend/catalog/maker/id/$1/sort/$2/page/$3', // страница со списком товаров выбранного производителя, сортировка, постраничная навигация
        '~^catalog/search$~i'                                          => 'frontend/catalog/search', // страница поиска по каталогу
        '~^catalog/search/query/([a-z0-9%_.-]+)$~i'                    => 'frontend/catalog/search/query/$1', // страница результатов поиска по каталогу
        '~^catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i'         => 'frontend/catalog/search/query/$1/page/$2', // страница результатов поиска по каталогу, постраничная навигация
        '~^catalog/ajax$~i'                                            => 'frontend/catalog/ajax', // поиск по каталогу, XmlHttpRequest
        /* просмотренные товары */
        '~^viewed$~i'                                                  => 'frontend/viewed/index', // страница со списком всех просмотренных товаров
        '~^viewed/page/(\d+)$~i'                                       => 'frontend/viewed/index/page/$1', // просмотренные товары, постраничная навигация
        /* отложенные товары */
        '~^wished$~i'                                                  => 'frontend/wished/index', // страница со списком всех отложенных товаров
        '~^wished/page/(\d+)$~i'                                       => 'frontend/wished/index/page/$1', // отложенные товары, постраничная навигация
        '~^wished/addprd$~i'                                           => 'frontend/wished/addprd', // добавить товар в список отложенных
        '~^wished/rmvprd$~i'                                           => 'frontend/wished/rmvprd', // удалить товар из списка отложенных
        '~^wished/ajax/addprd$~i'                                      => 'frontend/wished/ajax/action/addprd', // добавить товар в список отложенных, XmlHttpRequest
        '~^wished/ajax/rmvprd$~i'                                      => 'frontend/wished/ajax/action/rmvprd', // удалить товар из списка отложенных, XmlHttpRequest
        /* товары для сравнения */
        '~^compared$~i'                                                => 'frontend/compared/index', // страница со списком товаров для сравнения
        '~^compared/page/(\d+)$~i'                                     => 'frontend/compared/index/page/$1', // товары для сравнения, постраничная навигация
        '~^compared/addprd$~i'                                         => 'frontend/compared/addprd', // добавить товар в список сравнения
        '~^compared/rmvprd$~i'                                         => 'frontend/compared/rmvprd', // удалить товар из списка сравнения
        '~^compared/ajax/addprd$~i'                                    => 'frontend/compared/ajax/action/addprd', // добавить товар в список сравнения, XmlHttpRequest
        '~^compared/ajax/rmvprd$~i'                                    => 'frontend/compared/ajax/action/rmvprd', // удалить товар из списка сравнения, XmlHttpRequest
        /* корзина */
        '~^basket$~i'                                                  => 'frontend/basket/index', // страница покупательской корзины
        '~^basket/addprd$~i'                                           => 'frontend/basket/addprd', // добавить товар в корзину
        '~^basket/rmvprd/(\d+)$~i'                                     => 'frontend/basket/rmvprd/id/$1', // удалить товар из корзины
        '~^basket/checkout$~i'                                         => 'frontend/basket/checkout', // оформление заказа
        '~^basket/ajax/addprd$~i'                                      => 'frontend/basket/ajax/action/addprd', // добавить товар в корзину, XmlHttpRequest
        /* пользователи */
        '~^user/index$~i'                                              => 'frontend/user/index', // личный кабинет пользователя
        '~^user/login$~i'                                              => 'frontend/user/login', // страница авторизации пользователя
        '~^user/logout$~i'                                             => 'frontend/user/logout', // выйти из личного кабинета
        '~^user/reg$~i'                                                => 'frontend/user/reg', // страница регистрации нового пользователя
        '~^user/edit$~i'                                               => 'frontend/user/edit', // страница редактирования личных данных
        '~^user/allprof$~i'                                            => 'frontend/user/allprof', // страница со списком профилей
        '~^user/addprof$~i'                                            => 'frontend/user/addprof', // страница с формой для добавления профиля
        '~^user/editprof/(\d+)$~i'                                     => 'frontend/user/editprof/id/$1', // страница с формой для редактирования профиля
        '~^user/rmvprof/(\d+)$~i'                                      => 'frontend/user/rmvprof/id/$1', // удаление профиля
        '~^user/forgot$~i'                                             => 'frontend/user/forgot', // восстановление пароля
        '~^user/all-orders$~i'                                         => 'frontend/user/allorders', // история заказов
        '~^user/all-orders/page/(\d+)$~i'                              => 'frontend/user/allorders/page/$1', // история заказов, постраничная навигация
        '~^user/order/(\d+)$~i'                                        => 'frontend/user/order/id/$1', // подробная информация о заказе
        '~^user/repeat/(\d+)$~i'                                       => 'frontend/user/repeat/id/$1', // повторить заказ
        '~^user/ajax/profile/(\d+)$~i'                                 => 'frontend/user/ajax/id/$1', // получение профиля, XmlHttpRequest
        /* новости */
        '~^news$~i'                                                    => 'frontend/news/index', // главная страница новостей
        '~^news/page/(\d+)$~i'                                         => 'frontend/news/index/page/$1', // главная страница новостей, постраничная навигация
        '~^news/item/(\d+)$~i'                                         => 'frontend/news/item/id/$1', // отдельная новость
        '~^news/ctg/(\d+)$~i'                                          => 'frontend/news/category/id/$1', // список новостей выбранной категории
        '~^news/ctg/(\d+)/page/(\d+)$~i'                               => 'frontend/news/category/id/$1/page/$2', // список новостей выбранной категории, постраничная навигация
        /* страницы */
        '~^page/(\d+)$~i'                                              => 'frontend/page/index/id/$1', // страница сайта
        /* карта сайта */
        '~^sitemap$~i'                                                 => 'frontend/sitemap/index', // карта сайта
    )
);