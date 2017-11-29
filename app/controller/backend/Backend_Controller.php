<?php
/**
 * Абстрактный класс Backend_Controller, родительский для всех контроллеров
 * административной части сайта
 */
abstract class Backend_Controller extends Base_Controller {

    /**
     * администратор сайта авторизован?
     */
    protected $authAdmin = false;

    /**
     * экземпляр класса модели для работы с администратором сайта
     */
    protected $adminBackendModel;

    /**
     * экземпляр класса модели для работы со статьями
     */
    protected $articleBackendModel;

    /**
     * экземпляр класса модели для работы с баннерами
     */
    protected $bannerBackendModel;

    /**
     * экземпляр класса модели для работы с блогом
     */
    protected $blogBackendModel;

    /**
     * экземпляр класса модели для работы с кэшем, класс-пустышка,
     * только для доступа к родительским свойствам и методам
     */
    protected $cacheBackendModel;

    /**
     * экземпляр класса модели для работы с каталогом
     */
    protected $catalogBackendModel;

    /**
     * экземпляр класса модели для работы с фильтром товаров
     */
    protected $filterBackendModel;

    /**
     * экземпляр класса модели для работы с главной страницей
     * административной части сайта
     */
    protected $indexBackendModel;

    /**
     * экземпляр класса модели для работы с главным меню
     * общедоступной части сайта
     */
    protected $menuBackendModel;

    /**
     * экземпляр класса модели для работы с заказами
     */
    protected $orderBackendModel;

    /**
     * экземпляр класса модели для работы со страницами
     */
    protected $pageBackendModel;

    /**
     * экземпляр класса модели для работы с партнерами компании
     */
    protected $partnerBackendModel;

    /**
     * экземпляр класса модели для работы со рейтингом продаж
     */
    protected $ratingBackendModel;

    /**
     * экземпляр класса модели для работы с товарами со скидкой
     */
    protected $saleBackendModel;

    /**
     * экземпляр класса модели для работы с картой сайта
     */
    protected $sitemapBackendModel;

    /**
     * экземпляр класса модели для работы с типовыми решениями
     */
    protected $solutionBackendModel;

    /**
     * экземпляр класса модели для работы с главной страницей
     * (витриной) общедоступной части сайта
     */
    protected $startBackendModel;

    /**
     * экземпляр класса модели для работы с пользователями
     */
    protected $userBackendModel;

    /**
     * экземпляр класса модели для работы с вакансиями
     */
    protected $vacancyBackendModel;


    public function __construct($params = null) {

        parent::__construct($params);

        // экземпляр класса модели для работы с администратором сайта
        $this->adminBackendModel =
            isset($this->register->adminBackendModel) ? $this->register->adminBackendModel : new Admin_Backend_Model();

        // экземпляр класса модели для работы со статьями
        $this->articleBackendModel =
            isset($this->register->articleBackendModel) ? $this->register->articleBackendModel : new Article_Backend_Model();

        // экземпляр класса модели для работы с баннерами
        $this->bannerBackendModel =
            isset($this->register->bannerBackendModel) ? $this->register->bannerBackendModel : new Banner_Backend_Model();

        // экземпляр класса модели для работы с блогом
        $this->blogBackendModel =
            isset($this->register->blogBackendModel) ? $this->register->blogBackendModel : new Blog_Backend_Model();

        // экземпляр класса модели для работы с кэшем, класс-пустышка
        $this->cacheBackendModel =
            isset($this->register->cacheBackendModel) ? $this->register->cacheBackendModel : new Cache_Backend_Model();

        // экземпляр класса модели для работы с каталогом
        $this->catalogBackendModel =
            isset($this->register->catalogBackendModel) ? $this->register->catalogBackendModel : new Catalog_Backend_Model();

        // экземпляр класса модели для работы с фильтром товаров
        $this->filterBackendModel =
            isset($this->register->filterBackendModel) ? $this->register->filterBackendModel : new Filter_Backend_Model();

        // экземпляр класса модели для работы с главной страницей админки
        $this->indexBackendModel =
            isset($this->register->indexBackendModel) ? $this->register->indexBackendModel : new Index_Backend_Model();

        // экземпляр класса модели для работы с главным меню
        $this->menuBackendModel =
            isset($this->register->menuBackendModel) ? $this->register->menuBackendModel : new Menu_Backend_Model();

        // экземпляр класса модели для работы с заказами
        $this->orderBackendModel =
            isset($this->register->orderBackendModel) ? $this->register->orderBackendModel : new Order_Backend_Model();

        // экземпляр класса модели для работы со страницами
        $this->pageBackendModel =
            isset($this->register->pageBackendModel) ? $this->register->pageBackendModel : new Page_Backend_Model();

        // экземпляр класса модели для работы с партнерами компании
        $this->partnerBackendModel =
            isset($this->register->partnerBackendModel) ? $this->register->partnerBackendModel : new Partner_Backend_Model();

        // экземпляр класса модели для работы с товарами рейтинга продаж
        $this->ratingBackendModel =
            isset($this->register->ratingBackendModel) ? $this->register->ratingBackendModel : new Rating_Backend_Model();

        // экземпляр класса модели для работы с товарами со скидкой
        $this->saleBackendModel =
            isset($this->register->saleBackendModel) ? $this->register->saleBackendModel : new Sale_Backend_Model();

        // экземпляр класса модели для работы с картой сайта
        $this->sitemapBackendModel =
            isset($this->register->sitemapBackendModel) ? $this->register->sitemapBackendModel : new Sitemap_Backend_Model();

        // экземпляр класса модели для работы с типовыми решениями
        $this->solutionBackendModel =
            isset($this->register->solutionBackendModel) ? $this->register->solutionBackendModel : new Solution_Backend_Model();

        // экземпляр класса модели для работы с витриной
        $this->startBackendModel =
            isset($this->register->startBackendModel) ? $this->register->startBackendModel : new Start_Backend_Model();

        // экземпляр класса модели для работы с пользователями
        $this->userBackendModel =
            isset($this->register->userBackendModel) ? $this->register->userBackendModel : new User_Backend_Model();

        // экземпляр класса модели для работы с вакансиями
        $this->vacancyBackendModel =
            isset($this->register->vacancyBackendModel) ? $this->register->vacancyBackendModel : new Vacancy_Backend_Model();

        // администратор сайта авторизован?
        $this->authAdmin = $this->adminBackendModel->isAuthAdmin();

        // если администратор не авторизован, перенаправляем на страницу авторизации
        if ( ! $this->authAdmin) {
            $this->redirect($this->adminBackendModel->getURL('backend/admin/login'));
        }

    }

    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Backend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Base_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Backend_Controller
         */
        parent::input();

