<?php
/**
 * Список всех пользователей,
 * файл view/example/backend/template/user/index/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $users - массив всех пользователей
 * $pager - постраничная навигация
 * $thisPageUrl - URL ссылки на эту страницу
 * $addUserUrl - URL ссылки для добавления нового пользователя
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/user/index/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Пользователи</h1>

<p><a href="<?php echo $addUserUrl; ?>">Добавить пользователя</a></p>

<?php if (!empty($users)): // все пользователи ?>
    <div id="all-users">
        <ul>
            <?php foreach ($users as $user) : ?>
                <li>
                    <div>
                        <a href="<?php echo $user['url']['show']; ?>">
                            <?php echo $user['surname']; ?>
                            <?php echo $user['name']; ?>
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo $user['url']['edit']; ?>" title="Редактировать">Ред.</a>
                        <a href="<?php echo $user['url']['remove']; ?>" title="Удалить">Удл.</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($pager)): // постраничная навигация ?>
    <ul class="pager">
        <?php if (isset($pager['first'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>">&lt;&lt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['prev'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['prev'] : ''; ?>">&lt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['left'])): ?>
            <?php foreach ($pager['left'] as $left) : ?>
                <li>
                    <a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        <li>
            <span><?php echo $pager['current']; // текущая страница ?></span>
        </li>

        <?php if (isset($pager['right'])): ?>
            <?php foreach ($pager['right'] as $right) : ?>
                <li>
                    <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($pager['next'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>">&gt;</a>
            </li>
        <?php endif; ?>
        <?php if (isset($pager['last'])): ?>
            <li>
                <a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>">&gt;&gt;</a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>

<!-- Конец шаблона view/example/backend/template/user/index/center.php -->
