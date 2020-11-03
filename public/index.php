<?php
require "../bootstrap.php";
use Src\Controller\CampaignController;
use Src\Controller\PlayerController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$routes = array("campaign","player");

if (!in_array($uri[1], $routes)) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the user id is, of course, optional and must be a number:
$Id = null;
if (isset($uri[2])) {
    $Id = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and user ID to the PersonController and process the HTTP request:
switch($uri[1]) {
    case 'campaign':
        $controller = new CampaignController($dbConnection, $requestMethod, $Id);
    break;
    case 'player':
        $controller = new PlayerController($dbConnection, $requestMethod, $Id);
    break;
}

$controller->processRequest();
?>