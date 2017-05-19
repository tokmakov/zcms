<?php
/**
 * Содержимое элемента <head> страницы, файл view/example/backend/template/head.php,
 * административная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $title - содержимое тега <title> страницы
 * $cssFiles - массив css-файлов, которые должны быть подключены к странице
 * $jsFiles - массив js-файлов, которые должны быть подключены к странице
 */
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/head.php -->

<title><?php echo $title; ?></title>

<?php if (isset($cssFiles) && count($cssFiles) > 0) : ?>
    <?php foreach($cssFiles as $cssFile) : ?>
        <link rel="stylesheet" href="<?php echo $cssFile; ?>" type="text/css" />
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($jsFiles) && count($jsFiles) > 0) : ?>
    <?php foreach($jsFiles as $jsFile) : ?>
        <script type="text/javascript" src="<?php echo $jsFile; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/head.php -->
