<?php
/**
 * Абстрактный класс Frontend_Model, родительский для всех моделей
 * административной части сайта
 */
abstract class Backend_Model extends Base_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция для изменения размеров изображения
     * Параметры:
     * $src - имя исходного файла
     * $dst - имя генерируемого файла
     * $width, $height - ширина и высота генерируемого изображения, в пикселях
     * Необязательные параметры:
     * $res - формат выходного файла (jpg, gif, png), по умолчанию - формат входного файла
     * $rgb - цвет фона, по умолчанию - белый
     */
    protected function resizeImage($src, $dst, $width, $height, $res = '', $rgb = array(255,255,255)) {
        if ( ! in_array($res, array('', 'jpg', 'jpeg', 'gif', 'png'))) return false;
        if ('jpg' == $res) $res = 'jpeg';

        if ( ! file_exists($src)) return false;
        $size = getimagesize($src);
        if (false === $size) return false;

        // определяем исходный формат по MIME-информации, предоставленной функцией
        // getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию
        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
        // если ширина и высота изображения уже имеют нужное значение
        if (($size[0] == $width && $size[1] == $height) || ($size[0] == $width && 0 == $height)) {
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
}