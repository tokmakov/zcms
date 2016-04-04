<?php
/**
 * Класс Edit_Vacancy_Backend_Controller для редактирования вакансии, формирует страницу
 * с формой для редактирования вакансии, обновляет запись в таблице БД vacancies, работает
 * с моделью Vacancy_Backend_Model, административная часть сайта
 */
class Edit_Vacancy_Backend_Controller extends Vacancy_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования вакансии
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Vacancy_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Edit_Vacancy_Backend_Controller
         */
        parent::input();

        // если не передан id вакансии или id вакансии не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление вакансии прошло успешно
                $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/index'));
            } else { // при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/edit/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование вакансии. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->vacancyBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->vacancyBackendModel->getURL('backend/vacancy/index'), 'name' => 'Вакансии'),
        );

        // получаем от модели информацию о вакансии
        $vacancy = $this->vacancyBackendModel->getVacancy($this->params['id']);
        // если запрошенная вакансия не найдена в БД
        if (empty($vacancy)) {
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
            'action'      => $this->vacancyBackendModel->getURL('backend/vacancy/edit/id/' . $this->params['id']),
            // уникальный идентификатор вакансии
            'id'          => $this->params['id'],
            // название вакансии
            'name'        => $vacancy['name'],
            // подробная информация о вакансии
            'details'     => unserialize($vacancy['details']),
            // вакансия доступна для просмотра?
            'visible'     => $vacancy['visible'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('editVacancyForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editVacancyForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editVacancyForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет вакансию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name']    = trim(utf8_substr($_POST['name'], 0, 100));     // название вакансии
        
        $data['visible'] = 0;
        if (isset($_POST['visible'])) {
            $data['visible'] = 1;
        }
        
        // подробная информация о вакансии
        $details = array();
        $error = 0;
        if (isset($_POST['names']) && is_array($_POST['names'])) {
            $count = 0;
            foreach ($_POST['names'] as $key => $name) {
                $name = trim(utf8_substr($name, 0, 100));
                $items = array();
                if (isset($_POST['items'][$key]) && is_array($_POST['items'][$key])) {
                    foreach ($_POST['items'][$key] as $item) {
                        if (empty($item)) continue;
                        $items[] = trim(utf8_substr($item, 0, 100));
                    }
                }
                if ( (empty($name) && !empty($items)) || (!empty($name) && empty($items))) {
                    $error = $error + 1;
                }
                if (empty($name) && empty($items)) {
                    continue;
                }
                $details[$count] = array(
                    'name' => $name,
                    'items' => $items,
                );
                $count++;
            }
        }

        if (empty($details)) {
            $details = array(
                array(
                    'name'  => 'Требования',
                    'items' => array()
                ),
                array(
                    'name'  => 'Обязанности',
                    'items' => array()
                ),
                array(
                    'name'  => 'Условия',
                    'items' => array()
                ),
            );
        }
        $data['details'] = $details;

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название вакансии»';
        }
        if ($error) {
            $errorMessage[] = 'Не заполнены обязательные поля «Условия и требования»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editVacancyForm', $data);
            return false;
        }

        // уникальный идентификатор вакансии
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления вакансии
        $this->vacancyBackendModel->updateVacancy($data);

        return true;

    }

}