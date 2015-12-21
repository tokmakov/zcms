<?php
/**
 * Загрузка файлов с использованием XmlHttpRequest,
 * файл view/example/backend/template/blog/xhr/files.php,
 * контроллер Xhr_Files_Blog_Backend_Controller.php,
 * административная часть сайта
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/blog/xhr/files.php -->

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
        <li><a href="<?php echo $file['path'] ?>" target="_blank"><?php echo $icon; ?>&nbsp;<span><?php echo $file['name']; ?></span></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/blog/xhr/files.php -->
