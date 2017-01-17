<?php
/**
 * Класс Editbnr_Start_Backend_Controller для редактирования баннера на главной
 * странице, формирует страницу с формой для редактирования баннера, обновляет
 * запись в таблице БД banners, работает с моделью Start_Backend_Model
 */
class Editbnr_Start_Backend_Controller extends Start_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования баннера
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Start_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editbnr_Start_Backend_Controller
         */
        parent::input();

        // если не передан id баннера или id баннера не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление баннера прошло успешно
                $this->redirect($this->startBackendModel->getURL('backend/start/index'));
            } else { // при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->startBackendModel->getURL('backend/start/editbnr/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование баннера. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->startBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->startBackendModel->getURL('backend/start/index'), 'name' => 'Витрина'),
        );

        // получаем от модели информацию о баннере
        $banner = $this->startBackendModel->getBanner($this->params['id']);
        // если запрошенный баннер не найден в БД
        if (empty($banner)) {
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
            'action'      => $this->startBackendModel->getURL('backend/start/editbnr/id/' . $this->params['id']),
            // уникальный идентификатор баннера
            'id'          => $this->params['id'],
            // наименование баннера
            'name'        => $banner['name'],
            // URL ссылки с баннера
            'url'         => $banner['url'],
            // alt текст баннера
            'alttext'     => $banner['alttext'],
            // показывать баннер?
            'visible'     => $banner['visible'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('editStartBannerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editStartBannerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editStartBannerForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет баннер и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']    = trim(iconv_substr($_POST['name'], 0, 100));     // наименование баннера
        $data['url']     = trim(iconv_substr($_POST['url'], 0, 250));      // URL ссылки с баннера
        $data['alttext'] = trim(iconv_substr($_POST['alttext'], 0, 100));  // alt текст баннера
        $data['alttext'] = str_replace('"', '', $data['alttext']);

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
            $this->setSessionData('editStartBannerForm', $data);
            return false;
        }

        // обращаемся к модели для обновления баннера
        $data['id'] = $this->params['id'];
        $this->startBackendModel->updateBanner($data);

        return true;
    }

}