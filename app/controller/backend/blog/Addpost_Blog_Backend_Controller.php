<?php
/**
 * Класс Addpost_Blog_Backend_Controller формирует страницу с формой для
 * добавления поста блога, получает данные от модели Blog_Backend_Model,
 * административная часть сайта
 */
class Addpost_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления поста блога
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Blog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addpost_Blog_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            /*
             * Форма имеет две кнопки отправки данных:
             * 1. Кнопка <input type="submit" name="submit" value="Сохранить" />
             * 2. Кнопка <input type="submit" name="upload" value="Загрузить" />
             *
             * При нажатии первой кнопки, вызывается метод validateForm(), который
             * проверяет введенные данные, и, если все в порядке, вызывает метод
             * модели addPost() для добавления новой записи (поста) блога. Если
             * были допущены ошибки при заполнении формы, введенные данные сохраняются
             * в сессии, чтобы после редиректа опять показать форму, заполненную
             * введенными ранее данными и сообщения об ошибках.
             *
             * При нажатии второй кнопки, вызывается метод uploadFiles(), который
             * загружает на сервер выбранные администратором файлы. Введенные данные
             * сохраняются в сессии, чтобы администратору не пришлось заполнять поля
             * формы повторно.
             */
            if (isset($_POST['submit'])) { // нажата первая кнопка
                if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                    $this->redirect($this->blogBackendModel->getURL('backend/blog/addpost'));
                } else {
                    $this->redirect($this->blogBackendModel->getURL('backend/blog/index'));
                }
            }
            if (isset($_POST['upload'])) { // нажата вторая кнопка
                $this->uploadFiles();
                $this->redirect($this->blogBackendModel->getURL('backend/blog/addpost'));
            }
        }

        $this->title = 'Добавить пост. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->blogBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Блог',
                'url'  => $this->blogBackendModel->getURL('backend/blog/index')
            ),
        );

        // получаем от модели массив категорий постов, для возможности выбора
        $categories = $this->blogBackendModel->getCategories();

        // получаем от модели массив массив директорий и файлов
        $folders = $this->blogBackendModel->getFoldersAndFiles();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->blogBackendModel->getURL('backend/blog/addpost'),
            // массив категорий для возможности выбора
            'categories'  => $categories,
            // массив директорий и файлов
            'folders'     => $folders,
            // дата добавления поста
            'date'        => date('d.m.Y'),
            // время добавления поста
            'time'        => date('H:i:s'),
        );
        // если на предыдущем этапе администратор загружал файлы, передаем в шаблон
        // сохраненные в сессии данные формы
        if ($this->issetSessionData('uploadBlogPostForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('uploadBlogPostForm');
            $this->unsetSessionData('uploadBlogPostForm');
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и сохраненные в сессии данные формы
        if ($this->issetSessionData('addBlogPostForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addBlogPostForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addBlogPostForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция добавляет
     * новую запись (пост) блога и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // заголовок поста
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250));
        // анонс поста
        $data['excerpt']     = trim(iconv_substr($_POST['excerpt'], 0, 1000));
        // мета-тег keywords
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // содержание поста
        $data['body']        = trim($_POST['body']);
        // дата добавления
        $data['date']        = $_POST['date'];
        // время добавления
        $data['time']        = $_POST['time'];

        // категория поста
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Заголовок»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['excerpt'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Анонс»';
        }
        if (empty($data['body'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Содержание»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addBlogPostForm', $data);
            return false;
        }

        // обращаемся к модели для добавления поста
        $this->blogBackendModel->addPost($data);

        return true;

    }

    /**
     * Функция загружает на сервер выбранные администратором файлы и сохраняет в
     * сессии введенные данные, чтобы администратору не пришлось заполнять поля
     * формы повторно
     */
    private function uploadFiles() {

        /*
         * сохраняем введенные данные в сессии
         */

        // заголовок поста
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250));
        // анонс поста
        $data['excerpt']     = trim(iconv_substr($_POST['excerpt'], 0, 1000));
        // мета-тег keywords
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // содержание поста
        $data['body']        = trim($_POST['body']);
        // дата добавления
        $data['date']        = $_POST['date'];
        // время добавления
        $data['time']        = $_POST['time'];

        // категория поста
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        $this->setSessionData('uploadBlogPostForm', $data);

        /*
         *обращаемся к модели для загрузки файлов
         */
        $this->blogBackendModel->uploadFiles();

    }

}