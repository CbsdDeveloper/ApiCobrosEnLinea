<?php

require __DIR__ . "/inc/bootstrap.php";
// require __DIR__ . '/vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode('/', $uri);

// print_r($uri);


$arrControllers = array("ordenesCobro");


if ((isset($uri[3]) && !in_array($uri[3],$arrControllers)) || !isset($uri[4])) {

    header("HTTP/1.1 404 Not Found");

    exit();
}

// require PROJECT_ROOT_PATH . "/Controller/Api/ordenMovilizacionController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/ordenesCobroController.php";

// $objFeedController["ordenMovilizacion"] = new OrdenMovilizacionController();
$objFeedController["ordenesCobro"] = new OrdenesCobroController();

$strMethodName = $uri[4] . 'Action';

$objFeedController[$uri[3]]->{$strMethodName}();
