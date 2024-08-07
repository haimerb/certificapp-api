<?php
/**
 * @author Haymer Barbetti <hbarbetti.ing@icloud.com>
 */
include_once './config/database.php';
require "../vendor/autoload.php";
$configs=include('./config/config.php');

use \Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

header("Access-Control-Allow-Origin: http://localhost:4200");
//header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$pathInfo = $_SERVER['PATH_INFO'];
$method=$_SERVER['REQUEST_METHOD'];

$opciones = array(
    'http'=>array(
      'method'=>"GET",
      'header'=>"Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n"
    )
  );

$contexto = stream_context_create($opciones);

$email = '';
$password = '';

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();
$data = json_decode(file_get_contents("php://input",false,$contexto));
$id_organization=isset($data->idOrganization)?$data->idOrganization:(isset($_REQUEST['idOrganization'])?$_REQUEST['idOrganization']:"");

function allCertificates($id_organization,$conn){
    
    $querySelect='select  
                    cg.id_certificados_generado,
                    cg.tipo_certificado,
                    id_certificados_generado,
                    cg.nombre, 
                    cg.organization_asociate, 
                    o.name,
                    o.nit,
                    o.dv,
                    cg.url_assoc_file
    from certificados_generados cg
    inner join organizations o ON o.id_organization =cg.organization_asociate 
    where cg.organization_asociate =:organization_asociate 
    and cg.createat is not null 
    and cg.url_assoc_file !=""
    order by cg.id_certificados_generado DESC  limit 5';

    $stmt = $conn->prepare( $querySelect );
    $stmt->bindParam(':organization_asociate',$id_organization);
    $stmt->execute();
    $num = $stmt->rowCount();
    

    if($num > 0){
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                //print_r(json_encode($row) );
                // $id = $row['id'];
                // $firstname = $row['first_name'];
                // $lastname = $row['last_name'];
                // $i = 0;
                //$array_certificates=array();
                
                // for ($i=0; $i<$num; $i++) {
                //     // $valor = $valor * 2;
                //     //array_push($array_certificates,array($row[$i]=>$valor) );
                //     print_r($row[$i]);
                    
                // }
                //$names_certificate=$row;

                    //http_response_code(200);
                    // $headers = [
                    //     'x-forwarded-for' => 'www.google.com'
                    // ];
            
                    echo json_encode($row
                        // array(
                        //     // "nombres" => $names_certificate,
                        //     // "firstname" => $firstname,
                        //     // "lastname" => $lastname                   
                        // )
                    );        
            }else{    
                //http_response_code(401);
                echo json_encode(array("message" => "QueSQL failed."));
            }

}

function  getAllTypesCertificates($conn){
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST GET");
    header("Access-Control-Max-Age: 0");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $logger = new Logger('files-logger');
    $logger->pushHandler(new StreamHandler('./tmp/logs/log.log', Logger::DEBUG));
    
    $querySelect='select tc.id_type_certificate,tc.name_type ,tc.description, tc.indicator  
                    from type_certificates tc; ';

    $stmt = $conn->prepare( $querySelect );
    $stmt->execute();
    $num = $stmt->rowCount();

    $logger->info("Select Query: ".$querySelect);

    $logger->info("Result data: ".$num);

    
    if($num > 0){
        $rowOut = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->info("Result data: ".json_encode($rowOut));
        echo json_encode($rowOut,JSON_INVALID_UTF8_IGNORE);
    }else{
        echo json_encode(array("message" => "QueSQL failed.", "code"=>"401"));
    }
   
}

if($pathInfo==='/user'){
    usersById($id_user,$querySelect,$conn);
}

// if($pathInfo==='/certifications'){
//     allCertificates($id_organization,$querySelect,$conn);
// }


/**
 * Get petitions
 */
if($method==='GET'){
    if($pathInfo=== '/allTypesCertificates'){
        //print_r($method);
        getAllTypesCertificates($conn);
    }    
    if($pathInfo==='/certifications'){
        allCertificates($id_organization,$conn);
    }
}else{
    //http_response_code(401);
    echo json_encode(array("message" => "Mehtod not allowed","code" => "401"));
}

?>