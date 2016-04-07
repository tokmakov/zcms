<?php
/**
 * Класс Addprd_Solution_Backend_Controller формирует страницу с формой
 * для добавления товара в типовое решение. Получает данные от модели
 * Solution_Backend_Model, административная часть сайта
 */
class Addprd_Solution_Backend_Controller extends Solution_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solution_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addprd_Solution_Backend_Controller
         */
        parent::input();

        // если не передан id типового решения или id типового решения не число
        if ( ! (isset($this->params['parent']) && ctype_digit($this->params['parent']))) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['parent'] = (int)$this->params['parent'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление товара прошло успешно
                // возвращаемся к списку товаров типового решения
                $this->redirect($this->solutionBackendModel->getURL('backend/solution/show/id/' . $this->params['parent']));
            } else { // при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ошибок
                $this->redirect($this->solutionBackendModel->getURL('backend/solution/addprd/parent/' . $this->params['parent']));
            }
        }

        $this->title = 'Добавить товар. ' . $this->title;

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

        // единицы измерения для возможности выбора
        $units = $this->solutionBackendModel->getUnits();

        // группы (типы) товаров: основное, объектовое, пультовое, дополнительное
        $groups = $this->solutionBackendModel->getGroups();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->solutionBackendModel->getURL('backend/solution/addprd/parent/' . $this->params['parent']),
            // группы (типы) товаров
            'groups'      => $groups,
            // единицы измерения
            'units'       => $units,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addSolutionProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addSolutionProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addSolutionProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если
     * были допущены ошибки, функция возвращает false; если ошибок нет,
     * функция добавляет новое типовое решение и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // группа (тип) товара
        $data['group'] = 1;
        if (ctype_digit($_POST['group']) && $_POST['group'] > 1) {
            $data['group'] = (int)$_POST['group'];
        }

        // заголовок
        $data['heading']    = trim(utf8_substr($_POST['heading'], 0, 100));
        // торговое наименование
        $data['name']       = trim(utf8_substr($_POST['name'], 0, 250));
        // функциональное наименование
        $data['title']      = trim(utf8_substr($_POST['title'], 0, 250));
        // краткое описание
        $data['shortdescr'] = trim(utf8_substr($_POST['shortdescr'], 0, 2000));

        // код (артикул)
        $data['code'] = '';
        $code = trim($_POST['code']);
        if (preg_match('~^\d{6}$~', $code)) {
            $data['code'] = $code;
        }

        // цена
        $data['price'] = 0.0;
        $price = trim($_POST['price']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $price)) {
            $data['price'] = (float)$price;
        }

        // единица измерения
        $data['unit'] = 0;
        if (ctype_digit($_POST['unit']) && in_array($_POST['unit'], array(1,2,3,4,5))) {
            $data['unit'] = (int)$_POST['unit'];
        }

        // количество
        $data['count'] = 1;
        $count = trim($_POST['count']);
        if (ctype_digit($count)) {
            $data['count'] = (int)$count;
        }

        // сноска
        $data['note'] = 0;
        if (isset($_POST['note'])) {
            $data['note'] = 1;
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['code'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Код (артикул)»';
        }
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Торговое наименование»';
        }
        if (empty($data['count'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Количество»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * администратором данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addSolutionProductForm', $data);
            return false;
        }

        $data['parent'] = $this->params['parent'];

        // обращаемся к модели для добавления нового товара
        $this->solutionBackendModel->addSolutionProduct($data);

        return true;

    }

}