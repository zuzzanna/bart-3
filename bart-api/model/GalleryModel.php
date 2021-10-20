<?php defined("BASEPATH") OR exit("No direct script access allowed");

class GalleryModel {

    /**
     * Creates gallery with specified name
     * @param $name string gallery name
     * @return bool|string|null false if gallery already exists; null if gallery cannot be created; gallery path otherwise
     */
    function create_gallery($name) {
        $path = convert_to_path_safe($name);

        // check if gallery with same name does not exist already
        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        $galleries = scandir($galleries_dir);
        if ($galleries === false) // cannot check if gallery exist -> pretend it does
            return false;
        $galleries = array_diff($galleries, array(".", ".."));
        foreach ($galleries as $gallery) {
            if (!is_dir($galleries_dir . DIRECTORY_SEPARATOR . $gallery))
                continue;
            $name_file = $galleries_dir . DIRECTORY_SEPARATOR . $gallery . DIRECTORY_SEPARATOR . "name.txt";
            if (is_file($name_file) && file_get_contents_utf8($name_file) == $name) {
                return false;
            }
        }

        // generate new gallery directory
        $gallery_dir = $galleries_dir . DIRECTORY_SEPARATOR . $path;
        $random_characters = "abcdefghijklmnopqrstuvwyz0123456789_-";
        while (is_dir($gallery_dir)) {
            $rnd = $random_characters[rand(0, strlen($random_characters) - 1)];
            $gallery_dir = $gallery_dir . $rnd;
            $path = $path . $rnd;
        }
        $gallery_name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";

        // create gallery directory
        if (!mkdir($gallery_dir, 0777, true))
            return null;

        // save gallery name
        if (!file_set_contents_utf8($gallery_name_file, $name))
            return null;

        return $path;
    }

    /**
     * Saves uploaded image file
     * @param $gallery string gallery path where image should be saved
     * @param $file_path string temporary image path
     * @param $name string target image name
     * @param $mime string image mime type to determine extension
     * @return array|bool|null false if gallery does not exist; null in case of internal error; on success array containing details
     */
    function add_file($gallery, $file_path, $name, $mime) {
        if (!is_safe_path($gallery))
            return false;

        // check if gallery exists
        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        $gallery_dir = $galleries_dir . DIRECTORY_SEPARATOR . $gallery;
        $gallery_name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";
        if (!is_dir($gallery_dir) || !is_file($gallery_name_file))
            return false;

        // generate paths
        $safe_name = convert_to_path_safe($name);
        $ext = "";
        if ($mime == "image/jpeg")
            $ext = ".jpg";
        elseif ($mime == "image/png")
            $ext = ".png";
        
        $random_characters = "abcdefghijklmnopqrstuvwyz0123456789_-";
        while (is_file($gallery_dir . DIRECTORY_SEPARATOR . $safe_name . $ext)) {
            $rnd = $random_characters[rand(0, strlen($random_characters) - 1)];
            $safe_name = $safe_name . $rnd;
        }
        $image_file = $gallery_dir . DIRECTORY_SEPARATOR . $safe_name . $ext;
        $image_name_file = $image_file . ".txt";

        // save image and write image name
        if (move_uploaded_file($file_path, $image_file) && file_set_contents_utf8($image_name_file, $name)) {
            return array(
                "path" => $safe_name . $ext,
                "fullpath" => $gallery . "/" . $safe_name . $ext,
                "name" => $name,
                "modified" => date("c", filemtime($image_file))
            );
        }

        return null;
    }

    /**
     * Function return array of galleries with their respective name and path
     * @return array{'path' => string, 'name' => string}
     */
    function list_galleries() {
        // check if assets directory exists
        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        if (!is_dir($galleries_dir))
            return [];

        // load assets dir contents
        $files = scandir($galleries_dir);
        if ($files === false)
            return [];
        $files = array_diff($files, array(".", ".."));
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        // filter gallery directories
        $result = [];
        foreach ($files as $f) {
            $path = $f;
            $f = $galleries_dir . DIRECTORY_SEPARATOR . $f;
            if (!is_dir($f))
                continue;

            $name_file = $galleries_dir . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "name.txt";
            if (is_file($name_file)) { // load gallery name from file
                $name = file_get_contents_utf8($name_file);
            } else { // this is probably not a gallery folder
                // $name = $path;
                continue;
            }

            array_push($result, array("name"=>$name, "path"=>$path));
        }

        return $result;
    }

