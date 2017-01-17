<?php
/**
 * Класс Additem_Sitemap_Backend_Controller формирует страницу с формой
 * для добавления нового элемента карты сайта, получает данные от модели
 * Sitemap_Backend_Model, административная часть сайта
 */
class Additem_Sitemap_Backend_Controller extends Sitemap_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели Sitemap_Backend_Model данные, необходимые для
     * формирования страницы с формой для добавления нового элемента карты сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sitemap_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Additem_Sitemap_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ишибок
                $this->redirect($this->sitemapBackendModel->getURL('backend/sitemap/additem'));
            } else {
                $this->redirect($this->sitemapBackendModel->getURL('backend/sitemap/index'));
            }
        }

        $this->title = 'Новый элемент карты сайта. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->sitemapBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Карта сайта',
                'url'  => $this->sitemapBackendModel->getURL('backend/sitemap/index')
            ),
        );

        // получаем от модели массив всех элементов карты сайта
        // для возможности выбора родителя
        $sitemapItems = $this->sitemapBackendModel->getSitemapItems();

        // получаем от модели массив всех страниц сайта
        $pages = $this->sitemapBackendModel->getAllPages();

        // получаем от модели массив категорий каталога верхнего уровня
        $catalogCategories = $this->sitemapBackendModel->getRootCategories();

        // получаем массив всех категорий блога
        $blogCategories = $this->sitemapBackendModel->getBlogCategories();

        // получаем массив всех категорий типовых решений
        $solutionCategories = $this->sitemapBackendModel->getSolutionCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'         => $breadcrumbs,
            // атрибут action тега form
            'action'              => $this->sitemapBackendModel->getURL('backend/sitemap/additem'),
            // массив всех элементов карты сайта для возможности выбора родителя
            'sitemapItems'           => $sitemapItems,
            // массив всех страниц сайта
            'pages'               => $pages,
            // массив категорий каталога верхнего уровня
            'catalogCategories'   => $catalogCategories,
            // массив категорий блога
            'blogCategories'      => $blogCategories,
            // массив категорий типовых решений
            'solutionCategories'  => $solutionCategories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addSitemapItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addSitemapItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addSitemapItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены
     * ошибки, функция возвращает false; если ошибок нет, функция добавляет элемент карты
     * сайта и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']   = trim(iconv_substr($_POST['name'], 0, 100)); // наименование элемента карты сайта
        $data['capurl'] = trim(iconv_substr($_POST['capurl'], 0, 100));  // CAP URL элемента карты сайта

        // родитель
        $data['parent'] = 0;
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = $_POST['parent'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['capurl'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «CAP URL»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addSitemapItemForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового элемента карты сайта
        $this->sitemapBackendModel->addSitemapItem($data);

        return true;
    }

}