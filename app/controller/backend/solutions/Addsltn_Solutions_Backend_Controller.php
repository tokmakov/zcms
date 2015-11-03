<?php
/**
 * Класс Addsltn_Solutions_Backend_Controller формирует страницу с формой для
 * добавления типового решения. Получает данные от модели Solutions_Backend_Model,
 * административная часть сайта
 */
class Addsltn_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного добавления нового типового решения и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления типового решения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addsltn_Solutions_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление типового решения прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию добавленного типового решения
                    $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу типовых решений
                    $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ошибок
                $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/addsltn'));
            }
        }

        $this->title = 'Новое решение. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionsBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionsBackendModel->getURL('backend/solutions/index')
            ),
            array(
                'name' => 'Категории',
                'url' => $this->solutionsBackendModel->getURL('backend/solutions/allctgs')
            ),
        );

        // получаем от модели массив категорий для возможности выбора родителя
        $categories = $this->solutionsBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->solutionsBackendModel->getURL('backend/solutions/addsltn'),
            // массив категорий
            'categories'  => $categories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addSolutionsItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addSolutionsItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addSolutionsItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если
     * были допущены ошибки, функция возвращает false; если ошибок нет,
     * функция добавляет новое типовое решение и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование типового решения
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 100));
        // мета-тег keywords
        $data['keywords']    = trim(utf8_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // краткое описание типового решения
        $data['excerpt']     = trim(utf8_substr($_POST['excerpt'], 0, 1000));
        // основное содержание типового решения
        $data['content1']    = trim($_POST['content1']);
        // коды товаров типового решения
        $data['codes']       = trim(utf8_substr($_POST['codes'], 0, 350));
        // дополнительное содержание типового решения (заключение)
        $data['content2']    = trim($_POST['content2']);

        // категория
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Краткое описание»';
        }
        if (empty($data['content1'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание»';
        }
        if (empty($data['codes'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Коды товаров»';
        } elseif ( ! preg_match('~^\d{6}( \d{6})*$~', $data['codes'])) {
            $errorMessage[] = 'Недопустимое значение поля «Коды товаров»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * администратором данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addSolutionsItemForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['category'];

        // обращаемся к модели для добавления нового типового решения
        $this->solutionsBackendModel->addSolution($data);

        return true;

    }

}