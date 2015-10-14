<?php
/**
 * Главный шаблон, обёртка для всех остальных шаблонов,
 * файл view/example/backend/template/wrapper.php,
 * административная часть сайта
 *
 * $headContent - html-код тега <head>
 * $headerContent - html-код шапки страницы
 * $menuContent - html-код главного меню
 * $centerContent - html-код центральной колонки
 * $footerContent - html-код подвала страницы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/wrapper.php -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
echo $headContent;
?>
</head>
<body>

<div id="wrapper">

    <div id="header">
        <?php echo $headerContent; ?>
    </div>

    <div id="menu">
        <?php echo $menuContent; ?>
    </div>

    <div id="content">
        <?php echo $centerContent; ?>
    </div>

    <div id="footer">
        <?php echo $footerContent; ?>
    </div>

</div>

</body>
</html>

<!-- Конец шаблона view/example/backend/template/wrapper.php -->
