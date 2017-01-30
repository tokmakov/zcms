<?php
/**
 * Класс Edititem_Sitemap_Backend_Controller формирует страницу с формой для
 * редактирования элемента карты сайта, получает данные от модели Sitemap_Backend_Model,
 * административная часть сайта
 */
class Edititem_Sitemap_Backend_Controller extends Sitemap_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от моделей Sitemap_Backend_Model, Page_Backend_Model, Catalog_Backend_Model
     * данные, необходимые для формирования страницы с формой для редактирования элемента карты сайта
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sitemap_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Edititem_Sitemap_Backend_Controller
         */
        parent::input();

        // если не передан id элемента карты сайта или id элемента не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if (!$this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->sitemapBackendModel->getURL('backend/sitemap/edititem/id/' . $this->params['id']));
            } else {
                $this->redirect($this->sitemapBackendModel->getURL('backend/sitemap/index'));
            }
        }

        $this->title = 'Редактирование элемента карты сайта. ' . $this->title;

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

        // получаем от модели информацию об элементе карты сайта
        $sitemapItem = $this->sitemapBackendModel->getSitemapItem($this->params['id']);
        // если запрошенный элемент карты сайта не найден в БД
        if (empty($sitemapItem)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех элементов карты сайта для возможности выбора родителя
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
            'breadcrumbs'        => $breadcrumbs,
            // атрибут action тега form
            'action'             => $this->sitemapBackendModel->getURL('backend/sitemap/edititem/id/' . $this->params['id']),
            // уникальный идентификатор элемента карты сайта
            'id'                 => $this->params['id'],
            // наименование элемента карты сайта
            'name'               => $sitemapItem['name'],
            // CAP URL элемента карты сайта
            'capurl'             => $sitemapItem['capurl'],
            // родитель элемента карты сайта
            'parent'             => $sitemapItem['parent'],
            // массив всех элементов карты сайта для возможности выбора родителя
            'sitemapItems'       => $sitemapItems,
            // массив всех страниц сайта
            'pages'              => $pages,
            // массив категорий каталога верхнего уровня
            'catalogCategories'  => $catalogCategories,
            // массив категорий блога
            'blogCategories'     => $blogCategories,
            // массив категорий типовых решений
            'solutionCategories' => $solutionCategories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные данные
        // формы и массив сообщений об ошибках
        if ($this->issetSessionData('editSitemapItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSitemapItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSitemapItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки, функция
     * возвращает false; если ошибок нет, функция обновляет элемент карты сайта и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']   = trim(iconv_substr($_POST['name'], 0, 100)); // наименование элемента карты сайта
        $data['capurl'] = trim(iconv_substr($_POST['capurl'], 0, 100));  // CAP URL элемента карты сайта

        // родитель
        $data['parent'] = $this->sitemapBackendModel->getSitemapItemParent($this->params['id']);
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
        if ($data['parent'] == $this->params['id']) { // родителем элемента карты сайта назначен он сам?
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }
        // родителем элемента карты сайта назначен его потомок?
        if (in_array($data['parent'], $this->sitemapBackendModel->getAllChildItems($this->params['id']))) {
            $errorMessage[] = 'Недопустимое значание поля «Родитель»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editSitemapItemForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор элемента карты сайта

        // обращаемся к модели для обновления элемента карты сайта
        $this->sitemapBackendModel->updateSitemapItem($data);

        return true;

    }

}