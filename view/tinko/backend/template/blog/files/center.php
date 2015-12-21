<?php
/**
 * Список всех категорий блога,
 * файл view/example/backend/template/blog/files/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $allPostsUrl - URL ссылки на страницу со списком всех постов блога
 * $allCtgsUrl - URL ссылки на страницу со списком категорий
 * $folders - массив директорий и файлов
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/blog/files/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Блог</h1>

<ul id="tabs">
    <li><a href="<?php echo $allPostsUrl; ?>">Посты</a></li>
    <li><a href="<?php echo $allCtgsUrl; ?>">Категории</a></li>
    <li class="current"><span>Файлы</span></li>
</ul>

<div id="all-blog-files">
    <div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple="multiple" />
            <input type="submit" id="submit" value="Загрузить" />
        </form>
    </div>
    <div>
    <?php foreach ($folders as $folder => $files): ?>
        <div>
            <span><i class="fa fa-folder-open-o"></i>&nbsp;<?php echo $folder; ?></span>
            <div>
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
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<!-- Конец шаблона view/example/backend/template/blog/files/center.php -->
