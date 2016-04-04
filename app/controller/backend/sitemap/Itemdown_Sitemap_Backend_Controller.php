<?php
/**
 * Класс Itemdown_Sitemap_Backend_Controller опускает элемент карты сайта вниз,
 * взаимодействует с моделью Sitemap_Backend_Model, административная часть сайта
 */
class Itemdown_Sitemap_Backend_Controller extends Sitemap_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы. В
     * данном случае страницу нам формировать не нужно, и от модели ничего получать
     * не надо. Только опустить элемент карты сайта вниз и сделать редирект.
     */
    protected function input() {
        // если передан id элемента карты сайта и id элемента целое положительное число
        if (isset($this->params['id']) && ctype_digit($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->sitemapBackendModel->moveSitemapItemDown($this->params['id']);
        }
        $this->redirect($this->sitemapBackendModel->getURL('backend/sitemap/index'));
    }
}