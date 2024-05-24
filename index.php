<?php


//header("Access-Control-Allow-Origin: *");
 require "../apext-api/vendor/autoload.php";
// include '../apext-api/core/view/landing.php';
$nuevaURL='/core/view/landing';
//******************header('Location: '.$nuevaURL.'.php');

require_once 'config/config.php';



//print_r(get_include_path());

//$path = constant("PROJECT_INCLUDE_PATH")."/";
//print_r($path);
echo "<br></br>";
//print_r(constant("PATH_CONTROLLER_CONFIG"));

//set_include_path($path);

//print_r(get_include_path());



if(!isset($_GET["controller"])) $_GET["controller"] = constant("DEFAULT_CONTROLLER");
if(!isset($_GET["action"])) $_GET["action"] = constant("DEFAULT_ACTION");

$controller_path = 'core/controller/'.$_GET["controller"].'.php';

/**
 * verifiaca si existen los controladores
 */
if(!file_exists($controller_path)) $controller_path = 'core/controller/'.constant("DEFAULT_CONTROLLER").'.php';


/**
 * Se cargan los controladores
 */
require_once $controller_path;
$controllerName = $_GET["controller"].'Controller';
$controller = new $controllerName();
//--$controllerName = $_GET["controller"].'Controller';
//--$controller = new $controllerName();


/**
 * Verifica si estan definidos los metodos
 */
$dataToView["data"] = array();
if(method_exists($controller,$_GET["action"])) $dataToView["data"] = $controller->{$_GET["action"]}();

/**
 * Se cargan las vistas
 */
require_once 'core/view/template/header.php';
require_once 'core/view/'.$controller->view.'.php';
require_once 'core/view/template/footer.php';

?>
