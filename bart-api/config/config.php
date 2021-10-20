<?php defined("BASEPATH") OR exit("No direct script access allowed");


/** Function sends response to user. Function terminates PHP script execution with die()
 * @param int $code target response code
 * @param string|array $msg message that will be send to user
 */
function send_response($code = 200, $msg = "OK") {
    http_response_code($code);
    if (is_array($msg)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($msg);
    } else {
        echo $code . " - " . $msg;
    }
    die();
}

/** Converts name to path-safe name
 * @param $name string name to be converted to path
 * @return string converted path
 */
function convert_to_path_safe($name) {
    // remove accents
    $replace = array(
        'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
        'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
        'Þ'=>'B',
        'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
        'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
        'Ğ'=>'G',
        'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
        'Ł'=>'L',
        'Ñ'=>'N', 'Ń'=>'N',
        'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
        'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
        'Ț'=>'T',
        'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
        'Ý'=>'Y',
        'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
        'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
        'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
        'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
        'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
        'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
        'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
        'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
        'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
        'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
        'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
        'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
        'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
        'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
        'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
        'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
        'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
        'ק'=>'q',
        'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
        'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
        'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
        'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
        'в'=>'v', 'ו'=>'v', 'В'=>'v',
        'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
        'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
        'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
    );
    $path = strtr($name, $replace);

    // replace non-safe characters with underscore
    $path = preg_replace("/[^a-zA-Z0-9_-]/", "_", $path);

    if (strlen($path) > 20)
        $path = substr($path, 0, 20);

    return $path;
}

/**
 * Checks whether path is safe for use or not
 * @param $path string path to be checked
 * @return bool true if path is valid; false otherwise
 */
function is_safe_path($path) {
    return preg_match("/[a-zA-Z0-9_.\\-]+$/", $path) === 1 && trim($path, ".") !== "";
}

/** Reads file contents from specified path in UTF-8
 * @param $fn string file path
 * @return string decoded file contents
 */
function file_get_contents_utf8($fn) {
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

/** Writes file contents to specified path in UTF-8
 * @param $fn string file path
 * @param $content string file contents
 * @return bool true on success; false otherwise
 */
function file_set_contents_utf8($fn, $content) {
    return file_put_contents($fn, $content) == strlen($content);
}

/**
 * Function checks that file name contains supported image file extension
 * @param $file string file name
 * @return bool true if extension is supported
 */
function is_image_file($file) {
    $file = strtolower($file);
    $supported_extensions = [".jpg", ".jpeg", ".png"];
    foreach ($supported_extensions as $extension) {
        $ext_length = strlen($extension);
        if ($ext_length == 0 || substr($file, -$ext_length) == $extension)
            return true;
    }

    return false;
}

/**
 * Deletes non-empty directory
 * @param $dir string directory path
 * @return bool true on success; false otherwise
 */
function delete_directory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}
