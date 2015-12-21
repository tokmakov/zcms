<?php
/**
 * Форма для добавления поста блога,
 * файл view/example/backend/template/blog/addpost/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $categories - массив всех категорий
 * $date - текущая дата
 * $time - текущее время
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/blog/addpost/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новая запись</h1>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $name        = '';
    $category    = 0;
    $keywords    = '';
    $description = '';
    $excerpt     = '';
    $body        = '';

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $category    = $savedFormData['category'];
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $excerpt     = htmlspecialchars($savedFormData['excerpt']);
        $body        = htmlspecialchars($savedFormData['body']);
        $date        = htmlspecialchars($savedFormData['date']);
        $time        = htmlspecialchars($savedFormData['time']);
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="add-edit-post">
    <div>
        <div>Заголовок</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Категория</div>
        <div>
            <select name="category">
            <option value="0">Выберите</option>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $ctg): ?>
                    <option value="<?php echo $ctg['id']; ?>"<?php if ($ctg['id'] == $category) echo 'selected="selected"'; ?>><?php echo $ctg['name']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div>Ключевые слова (meta)</div>
        <div><input type="text" name="keywords" maxlength="250" value="<?php echo $keywords; ?>" /></div>
    </div>
    <div>
        <div>Описание (meta)</div>
        <div><input type="text" name="description" maxlength="250" value="<?php echo $description; ?>" /></div>
    </div>
    <div>
        <div>Изображение</div>
        <div><input type="file" name="image" /></div>
    </div>
    <div>
        <div>Анонс</div>
        <div><textarea name="excerpt"><?php echo $excerpt; ?></textarea></div>
    </div>
    <div>
        <div>Текст (содержание)</div>
        <div><textarea name="body"><?php echo $body; ?></textarea></div>
    </div>
    <div id="blog-files">
        <div>
            <div>
                <span>Файлы</span>
            </div>
            <div>
                <input type="file" name="files[]" id="files" multiple="multiple" />
                <input type="submit" name="upload" value="Загрузить" />
            </div>
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
                        <li><span data-url="<?php echo $file['path'] ?>" data-type="<?php echo $file['type'] ?>" title="Вставить"><?php echo $icon; ?>&nbsp;<span><?php echo $file['name'] ?></span></span></li>
                    <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div>Дата и время</div>
        <div>
            <input type="text" name="date" value="<?php echo $date; ?>" />
            <input type="text" name="time" value="<?php echo $time; ?>" />
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/blog/addpost/center.php -->
