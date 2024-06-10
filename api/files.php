<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
// header("Content-Type:  application/pdf; multipart/form-data; charset=utf-8");
header("Content-Type: application/json; charset=UTF-8");
//header("Content-Type:  multipart/form-data; charset=utf-8");
header("Access-Control-Allow-Methods: GET POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require "../vendor/autoload.php";
include_once '../files-handler-core/handler.php';

$configs=include('./config/config.php');
include_once './config/database.php';

use \Firebase\JWT\JWT;

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();


$data = json_decode(file_get_contents("php://input"));

$whitOutSave=isset($data->whitOutSave)?$data->whitOutSave:"";
$ini = isset($data->range_ini)?$data->range_ini:"";
$end = isset($data->range_end)?$data->range_end:"";


 $pathInfo = $_SERVER['PATH_INFO'];
 $method = $_SERVER['REQUEST_METHOD'];

function generarPdf(){
    return "";
}

// function testRead(){
//     ///require ('../files-handler-core/handler.php');
// }


// if($pathInfo==='/files'){
//     uploadFile($file_name,$file_type,$file_size,$file_tmp_name,$file_error);    
// }
if($pathInfo==='/files/validate'){
    //upLoad($file_name,$file_type,$file_size,$file_tmp_name,$file_error);
}
if($pathInfo==='/files/generarPdf'){
    //generarPdf();
    //testRead();
    //------readFileXlsx("Asignaciones_Líneas_Johan Mejía.xlsx");
    generateDocPdf();
    
}

/**
 * 
 * http petitions
 */
if($method==='GET'){
    http_response_code(200);
    echo json_encode(array("message" => "not implementations","code"=>"200"));
}elseif($method==='POST'){

    if($pathInfo==='/files'){
        if (!isset($_FILES['fileUpload']['error']) ||is_array($_FILES['fileUpload']['error'])){
            throw new RuntimeException('Invalid parameters.');
        }else{
            $file_name     = $_FILES["fileUpload"]["name"];
            $file_type     = $_FILES["fileUpload"]["type"];
            $file_size     = $_FILES["fileUpload"]["size"];
            $file_tmp_name = $_FILES["fileUpload"]["tmp_name"];
            $file_error = $_FILES["fileUpload"]["error"];
        }
        http_response_code(200);
        uploadFile($file_name,$file_type,$file_size,$file_tmp_name,$file_error);    
    }
    if($pathInfo==='/files/procesFile'){
        //http_response_code(200);
        readFileXlsx("ICA indicador base Bim I 2023.xlsx",$conn,$whitOutSave,$ini,$end);    
    }

}elseif($method==='PUT') {
    http_response_code(200);
    echo json_encode(array("message" => "not implementations","code"=>"200"));
}else{
    http_response_code(401);
    echo json_encode(array("message" => "Mehtod not allowed","code"=>"401"));
}



