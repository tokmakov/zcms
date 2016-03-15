<?php
/**
 * Класс Xhr_Product_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате HTML, получает данные от модели Product_Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит информацию о товаре каталога для показа в модальном окне
 */
class Xhr_Product_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

    /**
     * информация о товаре в формате HTML
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели данные о товаре
        $product = $this->productCatalogFrontendModel->getProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        
        // технические характеристики
        $techdata = array();
        if ( ! empty($product['techdata'])) {
            $techdata = unserialize($product['techdata']);
        }

        // фото товара
        if ((!empty($product['image'])) && is_file('files/catalog/imgs/medium/' . $product['image'])) {
            $image['medium'] = $this->config->site->url . 'files/catalog/imgs/medium/' . $product['image'];
        } else {
            $image['medium'] = $this->config->site->url . 'files/catalog/imgs/medium/nophoto.jpg';
        }
        
        // единицы измерения товара
        $units = $this->productCatalogFrontendModel->getUnits();

        // формируем HTML
        $this->output = $this->render(
            $this->config->site->theme . '/frontend/template/catalog/xhr/product.php',
            array(
                // заголовок h1 - торговое наименование товара
                'name'         => $product['name'],
                // заголовок h2 - функциональное наименование товара
                'title'        => $product['title'],
                // код (артикул) товара
                'code'         => $product['code'],
                // розничная цена
                'price'        => $product['price'],
                // цена, мелкий опт
                'price2'       => $product['price2'],
                // оптовая цена
                'price3'       => $product['price3'],
                // единица измерения
                'unit'         => $product['unit'],
                // массив единиц измерения товара
                'units'        => $units,
                // производитель
                'maker'        => array(
                    'id'   => $product['mkr_id'],
                    'name' => $product['mkr_name'],
                    'url'  => $this->productCatalogFrontendModel->getURL('frontend/catalog/maker/id/' . $product['mkr_id']),
                ),
                // функциональная группа
                'group'        => array(
                    'id'   => $product['grp_id'],
                    'name' => $product['grp_name'],
                    'url'  => $this->productCatalogFrontendModel->getURL('frontend/catalog/group/id/' . $product['grp_id']),
                ),
                // новый товар?
                'new'          => $product['new'],
                // лидер продаж?
                'hit'          => $product['hit'],
                // краткое описание
                'shortdescr'   => $product['shortdescr'],
                // фото товара
                'image'        => $image,
                // технические характеристики
                'techdata'     => $techdata,
            )
        );
    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-type: text/html; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}