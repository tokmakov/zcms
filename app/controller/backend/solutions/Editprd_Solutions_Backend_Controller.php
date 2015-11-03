<?php
/**
 * Класс Editprd_Solutions_Backend_Controller формирует страницу с формой
 * для релдактирования товара типового решения. Получает данные от модели
 * Solutions_Backend_Model, административная часть сайта
 */
class Editprd_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editprd_Solutions_Backend_Controller
         */
        parent::input();

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id']))) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление товара прошло успешно
                // возвращаемся к списку товаров типового решения
                $solution_id = $this->solutionsBackendModel->getProductParent($this->params['id']);
                $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/show/id/' . $solution_id));
            } else { // при заполнении формы были допущены ошибки
                // перенаправляем администратора на страницу с формой для исправления ошибок
                $this->redirect($this->solutionsBackendModel->getURL('backend/solutions/addprd/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование товара. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionsBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionsBackendModel->getURL('backend/solutions/index')
            ),
            array(
                'name' => 'Категории',
                'url' => $this->solutionsBackendModel->getURL('backend/solutions/allctgs')
            ),
        );

        // получаем от модели информацию о товаре
        $product = $this->solutionsBackendModel->getSolutionProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
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
            'action'      => $this->solutionsBackendModel->getURL('backend/solutions/editprd/id/' . $this->params['id']),
            // заголовок
            'heading'     => $product['heading'],
            // код (артикул) товара
            'code'        => $product['code'],
            // торговое наименование изделия
            'name'        => $product['name'],
            // функциональное наименование изделия
            'title'       => $product['title'],
            // краткое описание
            'shortdescr'  => $product['shortdescr'],
            // цена
            'price'       => $product['price'],
            // количество
            'count'       => $product['count'],
            // сноска
            'note'        => $product['note'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editSolutionProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSolutionProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSolutionProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если
     * были допущены ошибки, функция возвращает false; если ошибок нет,
     * функция добавляет новое типовое решение и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
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
            $this->setSessionData('editSolutionProductForm', $data);
            return false;
        }

        // уникальный идентификатор товара
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления товара
        $this->solutionsBackendModel->updateSolutionProduct($data);

        return true;

    }

}