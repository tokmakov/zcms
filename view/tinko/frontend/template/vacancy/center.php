<?php
/**
 * Вакансии компании,
 * файл view/example/frontend/template/vacancy/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $vacancies - массив вакансий компании
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/vacancy/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Вакансии</h1>

<?php if ( ! empty($vacancies)): ?>
    <div id="vacancies">
    <?php foreach($vacancies as $vacancy): ?>
        <h2><?php echo $vacancy['name']; ?></h2>
        <?php foreach($vacancy['details'] as $detail): ?>
            <p><?php echo $detail['name']; ?></p>
            <ul>
            <?php foreach($detail['items'] as $item): ?>
                <li><?php echo $item; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/vacancy/center.php -->
