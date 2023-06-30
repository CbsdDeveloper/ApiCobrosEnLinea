<?php

define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// Include database config
include PROJECT_ROOT_PATH . '/config/dbconfig.php';

// include the base controller file
require_once PROJECT_ROOT_PATH . "/controller/Api/BaseController.php";

// include the model files
require_once PROJECT_ROOT_PATH . "/model/ordenesCobroModel.php";


// incluyendo vendor para exportar a pdf
require_once PROJECT_ROOT_PATH . '/vendor/autoload.php';