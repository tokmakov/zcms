<?php
/**
 * Главный шаблон, обёртка для всех остальных шаблонов, файл
 * view/example/frontend/template/wrapper.php, общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $headContent - html-код тега <head>
 * $headerContent - html-код шапки страницы
 * $menuContent - html-код главного меню
 * $centerContent - html-код центральной колонки
 * $leftContent - html-код левой колонки
 * $rightContent - html-код правой колонки
 * $footerContent - html-код подвала страницы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/wrapper.php -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php
echo $headContent;
?>
</head>
<body>

<div id="header">
    <div class="wrapper">
        <?php echo $headerContent; ?>
    </div>
</div>

<div id="main-menu">
    <div class="wrapper">
        <?php echo $menuContent; ?>
    </div>
</div>

<div class="wrapper">
    <div id="wrap">
        <div id="content">
            <?php echo $centerContent; ?>
        </div>
    </div>
    <div id="left">
        <?php echo $leftContent; ?>
    </div>
    <div id="right">
        <?php echo $rightContent; ?>
    </div>
</div>

<div id="footer">
    <div class="wrapper">
        <?php echo $footerContent; ?>
    </div>
</div>

</body>
</html>

<!-- Конец шаблона view/example/frontend/template/wrapper.php -->