    /** Gets gallery details and image list
     * @param $path string target gallery path
     * @return array{"gallery"=>array{"path"=>string,"name"=>string},"images"=>array[array{"path"=>string,"fullpath"=>string,"name"=>string,"modified"=>string}]} gallery details or null if gallery does not exist
     */
    function get_gallery($path) {
        if (!is_safe_path($path)) // check that path is valid
            return null;

        // check if assets directory exists
        $gallery_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . $path;
        $name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";
        if (!is_dir($gallery_dir) || !is_file($name_file))
            return null;

        $name = file_get_contents_utf8($name_file);
        $image_files = scandir($gallery_dir);
        sort($image_files, SORT_NATURAL | SORT_FLAG_CASE);
        $image_files = array_diff($image_files, array('.', '..'));
        $images = [];
        foreach ($image_files as $img) {
            $image_file = $gallery_dir . DIRECTORY_SEPARATOR . $img;
            $name_file = $image_file . ".txt";
            if (!is_image_file($img) || !is_file($image_file) || !is_file($name_file))
                continue;

            $details = array(
                "path" => $img,
                "fullpath" => $path . "/" . $img,
                "name" => file_get_contents_utf8($name_file),
                "modified" => date("c", filemtime($image_file))
            );
            array_push($images, $details);
        }

        

        return array(
            "gallery" => array("path" => $path, "name" => $name),
            "images" => $images
        );
    }

    /**
     * Function deletes entire gallery
     * @param $path string gallery path to delete
     * @return bool True if gallery is deleted; false otherwise
     */
    function delete_gallery($path) {
        if (!is_safe_path($path))
            return false;

        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        $gallery_dir = $galleries_dir . DIRECTORY_SEPARATOR . $path;
        $gallery_name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";
        if (!is_dir($gallery_dir) || !is_file($gallery_name_file))
            return false;

        return delete_directory($gallery_dir);
    }

    /**
     * Function deletes single image from gallery
     * @param $path string gallery path
     * @param $image string image path
     * @return bool True if gallery is deleted; false otherwise
     */
    function delete_image($path, $image) {
        if (!is_safe_path($path) || !is_safe_path($image))
            return false;

        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        $gallery_dir = $galleries_dir . DIRECTORY_SEPARATOR . $path;
        $gallery_name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";
        if (!is_dir($gallery_dir) || !is_file($gallery_name_file))
            return false;

        // remove image and its name file
        $image_file = $gallery_dir . DIRECTORY_SEPARATOR . $image;
        $image_name_file = $image_file . ".txt";
        if (is_file($image_file) && is_file($image_name_file)) {
            return unlink($image_file) && unlink($image_name_file);
        }

        return false;
    }

    /**
     * Finds target image absolute path
     * @param $gallery string gallery path
     * @param $image string image path
     * @return string|null absolute path or null on failure
     */
    function get_image_absolute_path($gallery, $image) {
        if (!is_safe_path($gallery) || !is_safe_path($image)){
            return null;
        }

        // check if gallery exists
        $galleries_dir = realpath(BASEPATH) . DIRECTORY_SEPARATOR . "assets";
        $gallery_dir = $galleries_dir . DIRECTORY_SEPARATOR . $gallery;
        $gallery_name_file = $gallery_dir . DIRECTORY_SEPARATOR . "name.txt";
        if (!is_dir($gallery_dir) || !is_file($gallery_name_file))
            return null;

        // check if image exists
        $image_file = $gallery_dir . DIRECTORY_SEPARATOR . $image;
        $image_name_file = $image_file . ".txt";
        if (!is_file($image_file) || !is_file($image_name_file))
            return null;

        return $image_file;
    }

}