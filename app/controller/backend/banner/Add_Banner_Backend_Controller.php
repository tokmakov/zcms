<?php
/**
 * Класс Add_Banner_Backend_Controller для добавления нового баннера, формирует
 * страницу с формой для добавления баннера, добавляет запись в таблицу БД banners,
 * работает с моделью Banner_Backend_Model
 */
class Add_Banner_Backend_Controller extends Banner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления баннера; в данном случае никаких данных получать
     * не нужно
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Banner_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Add_Banner_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление баннера прошло успешно
                $this->redirect($this->bannerBackendModel->getURL('backend/banner/index'));
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->bannerBackendModel->getURL('backend/banner/add'));
            }
        }

        $this->title = 'Новый баннер. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->bannerBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->bannerBackendModel->getURL('backend/banner/index'), 'name' => 'Баннеры'),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->bannerBackendModel->getURL('backend/banner/add'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('addBannerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addBannerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addBannerForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет баннер и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']    = trim(utf8_substr($_POST['name'], 0, 100));     // наименование баннера
        $data['url']     = trim(utf8_substr($_POST['url'], 0, 250));      // URL ссылки с баннера
        $data['alttext'] = trim(utf8_substr($_POST['alttext'], 0, 100));  // alt текст баннера

        $data['visible'] = 0;
        if (isset($_POST['visible'])) {
            $data['visible'] = 1;
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['url'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «URL ссылки»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addBannerForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового баннера
        $this->bannerBackendModel->addBanner($data);

        return true;

    }

}