        /*
         * устанавливаем значения по умолчанию для всех переменных, необходимых
         * для формирования страниц административной части сайта, потом
         * переопределяем их значения в дочерних классах, если необходимо
         */
        $this->title = 'Панель управления';

        /*
         * переменные, которые будет переданы в шаблон главного меню, файл
         * menu.php, административная часть сайта
         */
        $this->menuVars = array(
            array(
                'name' => 'Главная',
                'url'  => $this->indexBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Витрина',
                'url'  => $this->startBackendModel->getURL('backend/start/index')
            ),
            array(
                'name' => 'Меню',
                'url'  => $this->menuBackendModel->getURL('backend/menu/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->catalogBackendModel->getURL('backend/catalog/index')
            ),
            array(
                'name' => 'Фильтр',
                'url'  => $this->filterBackendModel->getURL('backend/filter/index')
            ),
            array(
                'name' => 'Распродажа',
                'url'  => $this->saleBackendModel->getURL('backend/sale/index')
            ),
            array(
                'name' => 'Рейтинг',
                'url'  => $this->ratingBackendModel->getURL('backend/rating/index')
            ),
            array(
                'name' => 'Партнеры',
                'url'  => $this->partnerBackendModel->getURL('backend/partner/index')
            ),
            array(
                'name' => 'Вакансии',
                'url'  => $this->vacancyBackendModel->getURL('backend/vacancy/index')
            ),
            array(
                'name' => 'Решения',
                'url'  => $this->solutionBackendModel->getURL('backend/solution/index')
            ),
            array(
                'name' => 'Блог',
                'url'  => $this->blogBackendModel->getURL('backend/blog/index')
            ),
            array(
                'name' => 'Заказы',
                'url'  => $this->orderBackendModel->getURL('backend/order/index')
            ),
            array(
                'name' => 'Пользователи',
                'url'  => $this->userBackendModel->getURL('backend/user/index')
            ),
            array(
                'name' => 'Страницы',
                'url'  => $this->pageBackendModel->getURL('backend/page/index')
            ),
            array(
                'name' => 'Баннеры',
                'url'  => $this->bannerBackendModel->getURL('backend/banner/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleBackendModel->getURL('backend/article/index')
            ),
            array(
                'name' => 'Карта',
                'url'  => $this->sitemapBackendModel->getURL('backend/sitemap/index')
            ),
            array(
                'name' => 'Кэш',
                'url' => $this->cacheBackendModel->getURL('backend/cache/index')
            ),
        );

        $this->headerVars = array(
            'logoutUrl' => $this->adminBackendModel->getURL('backend/admin/logout'),
        );

    }

    /**
     * Функция формирует html-код отдельных частей страницы (меню,
     * основной контент, левая и правая колонка, подвал сайта и т.п.)
     */
    protected function output() {

        // получаем html-код тега <head>
        $this->headContent = $this->render(
            $this->headTemplateFile,
            array(
                'title'    => $this->title,
                'cssFiles' => $this->cssFiles,
                'jsFiles'  => $this->jsFiles,
            )
        );

        // получаем html-код шапки сайта
        $this->headerContent = $this->render(
            $this->headerTemplateFile,
            $this->headerVars
        );

        // получаем html-код главного меню
        $this->menuContent = $this->render(
            $this->menuTemplateFile,
            array('menu' => $this->menuVars)
        );

        // получаем html-код центральной колонки (основной контент)
        $this->centerContent = $this->render(
            $this->centerTemplateFile,
            $this->centerVars
        );

        // получаем html-код подвала страницы
        $this->footerContent = $this->render(
            $this->footerTemplateFile,
            $this->footerVars
        );

        /*
         * html-код отдельных частей страницы получен, теперь формируем
         * всю страницу целиком
         */
        $this->pageContent = $this->render(
            $this->wrapperTemplateFile,
            array(
                'headContent'   => $this->headContent,
                'headerContent' => $this->headerContent,
                'menuContent'   => $this->menuContent,
                'centerContent' => $this->centerContent,
                'leftContent'   => $this->leftContent,
                'rightContent'  => $this->rightContent,
                'footerContent' => $this->footerContent
            )
        );

    }

}