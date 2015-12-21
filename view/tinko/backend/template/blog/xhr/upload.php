<?php
/**
 * Загрузка файлов с использованием XmlHttpRequest,
 * файл view/example/backend/template/blog/xhr/upload.php,
 * контроллер Xhr_Upload_Blog_Backend_Controller.php,
 * административная часть сайта
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/blog/xhr/upload.php -->

<?php if (!empty($files)): ?>
<ul>
<?php foreach ($files as $file): ?>
    <?php
        $icon = '<i class="fa fa-file-o"></i>';
        switch($file['type']) {
            case 'img': $icon = '<i class="fa fa-file-image-o"></i>'; break;
            case 'pdf': $icon = '<i class="fa fa-file-pdf-o"></i>'; break;
            case 'zip': $icon = '<i class="fa fa-file-archive-o"></i>'; break;
            case 'doc': $icon = '<i class="fa fa-file-word-o"></i>'; break;
            case 'xls': $icon = '<i class="fa fa-file-excel-o"></i>'; break;
            case 'ppt': $icon = '<i class="fa fa-file-powerpoint-o"></i>'; break;
        }
    ?>
    <li><span data-url="<?php echo $file['path'] ?>" data-type="<?php echo $file['type'] ?>" title="Вставить"><?php echo $icon; ?>&nbsp;<span><?php echo $file['name'] ?></span></span></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/blog/xhr/upload.php -->
