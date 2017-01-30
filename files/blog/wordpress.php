<?php
/**
 * Для запуска из командной строки
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('ZCMS', true);
chdir('../..');

// поддержка кодировки UTF-8
require 'app/include/utf8.php';
// автоматическая загрузка классов
require 'app/include/autoload.php';
// настройки приложения
require 'app/config/config.php';
// инициализация настроек
Config::init($config);
unset($config);

// реестр, для хранения всех объектов приложения
$register = Register::getInstance();
// сохраняем в реестре настройки, чтобы везде иметь к ним доступ; доступ к
// настройкам возможен через реестр или напрямую через Config::getInstance()
$register->config = Config::getInstance();
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();

/*
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
*/

$register->database->execute('TRUNCATE TABLE `blog_posts`');

$xml = simplexml_load_file('files/blog/wordpress.xml');
foreach ($xml->channel->item as $item) {
    $namespace_wp = $item->children("http://wordpress.org/export/1.2/");
    $post_id = $namespace_wp->post_id;
    $title = $item->title;
    $title = str_replace('&quot;', '"', $title);
    $post_date = $namespace_wp->post_date;
    $temp = $item->category['nicename'];
    $category = 0;
    if ($temp == 'company-news') {
        $category = 1;
    }
    if ($temp == 'general-news') {
        $category = 2;
    }
    if ($category == 0) continue;
    $namespace_excerpt = $item->children("http://wordpress.org/export/1.2/excerpt/");
    $excerpt = $namespace_excerpt->encoded;
    $namespace_content = $item->children("http://purl.org/rss/1.0/modules/content/");
    $content = $namespace_content->encoded;
    
    $data = array(
        'id' => $post_id,
        'category' => $category,
        'name' => $title,
        'excerpt' => $excerpt,
        'body' => $content,
        'added' => $post_date
    );
    $query = "INSERT INTO `blog_posts`
              (
                  `id`,
                  `category`,
                  `name`,
                  `keywords`,
                  `description`,
                  `excerpt`,
                  `body`,
                  `added`
              )
              VALUES
              (
                  :id,
                  :category,
                  :name,
                  '',
                  '',
                  :excerpt,
                  :body,
                  :added
              )";
    $register->database->execute($query, $data);
}

$query = "SELECT * FROM `blog_posts` WHERE 1 ORDER BY `added` DESC";
$posts = $register->database->fetchAll($query);
foreach ($posts as $post) {
    $content = $post['body'];
    if (false !== preg_match_all('~/blog/wp-content/uploads/((\d+)/(\d+)/[^"?]+)~', $content, $matches)) {
        foreach ($matches[1] as $key => $value) {
            if (is_file('files/blog/source/'.$value)) {
                if (!is_dir('files/blog/' . $matches[2][$key])) {
                    mkdir('files/blog/' . $matches[2][$key]);
                }
                if (!is_dir('files/blog/' . $matches[2][$key] . '/' . $matches[3][$key])) {
                    mkdir('files/blog/' . $matches[2][$key] . '/' . $matches[3][$key]);
                }
                copy('files/blog/source/'.$value, 'files/blog/'.$value);
            } else {
				echo 'post=' . $post['id'] . ', no file ' . $value . PHP_EOL;
                // echo 'post=' . $post['id'].', no file '.preg_replace('~[а-яА-Я]~u', '*', $value) . PHP_EOL;
            }
        }
    }
    $content = str_replace('http://www.tinko.ru/blog/wp-content/uploads/', '/blog/wp-content/uploads/', $content);
    $content = str_replace('/blog/wp-content/uploads/', '/files/blog/', $content);
    $content = str_replace('class="instab"', 'class="data-table"', $content);
    $content = preg_replace('~<p>\s*<a href="(http://www.tinko.ru)?/p-(\d{6})\.html"( target="_blank")?>(<strong>)?.+?(</strong>)?</a>\s*</p>~u', '', $content);
    $content = preg_replace('~<p>\s*<a href="(http://www.tinko.ru)?/catalogsearch/[^"]+"( target="_blank")?>(<strong>)?.+?(</strong>)?</a>\s*</p>~u', '', $content);
    
    $query = "UPDATE `blog_posts` SET `body` = :content WHERE `id` = :id";
    $register->database->execute($query, array('content' => $content, 'id' => $post['id']));
}

require 'files/blog/thumb.php';
foreach ($wp_posts as $post) {
    if (empty($post['thumbnail'])) {
        echo 'No thumbnail for ' . $post['id'] . '<br/>';
        continue;
    }
    $temp = unserialize($post['thumbnail']);
    if ( is_array( $temp ) ) {
        $file = $temp['file'];
        $width = $temp['width'];
        $height = $temp['height'];
        if (!is_file('files/blog/source/'.$file)) {
            echo 'No thumbnail for ' . $post['id'] . '<br/>';
            continue;
        }
        if ($width != 100 || $height != 100) {
            resizeImage('files/blog/source/'.$file, 'files/blog/thumb/'.$post['id'].'.jpg', 100, 100);
        } else {
            copy('files/blog/source/'.$file, 'files/blog/thumb/'.$post['id'].'.jpg');
        }
    }
}

function resizeImage($src, $dst, $width, $height, $res = '', $rgb = array(255,255,255)) {
    if ( ! in_array($res, array('', 'jpg', 'jpeg', 'gif', 'png'))) return false;
    if ('jpg' == $res) $res = 'jpeg';

    if ( ! file_exists($src)) return false;
    $size = getimagesize($src);
    if (false === $size) return false;

    // определяем исходный формат по MIME-информации, предоставленной функцией
    // getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    // если ширина и высота изображения уже имеют нужное значение
    if ($size[0] == $width && $size[1] == $height) {
        if (empty($res) || $res == $format) {
            copy($src, $dst);
            return true;
        }
    }
    $func = "imagecreatefrom" . $format;
    if ( ! function_exists($func)) return false;

    if (0 == $height) {
        $height = floor(($size[1]/$size[0])*$width);
    }

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    // читаем в память файл изображения с помощью функции imagecreatefrom...
    $isrc = $func($src);
    // создаем новое изображение
    $idst = imagecreatetruecolor($width, $height);

    // заливка цветом фона
    if($format == 'png') { // прозрачность для png-изображений
        imagesavealpha($idst, true); // сохранение альфа канала
        $background = imagecolorallocatealpha($idst, $rgb[0], $rgb[1], $rgb[2], 127); // 127 - полная прозрачность
    } else {
        $background = imagecolorallocate($idst, $rgb[0], $rgb[1], $rgb[2]);
    }
    imagefill($idst, 0, 0, $background);

    // копируем существующее изображение в новое с изменением размера
    imagecopyresampled(
        $idst, // идентификатор нового изображения
        $isrc, // идентификатор исходного изображения
        $new_left, $new_top, // координаты (x,y) верхнего левого угла в новом изображении
        0, 0, // координаты (x,y) верхнего левого угла копируемого блока существующего изображения
        $new_width, // новая ширина копируемого блока
        $new_height, // новая высота копируемого блока
        $size[0], // ширина исходного копируемого блока
        $size[1] // высота исходного копируемого блока
    );

    // сохраняем результат
    if (empty($res)) $res = $format;
    $func = 'image' . $res;
    if ( ! function_exists($func)) return false;

    $func($idst, $dst);

    imagedestroy($isrc);
    imagedestroy($idst);

    return true;
}















