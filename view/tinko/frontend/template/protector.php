<?php
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/protect.php -->

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Сайт временно недоступен</title>
</head>
<body>
<h1>Сайт временно недоступен</h1>
<p>Слишком много запросов, ip-адрес <?php echo $ip; ?> заблокирован на <?php echo $time; ?> секунд.</p>
</body>
</html>

<!-- Конец шаблона view/example/frontend/template/protect.php -->
