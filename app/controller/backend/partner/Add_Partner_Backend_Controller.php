<?php
/**
 * Класс Add_Partner_Backend_Controller для добавления нового партнера компании,
 * формирует страницу с формой для добавления партнера, добавляет запись в таблицу БД
 * partners, работает с моделью Partner_Backend_Model, административная часть сайта
 */
class Add_Partner_Backend_Controller extends Partner_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления партнера компании; в данном случае никаких данных
     * получать не нужно
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Partner_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Add_Partner_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление партнера прошло успешно
                $this->redirect($this->partnerBackendModel->getURL('backend/partner/index'));
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->partnerBackendModel->getURL('backend/partner/add'));
            }
        }

        $this->title = 'Новый партнер. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->partnerBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->partnerBackendModel->getURL('backend/partner/index'), 'name' => 'Партнеры'),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->partnerBackendModel->getURL('backend/partner/add'),
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('addPartnerForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addPartnerForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addPartnerForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет партнера и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']    = trim(utf8_substr($_POST['name'], 0, 100));     // наименование партнера
        $data['alttext'] = trim(utf8_substr($_POST['alttext'], 0, 100));  // alt текст фото сертификата партнера
        $data['alttext'] = str_replace('"', '', $data['alttext']);

        $data['visible'] = 0;
        if (isset($_POST['visible'])) {
            $data['visible'] = 1;
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
            $this->setSessionData('addPartnerForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового партнера
        $this->partnerBackendModel->addPartner($data);

        return true;

    }

}