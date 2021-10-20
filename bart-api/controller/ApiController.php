<?php defined("BASEPATH") OR exit("No direct script access allowed");

require_once BASEPATH . "/config/config.php";
require_once BASEPATH . "/model/GalleryModel.php";

/**
 * Class ApiController API controller class that handles gallery and image preview
 */
class ApiController {

    /**
     * @var GalleryModel
     */
    private $model;

    function __construct() {
        $this->model = new GalleryModel();
    }

    function gallery_get($path = null) {
        if ($path === null) {
            send_response(200, array("galleries" => $this->model->list_galleries()));
        } else {
            $data = $this->model->get_gallery($path);
            if ($data != null) {
                send_response(200, $data);
            } else {
                send_response(404, "Gallery does not exist");
            }
        }
    }

    function gallery_post($path = null) {
        if ($path == null) { // create gallery
            // parse incoming data
            $input_data = file_get_contents("php://input");
            if ($input_data === false)
                send_response(400, '{"code": 400,"payload": {"paths": ["name"],"validator": "required","example": null},"name": "INVALID_SCHEMA","description": "Bad JSON object: u\\\'name\\\' is a required property"}');
            $data = json_decode($input_data, true);
            if ($data === null || !isset($data["name"]) || !is_string($data["name"]))
                send_response(400, '{"code": 400,"payload": {"paths": ["name"],"validator": "required","example": null},"name": "INVALID_SCHEMA","description": "Bad JSON object: u\\\'name\\\' is a required property"}');

            // create gallery
            $path = $this->model->create_gallery($data["name"]);
            if ($path === false) {
                send_response(409, "Gallery already exists");
            } elseif ($path === null) {
                send_response(500, "Failed to create gallery");
            } else {
                send_response(201, array("path" => $path, "name" => $data["name"]));
            }
        } else { // add image to gallery
            if (!isset($_POST["name"]) || !isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK)
                send_response(400, "Image or image name is missing");

            $size = getimagesize($_FILES["image"]["tmp_name"]);
            if ($size === false || ($size["mime"] != "image/jpeg" && $size["mime"] != "image/png"))
                send_response(400, "Unsupported image format");

            $name = $_POST["name"];
            $temp_file = $_FILES["image"]["tmp_name"];

            $details = $this->model->add_file($path, $temp_file, $name, $size["mime"]);
            if ($details === false) {
                send_response(404, "Gallery not found");
            } elseif ($details === null) {
                send_response(500, "Error processing the file");
            } else {
                send_response(201, array("uploaded" => [$details]));
            }
        }
    }

    function gallery_delete($gallery_path, $image_path = null) {
        if ($image_path == null) { // delete gallery
            if ($this->model->delete_gallery($gallery_path))
                send_response(200, "Gallery successfully deleted");
            else
                send_response(404, "Gallery does not exist or cannot be deleted");
        } else { // delete image
            if ($this->model->delete_image($gallery_path, $image_path))
                send_response(200, "Image successfully deleted");
            else
                send_response(404, "Image does not exist or cannot be deleted");
        }
    }

    function images_get($width, $height, $gallery, $image) {
        // check if file exists
        $image_file = $this->model->get_image_absolute_path($gallery, $image);
        if ($image_file == null)
            send_response(404, "Image does not exist");

        // check target size
        $width = intval($width);
        $height = intval($height);
        if ($width <= 0 && $height <= 0)
            send_response(500, "Invalid dimensions provided");

        // load image size
        $size = getimagesize($image_file);
        if ($size === false)
            send_response(500, "Error processing the image");
        $image_width = $size[0];
        $image_height = $size[1];
        if ($image_width == 0 || $image_height == 0)
            send_response(500, "Failed to load image size");

        if ($width <= 0) { // calculate scaled width
            $width = intval(round(($height / $image_height) * $image_width));
            if ($width == 0)
                $width = 1;
        } elseif ($height <= 0) { // calculate scaled height
            $height = intval(round(($width / $image_width) * $image_height));
            if ($height == 0)
                $height = 1;
        }
         $image_mime = $size["mime"];

        // load image
        $image = false;
        if ($image_mime == "image/jpeg") {
            $image = imagecreatefromjpeg($image_file);
        } elseif ($image_mime == "image/png") {
            $image = imagecreatefrompng($image_file);
        }
        if ($image === false) {
            send_response(500, "Unsupported image type");
        }

        // scale image
        $image = imagescale($image, $width, $height, IMG_BICUBIC);
        if ($image === false)
            send_response(500, "Failed to scale the image");

        // send image as stream
        header("Content-Type: image/png");
        imagepng($image,NULL,9);
    }

}
