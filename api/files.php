<?php
/**
 * @author Haymer Barbetti <hbarbetti.ing@gmail.com>
 */

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

header("Content-Type:  application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//header("Content-Type: application/json; charset=UTF-8");

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


// $opciones = array(
//     'http'=>array(
//       'method'=>"POST",
//       'header'=>"Accept-language: en\r\n" .
//                 "Cookie: foo=bar\r\n"
//     )
//   );

  //$contexto = stream_context_create($opciones);

$data = json_decode(file_get_contents("php://input"));
//$logger->info("data: ".$data->tipo_retencion);
//$fileUpload=base64_encode(isset($data->fileUpload)?$data->fileUpload:"");
//echo $fileUpload;

// if (!isset($_FILES['fileUpload']['error']) ||is_array($_FILES['fileUpload']['error'])){
//     echo "ERROR";
//     throw new RuntimeException('Invalid parameters.');
// }else{
//     $file_name     = $_FILES["fileUpload"]["name"];
//     $file_type     = $_FILES["fileUpload"]["type"];
//     $file_size     = $_FILES["fileUpload"]["size"];
//     $file_tmp_name = $_FILES["fileUpload"]["tmp_name"];
//     $file_error = $_FILES["fileUpload"]["error"];
// }


//-$whitOutSave = isset($data->whitOutSave) ? $data->whitOutSave : "";
//-$ini = isset($data->range_ini) ? $data->range_ini : "";
//-$end = isset($data->range_end) ? $data->range_end : "";
//-$dataType = isset($data->dataType) ? $data->dataType : "";
$nameFile = isset($data->nameFile) ? $data->nameFile : "";
$idCertificate = isset($data->idCertificate) ? $data->idCertificate : "";
$file = isset($data->file) ? $data->file : "";

//-$year_tribute= isset($data->year_tribute) ? $data->year_tribute:"";
//echo $_POST['dataType'];
//print_r ($nit." \n ".$tipo_retencion." \n ".$year_tribute." \n idOrganizacion: ".$idOrganizacion );

//$logger->info("Input Params: ".$nit." \n ".$tipo_retencion." \n ".$year_tribute." \n idOrganizacion: ".$idOrganizacion);


$pathInfo = $_SERVER['PATH_INFO'];
$method = $_SERVER['REQUEST_METHOD'];


/**
 * @
 * 
 */
// function generarBase($conn, $nit, $tipo_retencion, $year,$idOrganizacion){
    
//     // header("Access-Control-Allow-Origin: http://localhost:4200");
//     // header("Content-Type: application/json; charset=UTF-8");
//     // header("Access-Control-Allow-Methods: GET POST");
//     // header("Access-Control-Max-Age: 36600");
//     // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//     $certificatesData=[];
//     $nombreCert="";
//     $dateNow=date("Y-m-d");
//     $concepto="COMPRAS Y/O SERVICIOS";
//     $querySelect = 'select * from certificate_data 
//      where dataType=:dataType
//        and nit=:nit
//        and year_tribute=:year_tribute';

//     $logger = new Logger('files-logger');
//     $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));
//     $logger->info("Select Query".$querySelect);
//     try {
//         $stmt = $conn->prepare($querySelect);
//         $stmt->bindParam(':dataType',$tipo_retencion);
//         $stmt->bindParam(':nit', $nit);
//         $stmt->bindParam(':year_tribute', $year);
//         $stmt->execute();
//         $num = $stmt->rowCount();
//     } catch (PDOException $e) {
//         echo json_encode(array("message" => "Error!" . $e->getMessage()),JSON_INVALID_UTF8_IGNORE);
//     }    
//     $logger->info("Result count certificate_data: ".$num);
//     if ($num > 0) {
//         $row = $stmt->fetchAll();

//         for ($i=0; $i<$num;$i++)
//         {
//             array_push($certificatesData,
//                 array(
//                     "id_certificate_data_ica"=>$row[$i]["id_certificate_data_ica"],
//                     "tipo_retencion"=>$row[$i]["tipo_retencion"],
//                     "nit"=>$row[$i]["nit"],
//                     "razon_social"=>$row[$i]["razon_social"],
//                     "nombre_concepto"=>$row[$i]["nombre_concepto"],
//                     "base"=>$row[$i]["base"],
//                     "valor_retenido"=>$row[$i]["valor_retenido"],
//                     "porcentaje"=>$row[$i]["porcentaje"],
//                     "year_tribute"=>$row[$i]["year_tribute"],
//                     "range_ini"=>$row[$i]["range_ini"],
//                     "range_end"=>$row[$i]["range_end"],
//                     "dataType"=>(float)$row[$i]["dataType"]
//                 )
//             );
//         }
//         $logger->info("OBJ certificatesData: ".json_encode($certificatesData,JSON_INVALID_UTF8_IGNORE));
//         $lastCertificateGen=0;
//         $lastCertificateValue=0;
        
//         $logger->info("Result count certificatesData: ".count($certificatesData));

//         $insertQuery='INSERT INTO certificados_generados (tipo_certificado,
//                                            nombre,
//                                            organization_asociate,
//                                            ret_razon_social,
//                                            ret_nit,
//                                            ret_concepto_retencion,
//                                            anio_gravable,
//                                            createat,
//                                            url_assoc_file) 
//                         VALUES(:tipo_certificado,:nombre,:organization_asociate,:ret_razon_social,:ret_nit,:ret_concepto_retencion,:anio_gravable,:createat,"")';
            
//             $logger->info("Insert Query: ".$insertQuery);

//             $nombreCert=($tipo_retencion==1)?$nit."-ICA":$nit."-IVA";

//             $stmt = $conn->prepare($insertQuery);
//             $stmt->bindParam(':tipo_certificado',$tipo_retencion);
//             $stmt->bindParam(':nombre', $nombreCert);
//             $stmt->bindParam(':organization_asociate', $idOrganizacion);
//             $stmt->bindParam(':ret_razon_social', $certificatesData[0]["razon_social"]);
//             $stmt->bindParam(':ret_nit', $certificatesData[0]["nit"]);
//             $stmt->bindParam(':ret_concepto_retencion', $concepto);
//             $stmt->bindParam(':anio_gravable', $year);
//             $stmt->bindParam(':createat', $dateNow);            
            
//             $ejecute=$stmt->execute();
            
//             $logger->info("Insert Query: Inserta Registro!");

//             $lastCertificateGen=$conn->lastInsertId();
//             $num = $stmt->rowCount();

//             $logger->info("Inserted? : ".$ejecute." lastCertificateGen: ".$lastCertificateGen);

//             if($ejecute==1){
                
//             for ($i=0; $i<count($certificatesData);$i++){

//                 $insertValuesQuery='INSERT INTO values_certificates (concepto,base_retencion,valor_retenido) VALUES (?,?,?) ';

//                 $logger->info("Insert Query: ".$insertValuesQuery);
    
//                 $stmt = $conn->prepare($insertValuesQuery);
//                 $stmt->bindParam(1,$certificatesData[$i]["nombre_concepto"]);
//                 $stmt->bindParam(2,$certificatesData[$i]["base"]);
//                 $stmt->bindParam(3,$certificatesData[$i]["valor_retenido"]);
                
//                 $execute=$stmt->execute();
//                 $logger->info("Insert Query: Inserta Registro!");
//                 $lastCertificateValue=$conn->lastInsertId();
//                 $num = $stmt->rowCount();
                
//                 $logger->info("Result count values_certificates: ".$num);
//                 $logger->info("Result count certificatesData: ".json_encode($certificatesData[$i],JSON_INVALID_UTF8_IGNORE));

//                 if($execute==1){
//                     $insertRelations='INSERT INTO certificates_x_values (id_values,id_certificados_generado) VALUES (?,?); ';
                    
//                     $logger->info("Insert Query: ".$insertRelations." Data: lastCertificateGen".$lastCertificateGen." ".$lastCertificateValue);

//                     $logger->info("Result count lastCertificateGen - lastCertificateValue: ".$lastCertificateGen." ".$lastCertificateValue);
    
//                     $stmt = $conn->prepare($insertRelations);

//                     $stmt->bindParam(1,$lastCertificateValue );
//                     $stmt->bindParam(2,$lastCertificateGen);
                    
//                     //$num = $stmt->rowCount();
//                     //http_response_code(200);
//                     if($stmt->execute()){
//                         echo json_encode(
//                             array(
//                                 "message" => "Se creó el certificado con exito!",
//                                 "code" => "200",
//                                 "length" => $num." ".$lastCertificateGen
//                             ),JSON_INVALID_UTF8_IGNORE
//                         );
//                     }

//             }
//             generateDocPdf($conn,$lastCertificateGen);
//             }
//         // }


//         }else{
//             echo json_encode(array("message" => "No se encontraron registros", "code" => "401", "length" => 0, "data" => "" . $tipo_retencion . " " . $nit . " " . $year . " \n " . $querySelect,JSON_INVALID_UTF8_IGNORE));
//         }
        
       
//     } else {
//         echo json_encode(array("message" => "No se encontraron registros", "code" => "401", "length" => 0, "data" => "" . $tipo_retencion . " " . $nit . " " . $year . " \n " . $querySelect,JSON_INVALID_UTF8_IGNORE));
//     }
//     // return "";
// }

// function downloaadFile($file){
//     //echo $file;
//     $url = 'tmp/mpdf/outfiles/'.$file;
//     header("Content-type:application/pdf");
//     header('Content-Disposition: attachment; filename=' . $file);
//     readfile( $url );
// }

function downloaadFile($file)
{
    //header("Content-Type:application/json; charset=UTF-8");
    $url = "/api/tmp/mpdf/outfiles/" . $file;
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
    
    // if ($pathInfo === '/files/generateBase') {
    //     echo "ALGO!! ";
    //     generarBase($conn, $nit, $tipo_retencion, $year_tribute,$idOrganizacion);                
    // }

    if ($pathInfo === '/files/generarPdf') {
        generateDocPdf($conn, $idCertificate);
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
     * Migrated to api core
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

    if ($pathInfo === '/files/base') {

        $nit = isset($data->nit) ? $data->nit : "";
        $tipo_retencion = isset($data->tipo_retencion) ? $data->tipo_retencion : "";
        $year_tribute = isset($data->year_tribute) ? $data->year_tribute : "";
        $idOrganizacion = isset($data->idOrganizacion) ? $data->idOrganizacion : (isset($_REQUEST['idOrganizacion'])?$_REQUEST['idOrganizacion']:"") ;


            //echo "ALGO!! ";
            generarBase($conn, $nit, $tipo_retencion, $year_tribute,$idOrganizacion);                
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