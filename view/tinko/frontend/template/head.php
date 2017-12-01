<?php
/**
 * Содержимое элемента <head> страницы, файл view/example/frontend/template/head.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $title - содержимое тега <title> страницы
 * $keywords - содержимое мета-тега keywords
 * $description- содержимое мета-тега description
 * $cssFiles - массив css-файлов, которые должны быть подключены к странице
 * $jsFiles - массив js-файлов, которые должны быть подключены к странице
 * $robots - разрешить индексацию страницы роботами?
 * $canonicalURL - каноноческий URL для роботов поисковых систем
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/head.php -->

<title><?php echo $title; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<meta name="description" content="<?php echo $description; ?>" />

<?php if ($robots): ?>
    <meta name="robots" content="all" />
<?php else: ?>
    <meta name="robots" content="none" />
<?php endif; ?>

<?php if ( ! empty($cssFiles)): ?>
	<?php foreach($cssFiles as $cssFile): ?>
		<link rel="stylesheet" href="<?php echo $cssFile; ?>" type="text/css" />
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( ! empty($jsFiles)): ?>
	<?php foreach($jsFiles as $jsFile): ?>
		<script type="text/javascript" src="<?php echo $jsFile; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ($canonicalURL): ?>
    <link rel="canonical" href="<?php echo $canonicalURL; ?>" />
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/head.php -->
