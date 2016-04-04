<?php
/**
 * Страница управления вакансиями компании,
 * файл view/example/backend/template/vacancy/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $vacancies - массив вакансий компании компании
 * $addVacancyURL - URL страницы с формой для добавления вакансии
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/vacancy/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Вакансии</h1>

<p><a href="<?php echo $addVacancyURL; ?>">Добавить вакансию</a></p>

<?php if ( ! empty($vacancies)): ?>
    <div id="all-vacancies">
        <ul>
            <?php foreach($vacancies as $item) : ?>
                <li>
                    <div><span><?php echo $item['sortorder']; ?></span> <?php echo $item['name']; ?></div>
                    <div>
                        <a href="<?php echo $item['url']['up']; ?>" title="Вверх"><i class="fa fa-arrow-up"></i></a>
                        <a href="<?php echo $item['url']['down']; ?>" title="Вниз"><i class="fa fa-arrow-down"></i></a>
                        <a href="<?php echo $item['url']['edit']; ?>" title="Редактировать"><i class="fa fa-pencil-square-o"></i></a>
                        <a href="<?php echo $item['url']['remove']; ?>" title="Удалить"><i class="fa fa-trash-o"></i></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/vacancy/index/center.php -->
