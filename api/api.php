<?php
/**
 * @author Haymer Barbetti <hbarbetti.ing@icloud.com>
 * @see https://github.com/haimerb
 **/
include_once './config/database.php';
require "../vendor/autoload.php";
include_once '../files-handler-core/handler.php';
include_once './user.php';
$configs=include('./config/config.php');

use \Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('files-logger');
$logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

$pathInfo = $_SERVER['PATH_INFO'];
$method=$_SERVER['REQUEST_METHOD'];

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$nit = isset($data->nit) ? $data->nit : "";
$tipo_retencion = isset($data->tipo_retencion) ? $data->tipo_retencion : "";
$year_tribute = isset($data->year_tribute) ? $data->year_tribute : "";
$idOrganizacion = isset($data->idOrganizacion) ? $data->idOrganizacion : "" ;

$sinceRange=isset($data->rangeSince) ? $data->rangeSince : "" ;
$untilRange=isset($data->rangeUntil) ? $data->rangeUntil : "" ;
/**
 * Set Header 
 */
header('Access-Control-Allow-Origin: *');
header("Allow: GET, POST, PUT, DELETE, UPDATE, OPTIONS");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, UPDATE, OPTIONS");
header("Content-Type: application/json; charset=utf-8");
header("Accept: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
/**
 * http petitions
 */
$logger->info("METHOD: " . $method);
/**
 * Get
 */
if ($method === 'GET') {
    if ($pathInfo === '/user/rol') {
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $data = json_decode(file_get_contents("php://input"));
        $idUser = isset($data->idUser) ? $data->idUser :  $_REQUEST['idUser'];
        http_response_code(200);
        $logger->info("idUser: " .$idUser);
        getRolsByIdUser($conn, $idUser);
    }
    if($pathInfo === '/certificate/getInfoPreBase'){
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $nit = isset($data->nit) ? $data->nit :$_REQUEST['nit'];
        $tipo_retencion = isset($data->tipo_retencion) ? $data->tipo_retencion :$_REQUEST['tipo_retencion'];
        $year_tribute = isset($data->year_tribute) ? $data->year_tribute :$_REQUEST['year_tribute'];
        $idOrganizacion = isset($data->idOrganizacion) ? $data->idOrganizacion : $_REQUEST['idOrganizacion'];
        getInfoPreBase($conn, $nit, $tipo_retencion, $year_tribute, $idOrganizacion);        
    }
/**
 * POST
 */
} elseif ($method === 'POST') {
    if ($pathInfo === '/certificate/base') {
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $data = json_decode(file_get_contents("php://input"));
        $nit = isset($data->nit) ? $data->nit : "";
        $tipo_retencion = isset($data->tipo_retencion) ? $data->tipo_retencion : "";
        $year_tribute = isset($data->year_tribute) ? $data->year_tribute : "";
        $idOrganizacion = isset($data->idOrganizacion) ? $data->idOrganizacion : "" ;
        $sinceRange=isset($data->rangeSince) ? $data->rangeSince : "" ;
        $untilRange=isset($data->rangeUntil) ? $data->rangeUntil : "" ;
        generarBase($conn, $nit, $tipo_retencion, $year_tribute, $idOrganizacion, $sinceRange, $untilRange);
    }
    if ($pathInfo === '/files/procesFile') {
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $data = json_decode(file_get_contents("php://input"));
        $nameFile = isset($data->nameFile) ? $data->nameFile : "";
        http_response_code(200);
        readFileXlsx($nameFile, $conn, true, array());
    }
/**
 * PUT 
 */
} elseif ($method === 'PUT') {
    if ($pathInfo === '/user/update') {
        $data = json_decode(file_get_contents("php://input"));
        $idUser = isset($data->idUser) ? $data->idUser : "";
        $namesUser = isset($data->namesUser) ? $data->namesUser : "";
        $lastNamesuser = isset($data->lastNamesuser) ? $data->lastNamesuser : "";
        $passwordUser = isset($data->passwordUser) ? $data->passwordUser : "";
        changePassword($conn, $passwordUser);
    }

} else {
    echo json_encode(array("message" => "Mehtod not allowed", "code" => "401"));
}

?>