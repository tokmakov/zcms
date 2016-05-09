<?php
/**
 * Класс Editsltn_Solution_Backend_Controller формирует страницу с формой для
 * редактирования типового решения. Получает данные от модели Solution_Backend_Model,
 * административная часть сайта
 */
class Editsltn_Solution_Backend_Controller extends Solution_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного обновления типового решения и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования типового решения
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editsltn_Solution_Backend_Controller
         */
        parent::input();

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление типового решения прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию отредактированного типового решения
                    $this->redirect($this->solutionBackendModel->getURL('backend/solution/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу типовых решений
                    $this->redirect($this->solutionBackendModel->getURL('backend/solution/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ошибок
                $this->redirect($this->solutionBackendModel->getURL('backend/solution/editsltn/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование решения. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionBackendModel->getURL('backend/solution/index')
            ),
            array(
                'name' => 'Категории',
                'url' => $this->solutionBackendModel->getURL('backend/solution/allctgs')
            ),
        );

        // получаем от модели информацию о типовом решении
        $solution = $this->solutionBackendModel->getSolution($this->params['id']);
        // если запрошенное типовое решение не найдено в БД
        if (empty($solution)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив категорий для возможности выбора родителя
        $categories = $this->solutionBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->solutionBackendModel->getURL('backend/solution/editsltn/id/' . $this->params['id']),
            // наименование типового решения
            'name'        => $solution['name'],
            // категория типового решения
            'category'    => $solution['category'],
            // массив категорий
            'categories'  => $categories,
            // мета-тег keywords
            'keywords'    => $solution['keywords'],
            // мета-тег description
            'description' => $solution['description'],
            // краткое описание типового решения
            'excerpt'     => $solution['excerpt'],
            // основное содержание типового решения
            'content1'    => $solution['content1'],
            // дополнительное содержание типового решения
            'content2'    => $solution['content2'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editSolutionItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSolutionItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSolutionItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если
     * были допущены ошибки, функция возвращает false; если ошибок нет,
     * функция обновляет типовое решение и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование типового решения
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 150));
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

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * администратором данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editSolutionItemForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['category'];

        // уникальный идентификатор типового решения
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления типового решения
        $this->solutionBackendModel->updateSolution($data);

        return true;

    }

}