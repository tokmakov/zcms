<?php
/**
 * Класс Edititem_Article_Backend_Controller формирует страницу с формой для
 * редактирования статьи, обновляет статью (запись в таблице `articles` БД),
 * получает данные от модели Фкешсду_Backend_Model
 */
class Edititem_Article_Backend_Controller extends Article_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования статьи
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Article_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Edititem_Article_Backend_Controller
         */
        parent::input();

        // если не передан id статьи или id статьи не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->articleBackendModel->getURL('backend/article/edititem/id/' . $this->params['id']));
            } else {
                $this->redirect($this->articleBackendModel->getURL('backend/article/index'));
            }
        }

        $this->title = 'Редактирование статьи. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->articleBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Статьи',
                'url'  => $this->articleBackendModel->getURL('backend/article/index')
            ),
        );

        // получаем от модели информацию о статье
        $article = $this->articleBackendModel->getArticle($this->params['id']);
        // если запрошенная статья не найдена в БД
        if (empty($article)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем информацию о файлах
        $files = array();
        if (is_dir('files/article/' . $this->params['id'])) {
            $temp = scandir('files/article/' . $this->params['id']);
            foreach ($temp as $file) {
                if ($file == '.' || $file == '..' || $file == $this->params['id'] . '.jpg') {
                    continue;
                }
                $files[] = $file;
            }
        }

        // получаем от модели массив категорий статей, для возможности выбора родителя
        $categories = $this->articleBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->articleBackendModel->getURL('backend/article/edititem/id/' . $this->params['id']),
            // уникальный идентификатор статьи
            'id'          => $this->params['id'],
            // категория статьи
            'category'    => $article['ctg_id'],
            // массив категорий статей
            'categories'  => $categories,
            // заголовок статьи
            'name'        => $article['name'],
            // мета-тег keywords
            'keywords'    => $article['keywords'],
            // мета-тег description
            'description' => $article['description'],
            // анонс статьи
            'excerpt'     => $article['excerpt'],
            // содержание статьи
            'body'        => $article['body'],
            // дата добавления
            'date'        => $article['date'],
            // время добавления
            'time'        => $article['time'],
            // загруженные файлы
            'files'       => $files,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editArticleItemForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editArticleItemForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editArticleItemForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет статью и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250)); // заголовок статьи
        $data['excerpt']     = trim(iconv_substr($_POST['excerpt'], 0, 1000)); // анонс статьи
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250)); // мета-тег keywords
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250)); // мета-тег description
        $data['description'] = str_replace('"', '', $data['description']);
        $data['body']        = trim($_POST['body']); // содержание статьи
        $data['date']        = $_POST['date']; // дата
        $data['time']        = $_POST['time']; // время

        // категория статьи
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок статьи»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Анонс статьи»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание статьи»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editArticleItemForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор статьи

        // обращаемся к модели для обновления статьи
        $this->articleBackendModel->updateArticle($data);

        return true;

    }

}
