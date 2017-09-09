<?php
defined('ZCMS') or die('Access denied');

// см. файл app/config/config.php
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
        '~^frontend/catalog/category/id/(\d+)(/group/\d+)?(/maker/\d+)?(/hit/1)?(/new/1)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'catalog/category/$1$2$3$4$5$6$7$8$9',
        // товары производителя
        '~^frontend/catalog/maker/id/(\d+)(/group/\d+)?(/hit/1)?(/new/1)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'catalog/maker/$1$2$3$4$5$6$7$8',
        // товары функциональной группы
        '~^frontend/catalog/group/id/(\d+)(/maker/\d+)?(/hit/1)?(/new/1)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'catalog/group/$1$2$3$4$5$6$7$8',
        // страница товара каталога
        '~^frontend/catalog/product/id/(\d+)$~i' =>
        'catalog/product/$1',
        // страница всех производителей
        '~^frontend/catalog/makers$~i' =>
        'catalog/all-makers',
        // страница всех брендов (производителей, отмеченных в админке как бренд)
        '~^frontend/catalog/brands$~i' =>
        'catalog/all-brands',
        // страница результатов поиска по производителям
        '~^frontend/catalog/makers/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/all-makers/query/$1',
        // страница всех функциональных групп
        '~^frontend/catalog/groups$~i' =>
        'catalog/all-groups',
        // страница результатов поиска по функционалу
        '~^frontend/catalog/groups/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/all-groups/query/$1',
        // страница поиска по каталогу
        '~^frontend/catalog/search$~i'=>
        'catalog/search',
        // страница результатов поиска по каталогу
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^frontend/catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'catalog/search/query/$1/page/$2',

        // подгрузка меню каталога, XmlHttpRequest
        '~^frontend/catalog/menu$~i' =>
        'catalog/menu',

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
        // добавить комментарий к товару из списка отложенных
        '~^frontend/wished/comment$~i' =>
        'wished/comment',

        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^frontend/compare/index$~i' =>
        'compare',
        // добавить товар в список сравнения
        '~^frontend/compare/addprd$~i' =>
        'compare/addprd',
        // удалить товар из списка сравнения
        '~^frontend/compare/rmvprd$~i' =>
        'compare/rmvprd',
        // удалить все товары из сравнения
        '~^frontend/compare/clear$~i' =>
        'compare/clear',

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
        // удалить все товары из корзины
        '~^frontend/basket/clear$~i' =>
        'basket/clear',
        // оформление заказа
        '~^frontend/basket/checkout$~i' =>
        'basket/checkout',
        // добавить товар в корзину
        '~^frontend/basket/upsell$~i' =>
        'basket/upsell',

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
        '~^frontend/user/allorders$~i' =>
        'user/all-orders',
        // история заказов, постраничная навигация
        '~^frontend/user/allorders/page/(\d+)$~i' =>
        'user/all-orders/page/$1',
        // подробная информация о заказе
        '~^frontend/user/order/id/(\d+)$~i' =>
        'user/order/$1',
        // повторить заказ
        '~^frontend/user/repeat/id/(\d+)$~i' =>
        'user/repeat/$1',
        // получение профиля для страницы оформления заказа
        '~^frontend/user/profile/id/(\d+)$~i' =>
        'user/profile/$1',

        /*
         * блог
         */
        // главная страница блога
        '~^frontend/blog/index$~i' =>
        'blog',
        // главная страница блога, постраничная навигация
        '~^frontend/blog/index/page/(\d+)$~i' =>
        'blog/page/$1',
        // отдельный пост блога
        '~^frontend/blog/post/id/(\d+)$~i' =>
        'blog/item/$1',
        // список постов блога выбранной категории
        '~^frontend/blog/category/id/(\d+)$~i' =>
        'blog/category/$1',
        // список постов блога выбранной категории, постраничная навигация
        '~^frontend/blog/category/id/(\d+)/page/(\d+)$~i' =>
        'blog/category/$1/page/$2',

        /*
         * типовые решения
         */
        // главная страница типовых решений
        '~^frontend/solution/index$~i' =>
        'solutions',
        // главная страница типовых решений, постраничная навигация
        '~^frontend/solution/index/page/(\d+)$~i' =>
        'solutions/page/$1',
        // отдельное типовое решение
        '~^frontend/solution/item/id/(\d+)$~i' =>
        'solutions/item/$1',
        // список типовых решений выбранной категории
        '~^frontend/solution/category/id/(\d+)$~i' =>
        'solutions/category/$1',
        // список типовых решений выбранной категории, постраничная навигация
        '~^frontend/solution/category/id/(\d+)/page/(\d+)$~i' =>
        'solutions/category/$1/page/$2',
        // добавление товаров типового решения в корзину
        '~^frontend/solution/basket/id/(\d+)$~i' =>
        'solutions/basket/$1',

        /*
         * статьи
         */
        // список всех статей
        '~^frontend/article/index$~i' =>
        'articles',
        // список всех статей, постраничная навигация
        '~^frontend/article/index/page/(\d+)$~i' =>
        'articles/page/$1',
        // отдельная статья
        '~^frontend/article/item/id/(\d+)$~i' =>
        'articles/item/$1',
        // список статей выбранной категории
        '~^frontend/article/category/id/(\d+)$~i' =>
        'articles/category/$1',
        // список статей выбранной категории, постраничная навигация
        '~^frontend/article/category/id/(\d+)/page/(\d+)$~i' =>
        'articles/category/$1/page/$2',

        /*
         * распродажа
         */
        '~^frontend/sale/index$~i' =>
        'sale',

        /*
         * рейтинг продаж
         */
        '~^frontend/rating/index$~i' =>
        'rating',

        /*
         * партнеры компании
         */
        '~^frontend/partner/index$~i' =>
        'partners',

        /*
         * бренды
         */
        '~^frontend/brand/index$~i' =>
        'brands',

        /*
         * вакансии компании
         */
        '~^frontend/vacancy/index$~i' =>
        'vacancies',

        /*
         * карта сайта
         */
        '~^frontend/sitemap/index$~i' =>
        'sitemap',

        /*
         * обмен данными с 1С
         */
        '~^frontend/exchange/neworders/access/([a-f0-9]{32})$~i' =>
        'exchange/get-new-orders/access/$1',
        '~^frontend/exchange/order/id/(\d+)/access/([a-f0-9]{32})$~i' =>
        'exchange/get-order/id/$1/access/$2',
        '~^frontend/exchange/setstatus/order/(\d+)/status/(\d+)/access/([a-f0-9]{32})$~i' =>
        'exchange/set-order-status/order/$1/status/$2/access/$3',
    ),

    'sef2cap' => array( // Search Engines Friendly => Controller/Action/Params
        /*
         * главная страница сайта
         */
        '~^$~' =>
        'frontend/index/index',

        /*
         * каталог
         */
        // главная страница каталога
        '~^catalog$~i' =>
        'frontend/catalog/index',
        // категория каталога
        '~^catalog/category/(\d+)(/group/\d+)?(/maker/\d+)?(/hit/1)?(/new/1)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'frontend/catalog/category/id/$1$2$3$4$5$6$7$8$9',
        // товары производителя
        '~^catalog/maker/(\d+)(/group/\d+)?(/hit/1)?(/new/1/)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'frontend/catalog/maker/id/$1$2$3$4$5$6$7$8',
        // товары функциональной группы
        '~^catalog/group/(\d+)(/maker/\d+)(/hit/1)?(/new/1/)?(/param/\d+\.\d+(?:-\d+\.\d+)*)?(/sort/\d)?(/perpage/\d+)?(/page/\d+)?$~i' =>
        'frontend/catalog/group/id/$1$2$3$4$5$6$7$8',
        // страница товара каталога
        '~^catalog/product/(\d+)$~i' =>
        'frontend/catalog/product/id/$1',
        // страница всех производителей
        '~^catalog/all-makers$~i' =>
        'frontend/catalog/makers',
        // страница всех брендов (производителей, отмеченных в админке как бренд)
        '~^catalog/all-brands$~i' =>
        'frontend/catalog/brands',
        // страница результатов поиска по производителям
        '~^catalog/all-makers/query/([a-z0-9%_.-]+)$~i' =>
        'frontend/catalog/makers/query/$1',
        // страница всех функциональных групп
        '~^catalog/all-groups$~i' =>
        'frontend/catalog/groups',
        // страница результатов поиска по функционалу
        '~^catalog/all-groups/query/([a-z0-9%_.-]+)$~i' =>
        'frontend/catalog/groups/query/$1',
        // страница поиска по каталогу
        '~^catalog/search$~i' =>
        'frontend/catalog/search',
        // страница результатов поиска по каталогу
        '~^catalog/search/query/([a-z0-9%_.-]+)$~i' =>
        'frontend/catalog/search/query/$1',
        // страница результатов поиска по каталогу, постраничная навигация
        '~^catalog/search/query/([a-z0-9%_.-]+)/page/(\d+)$~i' =>
        'frontend/catalog/search/query/$1/page/$2',

        // подгрузка меню каталога, XmlHttpRequest
        '~^catalog/menu$~i' =>
        'frontend/catalog/menu',

        /*
         * просмотренные товары
         */
        // страница со списком всех просмотренных товаров
        '~^viewed$~i' =>
        'frontend/viewed/index',
        // просмотренные товары, постраничная навигация
        '~^viewed/page/(\d+)$~i' =>
        'frontend/viewed/index/page/$1',

        /*
         * отложенные товары
         */
        // страница со списком всех отложенных товаров
        '~^wished$~i' =>
        'frontend/wished/index',
        // отложенные товары, постраничная навигация
        '~^wished/page/(\d+)$~i' =>
        'frontend/wished/index/page/$1',
        // добавить товар в список отложенных
        '~^wished/addprd$~i' =>
        'frontend/wished/addprd',
        // удалить товар из списка отложенных
        '~^wished/rmvprd$~i' =>
        'frontend/wished/rmvprd',
        // добавить комментарий к товару из списка отложенных
        '~^wished/comment$~i' =>
        'frontend/wished/comment',

        /*
         * товары для сравнения
         */
        // страница со списком товаров для сравнения
        '~^compare$~i' =>
        'frontend/compare/index',
        // добавить товар в список сравнения
        '~^compare/addprd$~i' =>
        'frontend/compare/addprd',
        // удалить товар из списка сравнения
        '~^compare/rmvprd$~i' =>
        'frontend/compare/rmvprd',
        // удалить все товары из сравнения
        '~^compare/clear$~i' =>
        'frontend/compare/clear',

        /*
         * корзина
         */
        // страница покупательской корзины
        '~^basket$~i' =>
        'frontend/basket/index',
        // добавить товар в корзину
        '~^basket/addprd$~i' =>
        'frontend/basket/addprd',
        // удалить товар из корзины
        '~^basket/rmvprd/(\d+)$~i' =>
        'frontend/basket/rmvprd/id/$1',
        // удалить все товары из корзины
        '~^basket/clear$~i' =>
        'frontend/basket/clear',
        // оформление заказа
        '~^basket/checkout$~i' =>
        'frontend/basket/checkout',
        // добавить товар в корзину из рекомендованных
        '~^basket/upsell$~i' =>
        'frontend/basket/upsell',

        /*
         * пользователи
         */
        // личный кабинет пользователя
        '~^user/index$~i' =>
        'frontend/user/index',
        // страница авторизации пользователя
        '~^user/login$~i' =>
        'frontend/user/login',
        // выйти из личного кабинета
        '~^user/logout$~i' =>
        'frontend/user/logout',
        // страница регистрации нового пользователя
        '~^user/reg$~i' =>
        'frontend/user/reg',
        // страница редактирования личных данных
        '~^user/edit$~i' =>
        'frontend/user/edit',
        // страница со списком профилей
        '~^user/allprof$~i' =>
        'frontend/user/allprof',
        // страница с формой для добавления профиля
        '~^user/addprof$~i' =>
        'frontend/user/addprof',
        // страница с формой для редактирования профиля
        '~^user/editprof/(\d+)$~i' =>
        'frontend/user/editprof/id/$1',
        // удаление профиля
        '~^user/rmvprof/(\d+)$~i' =>
        'frontend/user/rmvprof/id/$1',
        // восстановление пароля
        '~^user/forgot$~i' =>
        'frontend/user/forgot',
        // история заказов
        '~^user/all-orders$~i' =>
        'frontend/user/allorders',
        // история заказов, постраничная навигация
        '~^user/all-orders/page/(\d+)$~i' =>
        'frontend/user/allorders/page/$1',
        // подробная информация о заказе
        '~^user/order/(\d+)$~i' =>
        'frontend/user/order/id/$1',
        // повторить заказ
        '~^user/repeat/(\d+)$~i' =>
        'frontend/user/repeat/id/$1',
        // получение профиля для страницы оформления заказа
        '~^user/profile/(\d+)$~i' =>
        'frontend/user/profile/id/$1',
        // получение данных последнего заказа для оформления нового заказа
        '~^user/customer$~i' =>
        'frontend/user/customer',

        /*
         * блог
         */
        // главная страница блога
        '~^blog$~i' =>
        'frontend/blog/index',
        // главная страница блога, постраничная навигация
        '~^blog/page/(\d+)$~i' =>
        'frontend/blog/index/page/$1',
        // отдельный пост блога
        '~^blog/item/(\d+)$~i' =>
        'frontend/blog/post/id/$1',
        // список постов блога выбранной категории
        '~^blog/category/(\d+)$~i' =>
        'frontend/blog/category/id/$1',
        // список постов блога выбранной категории, постраничная навигация
        '~^blog/category/(\d+)/page/(\d+)$~i' =>
        'frontend/blog/category/id/$1/page/$2',

        /*
         * типовые решения
         */
        // главная страница типовых решений
        '~^solutions$~i' =>
        'frontend/solution/index',
        // главная страница типовых решений, постраничная навигация
        '~^solutions/page/(\d+)$~i' =>
        'frontend/solution/index/page/$1',
        // отдельное типовое решение
        '~^solutions/item/(\d+)$~i' =>
        'frontend/solution/item/id/$1',
        // список типовых решений выбранной категории
        '~^solutions/category/(\d+)$~i' =>
        'frontend/solution/category/id/$1',
        // список типовых решений выбранной категории, постраничная навигация
        '~^solutions/category/(\d+)/page/(\d+)$~i' =>
        'frontend/solution/category/id/$1/page/$2',
        // добавление товаров типового решения в корзину
        '~^solutions/basket/(\d+)$~i' =>
        'frontend/solution/basket/id/$1',

        /*
         * статьи
         */
        // список всех статей
        '~^articles$~i' =>
        'frontend/article/index',
        // список всех статей, постраничная навигация
        '~^articles/page/(\d+)$~i' =>
        'frontend/article/index/page/$1',
        // отдельная статья
        '~^articles/item/(\d+)$~i' =>
        'frontend/article/item/id/$1',
        // список статей выбранной категории
        '~^articles/category/(\d+)$~i' =>
        'frontend/article/category/id/$1',
        // список статей выбранной категории, постраничная навигация
        '~^articles/category/(\d+)/page/(\d+)$~i' =>
        'frontend/article/category/id/$1/page/$2',

        /*
         * распродажа
         */
        '~^sale$~i' =>
        'frontend/sale/index$1',

        /*
         * рейтинг продаж
         */
        '~^rating$~i' =>
        'frontend/rating/index$1',

        /*
         * партнеры компании
         */
        '~^partners$~i' =>
        'frontend/partner/index$1',

        /*
         * бренды
         */
        '~^brands$~i' =>
        'frontend/brand/index$1',

        /*
         * вакансии компании
         */
        '~^vacancies$~i' =>
        'frontend/vacancy/index$1',

        /*
         * карта сайта
         */
        '~^sitemap$~i' =>
        'frontend/sitemap/index',

        /*
         * обмен данными с 1С
         */
        '~^exchange/get-new-orders/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/neworders/access/$1',
        '~^exchange/get-order/id/(\d+)/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/order/id/$1/access/$2',
        '~^exchange/set-order-status/order/(\d+)/status/(\d+)/access/([a-f0-9]{32})$~i' =>
        'frontend/exchange/setstatus/order/$1/status/$2/access/$3',
    )
);