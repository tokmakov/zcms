<?php
/**
 * Класс Edit_Partner_Backend_Controller для редактирования партнера, формирует страницу
 * с формой для редактирования партнера, обновляет запись в таблице БД partners, работает
 * с моделью Partner_Backend_Model, административная часть сайта
 */
class Edit_Partner_Backend_Controller extends Partner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования партнера компании
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Partner_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Edit_Partner_Backend_Controller
         */
        parent::input();

        // если не передан id партнера или id партнера не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление партнера прошло успешно
                $this->redirect($this->partnerBackendModel->getURL('backend/partner/index'));
            } else { // при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->partnerBackendModel->getURL('backend/partner/edit/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование партнера. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->partnerBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->partnerBackendModel->getURL('backend/partner/index'), 'name' => 'Партнеры'),
        );

        // получаем от модели информацию о партнере
        $partner = $this->partnerBackendModel->getPartner($this->params['id']);
        // если запрошенный партнер не найден в БД
        if (empty($partner)) {
            $this->notFoundRecord = true;
            return;
        }
        
        $image = $this->config->site->url . 'files/partner/images/' . $partner['image'] . '.jpg';

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->partnerBackendModel->getURL('backend/partner/edit/id/' . $this->params['id']),
            // уникальный идентификатор партнера
            'id'          => $this->params['id'],
            // наименование партнера
            'name'        => $partner['name'],
            // файл изображения сертификата
            'image'       => $image,
            // alt текст сертификата партнера
            'alttext'     => $partner['alttext'],
            // показывать сертификат партнера?
            'expire'      => $partner['expire'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('editPartnerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editPartnerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editPartnerForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет партнера и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']    = trim(utf8_substr($_POST['name'], 0, 100));     // наименование партнера
        $data['alttext'] = trim(utf8_substr($_POST['alttext'], 0, 100));  // alt текст сертификата партнера
        $data['alttext'] = str_replace('"', '', $data['alttext']);

        $year = date('Y') + 1;
        $data['expire'] = date('d') . '.' . date('m') . '.' . $year;
        $_POST['expire']  = trim ($_POST['expire']);
        if (preg_match('~\d{2}\.\d{2}\.\d{2}~', $_POST['expire'])) {
            $data['expire'] = $_POST['expire'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editPartnerForm', $data);
            return false;
        }

        // уникальный идентификатор партнера
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления партнера
        $this->partnerBackendModel->updatePartner($data);

        return true;

    }

}