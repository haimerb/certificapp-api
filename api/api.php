<?php
include_once './config/database.php';
require "../vendor/autoload.php";
include_once '../files-handler-core/handler.php';
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

//echo $sinceRange,$untilRange;

/**
 * Set Header
 */
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST GET");
header("Access-Control-Max-Age: 3600");
//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Get petitions
 */

$logger->info("METHOD: ".$method);

if($method==='GET'){
    
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    echo json_encode(array("message" => "not implementations", "code" => "200",JSON_INVALID_UTF8_IGNORE));

}elseif($method==='POST'){
    
    if($pathInfo==='/files/base'){
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        generarBase($conn, $nit, $tipo_retencion, $year_tribute,$idOrganizacion,$sinceRange,$untilRange);
    }
    if($pathInfo==='/files/procesFile'){
        
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        $data = json_decode(file_get_contents("php://input"));
        $nameFile = isset($data->nameFile) ? $data->nameFile : "";
        
        http_response_code(200);
        readFileXlsx($nameFile, $conn,true,array());
    }

    // if ($pathInfo === '/files/generarPdf') {

    //     header("Content-Type: application/json; charset=utf-8");
    //     header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
    //     $data = json_decode(file_get_contents("php://input"));
    //     $idCertificate = isset($data->idCertificate) ? $data->idCertificate : "";

    //     http_response_code(200);
    //     generateDocPdf($conn, $idCertificate);

    // }

}else{
    
    echo json_encode(array("message" => "Mehtod not allowed","code" => "401"));

}

?>