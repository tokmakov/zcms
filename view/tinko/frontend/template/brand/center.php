<?php
/**
 * Список брендов по алфавиту,
 * файл view/example/frontend/template/brand/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $alphabet - алфавит
 * $popular - массив популярных брендов
 * $brands - массив всех брендов
 * 
 * $alphabet = Array (
 *   [A-Z] => Array (
 *     [0] => A
 *     [1] => B
 *     [2] => C
 *     ..........
 *     [25] => Z
 *   )
 *   [А-Я] => Array (
 *     [0] => А
 *     [1] => Б
 *     [2] => В
 *     ..........
 *     [27] => Я
 *   )
 * )
 * 
 * $popular = Array (
 *   [0] => Array (
 *     [id] => 14
 *     [name] => Болид
 *     [maker] => http://www.host.ru/catalog/maker/42
 *     [image] => http://www.host.ru/files/brand/99cdac6ca76d13b91d69598e4c222421.jpg
 *     [sortorder] => 1
 *   )
 *   [1] => Array (
 *     ..........
 *   )
 *   [2] => Array (
 *     ..........
 *   )
 *   ..........
 * )
 * 
 * $brands = Array (
 *   [A-Z] => Array (
 *     [A] => Array (
 *       [0] => Array (
 *         [id] => 24
 *         [name] => Abloy
 *         [maker] => http://www.host.ru/catalog/maker/928
 *         [image] => http://www.host.ru/files/brand/fd2f0e584a537f6ee5d4c957ddd97fbf.jpg
 *         [sortorder] => 1
 *       )
 *       [1] => Array (
 *         ..........
 *       )
 *       ..........
 *     )
 *     [B] => Array (
 *       [0] => Array (
 *         [id] => 35
 *         [name] => Beward
 *         [maker] => http://www.host.ru/catalog/maker/718
 *         [image] => http://www.host.ru/files/brand/42dc62ab6411ba0ec590883a1f7894ca.jpg
 *         [sortorder] => 1
 *       )
 *       [1] => Array (
 *         ..........
 *       )
 *       ..........
 *     )
 *     ..........
 *     [Z] => Array (
 *       ..........
 *     )
 *   )
 *   [А-Я] => Array (
 *     ..........
 *   )
 * )
 * 
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/brand/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1 id="top-page">Бренды</h1>

<div id="all-brands">
    <div>
        <div>
        <?php foreach ($alphabet['A-Z'] as $char): /* ангийский алфавит */ ?>
            <?php if (isset($brands['A-Z'][$char])): ?>
                <a href="#char-<?php echo $char; ?>" class="scroll"><?php echo $char; ?></a>
            <?php else: ?>
                <span><?php echo $char; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
        <div>
        <?php foreach ($alphabet['А-Я'] as $char): /* русский алфавит */ ?>
            <?php if (isset($brands['А-Я'][$char])): ?>
                <a href="#char-<?php echo $char; ?>" class="scroll"><?php echo $char; ?></a>
            <?php else: ?>
                <span><?php echo $char; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </div>
    <?php if ( ! empty($popular)): /* популярные бренды */ ?>
        <section>
            <div>
                <span><i class="fa fa-star" title="Популярные"></i></span>
                <ul>
                <?php foreach($popular as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( ! empty($brands['A-Z'])): /* бренды A-Z */ ?>
        <section>
        <?php foreach ($brands['A-Z'] as $char => $items): ?>
            <div>
                <span id="char-<?php echo $char; ?>"><?php echo $char; ?></span>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
                <a href="#top-page" class="scroll"><i class="fa fa-arrow-circle-up"></i></a>
            </div>
        <?php endforeach; ?>
        </section>
    <?php endif; ?>
    
    <?php if ( ! empty($brands['А-Я'])): /* бренды А-Я */ ?>
        <section>
        <?php foreach ($brands['А-Я'] as $char => $items): ?>
            <div>
                <span id="char-<?php echo $char; ?>"><?php echo $char; ?></span>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
                <a href="#top-page" class="scroll"><i class="fa fa-arrow-circle-up"></i></a>
            </div>
        <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/brand/center.php -->
