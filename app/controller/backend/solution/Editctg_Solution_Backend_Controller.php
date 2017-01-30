<?php
/**
 * Класс Editctg_Solution_Backend_Controller формирует страницу с формой для
 * редактирования категории. Получает данные от модели Solution_Backend_Model,
 * административная часть сайта
 */
class Editctg_Solution_Backend_Controller extends Solution_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editctg_Solution_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id']))) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ошибок
                $this->redirect($this->solutionBackendModel->getURL('backend/solution/editctg/id/' . $this->params['id']));
            } else {
                // перенаправляем администратора на страницу со списком всех категорий
                $this->redirect($this->solutionBackendModel->getURL('backend/solution/allctgs'));
            }
        }

        $this->title = 'Редактирование категории. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->solutionBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->solutionBackendModel->getURL('backend/solution/index'), 'name' => 'Типовые решения'),
            array('url' => $this->solutionBackendModel->getURL('backend/solution/allctgs'), 'name' => 'Категории'),
        );

        // получаем от модели информацию о категории
        $category = $this->solutionBackendModel->getCategory($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($category)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->solutionBackendModel->getURL('backend/solution/editctg/id/' . $this->params['id']),
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории
            'name'        => $category['name'],
            // мета-тег keywords
            'keywords'    => $category['keywords'],
            // мета-тег description
            'description' => $category['description'],
            // краткое описание категории
            'excerpt'     => $category['excerpt'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editSolutionCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSolutionCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSolutionCategoryForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет категорию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование категории
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 100));
        // мета-тег keywords
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // краткое описание категории
        $data['excerpt']     = trim(iconv_substr($_POST['excerpt'], 0, 1000));

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Краткое описание»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * администратором данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editSolutionCategoryForm', $data);
            return false;
        }

        // уникальный идентификатор категории
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления категории
        $this->solutionBackendModel->updateCategory($data);

        return true;

    }

}