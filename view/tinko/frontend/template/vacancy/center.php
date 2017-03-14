<?php
/**
 * Вакансии компании,
 * файл view/example/frontend/template/vacancy/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $vacancies - массив вакансий компании
 *
 * $vacancies = Array (
 *   [0] => Array (
 *     [name] => Начальник складского хозяйства
 *     [details] => Array (
 *       [0] => Array (
 *         [name] => Должен знать
 *         [items] => Array (
 *           [0] => Отличное знание WMS, опыт работы с WMS от 1 года, желателен опыт внедрения
 *           [1] => Нормативные и методические материалы по вопросам организации складского хозяйства
 *           [2] => Стандарты и технические условия на хранение товарно-материальных ценностей
 *           ..........
 *         )
 *       )
 *       [1] => Array (
 *         [name] => Обязанности
 *         [items] => Array (
 *           [0] => Руководить работой по приему, хранению и отпуску товара
 *           [1] => Обеспечивать рациональное использование складских площадей
 *           [2] => Обеспечивать сохранность товарно-материальных ценностей
 *           ..........
 *         )
 *       )
 *       [2] => Array (
 *         [name] => Требования
 *         [items] => Array (
 *           [0] => Обязателен опыт работы с WMS от 1 года и выше
 *           [1] => Опыт работы в аналогичной должности от 3 лет
 *         )
 *       )
 *       [3] => Array (
 *         ..........
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [name] => Инженер-консультант по системам ОПС
 *     [details] => Array (
 *       [0] => Array (
 *         [name] => Требования
 *         [items] => Array (
 *           [0] => Должен знать — принципы построения систем безопасности
 *           [1] => Должен уметь — подбирать оборудование в соответствии с техническим заданием
 *           [2] => Высшее техническое образование
 *           [3] => Опыт работы от 1 до 3 лет
 *         )
 *       )
 *       [1] => Array (
 *         [name] => Обязанности
 *         [items] => Array (
 *           [0] => Консультации клиентов Компании по техническим вопросам по закрепленным направлениям
 *           [1] => Консультации менеджеров Компании по техническим вопросам по закрепленным направлениям
 *           [2] => Подготовка материалов для периодических изданий Компании
 *         )
 *       )
 *       [2] => Array (
 *         ..........
 *       )
 *     )
 *   )
 *   [2] => Array (
 *     ..........
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/vacancy/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Вакансии</h1>

<?php if ( ! empty($vacancies)): ?>
    <div id="vacancies">
    <?php foreach($vacancies as $vacancy): ?>
        <h2><?php echo $vacancy['name']; ?></h2>
        <?php foreach($vacancy['details'] as $detail): ?>
            <p><?php echo $detail['name']; ?></p>
            <ul>
            <?php foreach($detail['items'] as $item): ?>
                <li><?php echo $item; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Конец шаблона view/example/frontend/template/vacancy/center.php -->
