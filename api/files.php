<?php
/**
 * @author Haymer Barbetti <hbarbetti.ing@icloud.com>
 * @see https://github.com/haimerb
 **/
require "../vendor/autoload.php";
include_once '../files-handler-core/handler.php';

$configs = include ('./config/config.php');
include_once './config/database.php';

use \Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

header("Access-Control-Allow-Origin: http://localhost:4200");
//header("Content-Type: data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");
//header("Content-Type:  application/pdf; multipart/form-data; charset=utf-8");

//header("Content-Type:  application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Type: application/json; charset=UTF-8");

header("Accept-Encoding:  gzip, deflate, br");
//header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8");
//header("Content-Type: application/json; charset=UTF-8");
//header("Content-Type: application/plain; charset=UTF-8");
//header("Content-Type:  multipart/form-data; charset=utf-8");
//header("Content-Encoding:  gzip, deflate, br, zstd");
header("Access-Control-Allow-Methods: GET POST");
header("Cache-Control: no-cache");

header("Access-Control-Max-Age: 3600");
//header("Accept: */*");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$logger = new Logger('files-logger');
$logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$nameFile = isset($data->nameFile) ? $data->nameFile : "";
$idCertificate = isset($data->idCertificate) ? $data->idCertificate : "";
$file = isset($data->file) ? $data->file : "";

$sinceRange=isset($data->rangeSince) ? $data->rangeSince : "" ;
$untilRange=isset($data->rangeUntil) ? $data->rangeUntil : "" ;

$pathInfo = $_SERVER['PATH_INFO'];
$method = $_SERVER['REQUEST_METHOD'];

function downloaadFile($file)
{   
    $data = json_decode(file_get_contents("php://input"));
    $env=isset($data->env)?$data->env:false;
    $prodEnv="https://www.ti-soluciones.co/certificapp/apext-api";
    $DevEnv="http://localhost:8000";
    $url ='';

    if($env!==''&&$env!==null){
        if($env===true){
            $url = $prodEnv."/api/tmp/mpdf/outfiles/" . $file;
        }else{
            $url = $DevEnv."/api/tmp/mpdf/outfiles/" . $file;
        }
        
    }
    //$url = "/api/tmp/mpdf/outfiles/" . $file;
    echo json_encode(
        array(
            "url" => $url,
            "code" => "200"
        )
    );
}

if ($pathInfo === '/files/validate') {
    /**
     * Implement validations
     */
}

/**
 * 
 * http petitions
 */
if ($method === 'GET') {

    if ($pathInfo === '/files/downloadFile') {
        downloaadFile($file);
        $logger->info("Downloading file: " . $file);
        http_response_code(200);
    }
    
} elseif ($method === 'POST') {
    
    if ($pathInfo === '/files/generarPdf') {
        $outPutNameFile = "A" . time() . '.pdf';
        $outPutDirFile = 'tmp/mpdf/outfiles/' . $outPutNameFile;
        generateDocPdf($conn, $idCertificate,$outPutNameFile);
    }

    if ($pathInfo === '/files') {
        http_response_code(200);
        if (!isset($_FILES['fileUpload']['error']) || is_array($_FILES['fileUpload']['error'])) {
            echo "ERROR";
            $logger->error("Error: ".$_FILES['fileUpload']['error']);
            throw new RuntimeException('Invalid parameters.');
        } else {
            $file_name = $_FILES["fileUpload"]["name"];
            $file_type = $_FILES["fileUpload"]["type"];
            $file_size = $_FILES["fileUpload"]["size"];
            $file_tmp_name = $_FILES["fileUpload"]["tmp_name"];
            $file_error = $_FILES["fileUpload"]["error"];
        }
        uploadFile($file_name, $file_type, $file_size, $file_tmp_name, $file_error,$conn);
    }
    /**
     * @deprecated Migrated to api core
     */
    if ($pathInfo === '/files/procesFile') {
        
        header("Access-Control-Allow-Origin: http://localhost:4200");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET POST");
        header("Accept-Encoding:  gzip, deflate, br");
        header("Cache-Control: no-cache");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        //http_response_code(200);
        readFileXlsx($nameFile, $conn,true,array());

    }
    /**
     * @deprecated Migrated to api core
     */
    if ($pathInfo === '/files/base') {

        $nit = isset($data->nit) ? $data->nit : "";
        $tipo_retencion = isset($data->tipo_retencion) ? $data->tipo_retencion : "";
        $year_tribute = isset($data->year_tribute) ? $data->year_tribute : "";
        $idOrganizacion = isset($data->idOrganizacion) ? $data->idOrganizacion : (isset($_REQUEST['idOrganizacion'])?$_REQUEST['idOrganizacion']:"") ;
        generarBase($conn, $nit, $tipo_retencion, $year_tribute,$idOrganizacion,$sinceRange,$untilRange);
    }

} elseif ($method === 'PUT') {
    
    http_response_code(200);
    echo json_encode(array("message" => "not implementations", "code" => "200",JSON_INVALID_UTF8_IGNORE));

} elseif ($method === 'OPTIONS') {
    
    http_response_code(200);
    echo json_encode(array("message" => "not implementations", "code" => "200",JSON_INVALID_UTF8_IGNORE));
    
}else {
    echo json_encode(array("message" => "Mehtod not allowed", "code" => "401"),JSON_INVALID_UTF8_IGNORE);
}