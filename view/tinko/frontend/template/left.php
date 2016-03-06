<?php
/**
 * Левая колонка, файл view/example/frontend/template/left.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $catalogMenu - меню каталога (дерево каталога с одной раскрытой веткой)
 * $makers - массив 10 производителей
 * $allMakersURL - URL ссылки на страницу со списком всех производителей
 * $groups - массив 10 функциональных групп
 * $allGroupsURL - URL ссылки на страницу со списком всех функциональных групп
 *
 * $catalogMenu = Array (
 *   [0] => Array (
 *     [id] => 3
 *     [name] => Средства и системы охранно-пожарной сигнализации
 *     [level] => 0
 *     [url] => /catalog/category/3
 *   )
 *   [1] => Array (
 *     [id] => 185
 *     [name] => Средства и системы охранного телевидения
 *     [level] => 0
 *     [url] => /catalog/category/185
 *     [opened] => true
 *   )
 *   [2] => Array (
 *     [id] => 186
 *     [name] => Телекамеры
 *     [level] => 1
 *     [url] => /catalog/category/186
 *     [current] = true
 *   )
 *   [3] => Array (
 *     [id] => 394
 *     [name] => Объективы
 *     [level] => 1
 *     [url] => /catalog/category/394
 *   )
 *   [4] => Array (
 *     [id] => 437
 *     [name] => Видеорегистраторы
 *     [level] => 1
 *     [url] => /catalog/category/437
 *   )
 *   [5] => Array (
 *     [id] => 651
 *     [name] => Средства и системы контроля и управления доступом
 *     [level] => 0
 *     [url] => /catalog/category/651
 *   )
 * )
 *
 * $makers = Array (
 *   [0] => Array (
 *     [id] => 2252
 *     [name] => ABB
 *     [count] => 13
 *     [url] => http://www.host.ru/catalog/maker/2252
 *   )
 *   [1] => Array (
 *     [id] => 2249
 *     [name] => ABRON
 *     [count] => 12
 *     [url] => http://www.host.ru/catalog/maker/2249
 *   )
 *   [2] => Array (
 *     [id] => 318
 *     [name] => AccordTec
 *     [count] => 48
 *     [url] => http://www.host.ru/catalog/maker/318
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/left.php -->

<div>
    <div class="side-heading">Каталог оборудования</div>
    <div class="side-content">
        <?php if (!empty($catalogMenu)): ?>
            <div id="catalog-menu">
                <ul>
                <?php foreach ($catalogMenu as $item1): ?>
                    <?php
                        $class = 'root';
                        if ($item1['count']) {
                            $class = $class . ' parent';
                            if (isset($item1['opened'])) {
                                $class = $class . ' opened';
                            } else {
                                $class = $class . ' closed';
                            }
                        }
                    ?>
                    <li class="<?php echo $class; ?>">
                        <div>
                            <span><span></span></span>
                            <a href="<?php echo $item1['url']; ?>"><span><?php echo $item1['name']; ?></span></a>
                        </div>
                        <?php if (isset($item1['childs'])): ?>
                            <ul>
                            <?php foreach ($item1['childs'] as $item2): ?>
                                <?php
                                    $class = '';
                                    if ($item2['count']) {
                                        $class = 'parent';
                                        if (isset($item2['opened'])) {
                                            $class = $class . ' opened';
                                        } else {
                                            $class = $class . ' closed';
                                        }
                                    }
                                ?>
                                <li<?php if ( ! empty($class)) echo ' class="' . $class . '"'; ?>>
                                    <div>
                                        <span><span<?php if ($item2['count']) echo ' data-id="' . $item2['id'] . '"'; ?>></span></span>
                                        <a href="<?php echo $item2['url']; ?>"<?php if (isset($item2['current'])) echo ' class="selected"'; ?>><span><?php echo $item2['name']; ?></span></a>
                                    </div>
                                    <?php if (isset($item2['childs'])): ?>
                                        <ul>
                                        <?php foreach ($item2['childs'] as $item3): ?>
                                            <?php
                                                $class = '';
                                                if ($item3['count']) {
                                                    $class = 'parent';
                                                    if (isset($item3['opened'])) {
                                                        $class = $class . ' opened';
                                                    } else {
                                                        $class = $class . ' closed';
                                                    }
                                                }
                                            ?>
                                            <li<?php if ( ! empty($class)) echo ' class="' . $class . '"'; ?>>
                                                <div>
                                                    <span><span<?php if ($item3['count']) echo ' data-id="' . $item3['id'] . '"'; ?>></span></span>
                                                    <a href="<?php echo $item3['url']; ?>"<?php if (isset($item3['current'])) echo ' class="selected"'; ?>><span><?php echo $item3['name']; ?></span></a>
                                                </div>
                                                <?php if (isset($item3['childs'])): ?>
                                                    <ul>
                                                    <?php foreach ($item3['childs'] as $item4): ?>
                                                        <?php
                                                            $class = '';
                                                            if ($item4['count']) {
                                                                $class = 'parent';
                                                                if (isset($item4['opened'])) {
                                                                    $class = $class . ' opened';
                                                                } else {
                                                                    $class = $class . ' closed';
                                                                }
                                                            }
                                                        ?>
                                                        <li<?php if ( ! empty($class)) echo ' class="' . $class . '"'; ?>>
                                                            <div>
                                                                <span><span<?php if ($item4['count']) echo ' data-id="' . $item4['id'] . '"'; ?>></span></span>
                                                                <a href="<?php echo $item4['url']; ?>"<?php if (isset($item4['current'])) echo ' class="selected"'; ?>><span><?php echo $item4['name']; ?></span></a>
                                                            </div>
                                                            <?php if (isset($item4['childs'])): ?>
                                                                <ul>
                                                                <?php foreach ($item4['childs'] as $item5): ?>
                                                                    <?php
                                                                        $class = '';
                                                                        if ($item5['count']) {
                                                                            $class = 'parent';
                                                                            if (isset($item5['opened'])) {
                                                                                $class = $class . ' opened';
                                                                            } else {
                                                                                $class = $class . ' closed';
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <li<?php if ( ! empty($class)) echo ' class="' . $class . '"'; ?>>
                                                                        <div>
                                                                            <span><span<?php if ($item5['count']) echo ' data-id="' . $item5['id'] . '"'; ?>></span></span>
                                                                            <a href="<?php echo $item5['url']; ?>"<?php if (isset($item5['current'])) echo ' class="selected"'; ?>><span><?php echo $item5['name']; ?></span></a>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                                </ul>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="side-makers">
    <div class="side-heading">Производители</div>
    <div class="side-content">
        <ul>
        <?php foreach ($makers as $item): ?>
            <li>
                <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
            </li>
        <?php endforeach; ?>
        </ul>
        <p><a href="<?php echo $allMakersURL; ?>">Все производители</a></p>
    </div>
</div>

<div id="side-groups">
    <div class="side-heading">Функциональные группы</div>
    <div class="side-content">
        <ul>
        <?php foreach ($groups as $item): ?>
            <li>
                <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
            </li>
        <?php endforeach; ?>
        </ul>
        <p><a href="<?php echo $allGroupsURL; ?>">Все функциональные группы</a></p>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/left.php -->
