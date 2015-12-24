<?php
mysql_select_db ('blog');

// Создаём XML-документ
$dom = new DOMDocument('1.0', 'utf-8');
// Создаём корневой элемент <rss>
$root = $dom->createElement('rss');
$dom->appendChild($root);
$root->setAttribute('version', '2.0');
/*
$root->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
$root->setAttribute('xmlns:wfw', 'http://wellformedweb.org/CommentAPI/');
$root->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
$root->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
$root->setAttribute('xmlns:sy', 'http://purl.org/rss/1.0/modules/syndication/');
$root->setAttribute('xmlns:slash', 'http://purl.org/rss/1.0/modules/slash/');
*/
$root->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');

// Создаём элемент <channel>
$channel = $dom->createElement('channel');
$root->appendChild($channel);

$title = $dom->createElement('title', 'Новости ТД ТИНКО');
$channel->appendChild($title);

$link = $dom->createElement('link', 'http://www.tinko.ru/news/');
$channel->appendChild($link);

$description = $dom->createElement('description', 'Новости Торгового Дома ТИНКО');
$channel->appendChild($description);

$lastBuildDate = $dom->createElement('lastBuildDate', date('r'));
$channel->appendChild($lastBuildDate);

$query = "
SELECT `a`.`ID` AS `id`, `e`.`meta_value` AS `thumbnail`
FROM `wp_posts` `a`
INNER JOIN `wp_term_relationships` `b` ON `a`.`ID`=`b`.`object_id`
LEFT JOIN `wp_postmeta` `c` ON `a`.`ID`=`c`.`post_id` AND `c`.`meta_key`='_thumbnail_id'
LEFT JOIN `wp_postmeta` `d` ON `d`.`post_id`=`c`.`meta_value` AND `d`.`meta_key`='_wp_attached_file'
LEFT JOIN `wp_postmeta` `e` ON `e`.`post_id`=`c`.`meta_value` AND `e`.`meta_key`='_wp_attachment_metadata'
WHERE `a`.`post_type`='post' AND `a`.`post_status`='publish' AND `b`.`term_taxonomy_id` IN (5, 6)
ORDER BY `a`.`post_date` DESC LIMIT 10
";

$res = mysql_query( $query );
if ( mysql_num_rows( $res ) > 0 ) {
    $oldLastId = 0;
    $fileRssLastId = $mageRootDir . '/cron/post-products-import/rss-last-id/rss-last-id.txt';
    if (file_exists($fileRssLastId)) {
        $oldLastId = file_get_contents($fileRssLastId);
        if (!ctype_digit($oldLastId)) {
            $oldLastId = 0;
        }
    }
    $newLastId = 0;
    $countNewItems = 0;
    while( $row = mysql_fetch_assoc( $res ) ) {
        if ($newLastId == 0) {
            $newLastId = $row['id'];
        }
        if ($row['id'] > $oldLastId) {
            $countNewItems++;
        }
        $item = $dom->createElement('item');
        $channel->appendChild($item);
        // Для узла <item> добавляем дочерние узлы:
        // заголовок, ссылка, дата, анонс, картинка
        $title = $dom->createElement('title', htmlspecialchars($row['title']));
        $item->appendChild($title);
        $link = $dom->createElement('link', 'http://www.tinko.ru/news/archives/'.$row['id']);
        $item->appendChild($link);
        $pubDate = $dom->createElement('pubDate', date('r', $row['timestamp']));
        $item->appendChild($pubDate);
        $guid = $dom->createElement('guid', 'http://www.tinko.ru/news/archives/'.$row['id']);
        $item->appendChild($guid);
        $description = $dom->createElement('description', htmlspecialchars($row['description']));
        $item->appendChild($description);

        if ( !empty($row['thumbnail']) ) {
            $temp = unserialize($row['thumbnail']);
            if ( is_array( $temp ) ) {
                $path = utf8_substr($temp['file'], 0, 8);
                $thumbnail = 'http://www.tinko.ru/blog/wp-content/uploads/'.$temp['file'];
                $mimeType = '';
                $ext = substr($temp['file'], strrpos($temp['file'], '.') + 1);
                if ( $ext == 'jpeg' || $ext == 'jpg' ) {
                    $mimeType = 'image/jpeg';
                } elseif ( $ext == 'gif' ) {
                    $mimeType = 'image/gif';
                } elseif ( $ext == 'png' ) {
                    $mimeType = 'image/png';
                }
                $width = $temp['width'];
                $height = $temp['height'];
                if ( isset($temp['sizes']['thumbnail']) ) {
                    $thumbnail = 'http://www.tinko.ru/blog/wp-content/uploads/'.$path.$temp['sizes']['thumbnail']['file'];
                    $mimeType = $temp['sizes']['thumbnail']['mime-type'];
                    $width = $temp['sizes']['thumbnail']['width'];
                    $height = $temp['sizes']['thumbnail']['height'];
                }
                $image = $dom->createElement('media:content');
                $item->appendChild($image);
                $image->setAttribute('url', $thumbnail);
                $image->setAttribute('medium', 'image');
                $image->setAttribute('type', $mimeType);
                $image->setAttribute('width', $width);
                $image->setAttribute('height', $height);
            }
        }
    }

    // Сохраняем полученный XML-документ в файл
    if ($countNewItems > 2) {
        $dom->save($mageRootDir . '/rss2.xml');
        file_put_contents($fileRssLastId, $newLastId);
    }
}

mysql_select_db ('shop');
?>