<?php

define("BASEPATH", __DIR__);

require_once "config/config.php";
require_once "controller/ApiController.php";

// clear incoming URI
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = urldecode($uri);
if (strpos($uri, "/index.php/") === 0)
    $uri = substr($uri, strlen("/index.php"));
if (strpos($uri, "/api/") === 0)
    $uri = substr($uri, strlen("/api"));  

// http://api.programator.sk/docs
// routing array -> first group is always function name
$routing = ["/^\\/(gallery)\\/?$/", "/^\\/(gallery)\\/([^\\/]+)\\/?$/", "/^\\/(gallery)\\/([^\\/]+)\\/([^\\/]+)\\/?$/",
    "/^\\/(images)\\/(\\d+)x(\\d+)\\/([^\\/]+)\\/([^\\/]+)\\/?$/"];
$api_controller = new ApiController();

// find suitable handler
foreach ($routing as $route) {
    if (preg_match($route, $uri, $matches) === 1) {
        array_shift($matches); // ignore full match
        $target_method = array_shift($matches) . "_" . strtolower($_SERVER["REQUEST_METHOD"]);
        if (method_exists($api_controller, $target_method) && call_user_func_array([&$api_controller, $target_method], $matches) !== false) {
            die();
        }
    }
}

send_response(404, "Not found");


