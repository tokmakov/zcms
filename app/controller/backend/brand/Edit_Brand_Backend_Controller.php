<?php
/**
 * Класс Edit_Brand_Backend_Controller для редактирования бренда, формирует страницу
 * с формой для редактирования бренда, обновляет запись в таблице БД brands, работает
 * с моделью Brand_Backend_Model, административная часть сайта
 */
class Edit_Brand_Backend_Controller extends Brand_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования бренда
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Brand_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Edit_Brand_Backend_Controller
         */
        parent::input();

        // если не передан id бренда или id бренда не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление бренда прошло успешно
                $this->redirect($this->brandBackendModel->getURL('backend/brand/index'));
            } else { // при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->brandBackendModel->getURL('backend/brand/edit/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование бренда. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->brandBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Бренды',
                'url'  => $this->brandBackendModel->getURL('backend/brand/index') 
            ),
        );

        // получаем от модели информацию о бренде
        $brand = $this->brandBackendModel->getBrand($this->params['id']);
        // если запрошенный бренд не найден в БД
        if (empty($brand)) {
            $this->notFoundRecord = true;
            return;
        }
        
        $image = '';
        if ( ! empty($brand['image'])) {
            $image = $this->config->site->url . 'files/brand/' . $brand['image'] . '.jpg';
        }
        
        // все буквы, для возможности выбора
        $letters = array(
            'A-Z' => $this->brandBackendModel->getLatinLetters(),
            'А-Я' => $this->brandBackendModel->getCyrillicLetters()
        );
        
        // все производители, для возможности выбора
        $makers = $this->brandBackendModel->getAllMakers();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->brandBackendModel->getURL('backend/brand/edit/id/' . $this->params['id']),
            // уникальный идентификатор бренда
            'id'          => $this->params['id'],
            // наименование бренда
            'name'        => $brand['name'],
            // первая буква бренда
            'letter'      => $brand['letter'],
            // файл изображения
            'image'       => $image,
            // все буквы, для возможности выбора
            'letters'     => $letters,
            // все производители, для возможности выбора
            'makers'      => $makers,
        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('editBrandForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editBrandForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editBrandForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет бренд и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
         
        // наименование бренда
        $data['name']   = trim(utf8_substr($_POST['name'], 0, 32));
        
        // первая буква бренда
        $data['letter'] = '';
        if (
            isset($_POST['letter'])
            &&
            (
                in_array($_POST['letter'], $this->brandBackendModel->getLatinLetters())
                ||
                in_array($_POST['letter'], $this->brandBackendModel->getCyrillicLetters())
            )
        ) {
            $data['letter'] = $_POST['letter'];
        }
        
        // популярный бренд?
        $data['popular'] = false;
        if (isset($_POST['popular'])) {
            $data['popular'] = true;
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['letter'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Буква»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editBrandForm', $data);
            return false;
        }

        // уникальный идентификатор бренда
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления бренда
        $this->brandBackendModel->updateBrand($data);

        return true;

    }

}