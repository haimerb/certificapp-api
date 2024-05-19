<?php

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type:  multipart/form-data; charset=utf-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require "../vendor/autoload.php";
include_once '../files-handler-core/handler.php';

use \Firebase\JWT\JWT;

if (!isset($_FILES['fileUpload']['error']) ||is_array($_FILES['fileUpload']['error'])){
    throw new RuntimeException('Invalid parameters.');
}else{
    $file_name     = $_FILES["fileUpload"]["name"];
    $file_type     = $_FILES["fileUpload"]["type"];
    $file_size     = $_FILES["fileUpload"]["size"];
    $file_tmp_name = $_FILES["fileUpload"]["tmp_name"];
    $file_error = $_FILES["fileUpload"]["error"];
}

$pathInfo = $_SERVER['PATH_INFO'];

// function upLoad($file_name,$file_type,$file_size,$file_tmp_name,$file_error) {
//     //$dir_subida=__DIR__."\\tmp\\";
//     $dir_subida="C:/Users/hbarb/OneDrive/fuentes/atg/back/apext-api/api/tmp/";
    
    
//         $fichero_subido = $dir_subida . basename($file_name);
        
//         print_r("\n El Archivo_ ".$fichero_subido."\n");

//         if (move_uploaded_file( $file_tmp_name,$fichero_subido)) {
//             echo json_encode(
//                 array(
//                     "result" =>"El fichero es válido y se subió con éxito."               
//                 ));        
//         }else {
//             echo json_encode(
//                 array(
//                     "result" =>"¡Posible ataque de subida de ficheros!"               
//                 ));        
//         }
    
    
// }

function generarPdf(){
    return "";
}

// function testRead(){
//     ///require ('../files-handler-core/handler.php');
// }


if($pathInfo==='/files'){
    uploadFile($file_name,$file_type,$file_size,$file_tmp_name,$file_error);    
}
if($pathInfo==='/files/validate'){
    //upLoad($file_name,$file_type,$file_size,$file_tmp_name,$file_error);
}
if($pathInfo==='/files/generarPdf'){
    //generarPdf();
    //testRead();
    //------readFileXlsx("Asignaciones_Líneas_Johan Mejía.xlsx");
    generateDocPdf();
    
}
if($pathInfo==='/files/procesFile'){
    readFileXlsx("Asignaciones_Líneas_Johan Mejía.xlsx");    
}
