<?php
/**
 * @author Haymer Barbetti <hbarbetti.ing@icloud.com>
 */
include_once './config/database.php';
require "../vendor/autoload.php";
$configs = include ('./config/config.php');

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$pathInfo = $_SERVER['PATH_INFO'];
$method = $_SERVER['REQUEST_METHOD'];

$opciones = array(
    'http' => array(
        'method' => "GET",
        'header' => "Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n"
    )
);

$contexto = stream_context_create($opciones);
$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input", false, $contexto));
$id_organization = isset($data->idOrganization) ? $data->idOrganization : "";
$nit = isset($data->nit) ? $data->nit : $_REQUEST['nit'];

$table_name = 'certificados_generados';
$table_name_type_certificates = 'type_certificates';
$table_name_organization = 'organizations';

$querySelect = 'select * from ' . $table_name . ' cg
inner join organizations o ON o.id_organization =cg.organization_asociate 
where o.id_organization =? ';
/**
 *  @abstract request all generate certificates for organization to databases
 *  @param  integer $id_organization
 *  @param  string  $querySelect
 *  @param  mixed $conn
 */
function allCertificates($id_organization, $querySelect, $conn)
{
    $stmt = $conn->prepare($querySelect);
    $stmt->bindParam(1, $id_organization);
    $stmt->execute();
    $num = $stmt->rowCount();


    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r(json_encode($row));
        // $id = $row['id'];
        // $firstname = $row['first_name'];
        // $lastname = $row['last_name'];
        // $i = 0;
        $array_certificates = array();

        for ($i = 0; $i < $num; $i++) {
            // $valor = $valor * 2;
            //array_push($array_certificates,array($row[$i]=>$valor) );
            print_r($row[$i]);

        }
        //$names_certificate=$row;

        http_response_code(200);
        // $headers = [
        //     'x-forwarded-for' => 'www.google.com'
        // ];

        echo json_encode(
            $array_certificates
            // array(
            //     // "nombres" => $names_certificate,
            //     // "firstname" => $firstname,
            //     // "lastname" => $lastname                   
            // )
        );
    } else {
        //http_response_code(401);
        echo json_encode(array("message" => "QueSQL failed."));
    }

}

function getAllOrganizations($table_name_organization, $nit, $conn)
{
    //print_r($nit);
    $querySelectOrg = 'select o.id_organization ,o.name ,o.nit ,o.dv  from organizations o ';
    //print_r($querySelectOrg);
    $stmt = $conn->prepare($querySelectOrg);
    //$stmt->bindParam(1, $nit);
    $stmt->execute();
    $num = $stmt->rowCount();
    //print_r($num);

    if ($num > 0) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //http_response_code(200);
        echo json_encode($row,JSON_INVALID_UTF8_IGNORE);
    } else {
        echo json_encode(array("message" => "QueSQL failed."));
    }
}
function getAllTypesCertificates($table_name_type_certificates, $conn)
{
    $querySelect = 'select tc.id_type_certificate,tc.name_type ,tc.description  
                    from ' . $table_name_type_certificates . ' tc';
    $stmt = $conn->prepare($querySelect);
    //$stmt->bindParam(1, $id_user);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array($row));
    }


}

if ($pathInfo === '/user') {
    usersById($id_user, $querySelect, $conn);
}

if ($pathInfo === '/certifications') {
    allCertificates($id_organization, $querySelect, $conn);
}


/**
 * Get petitions
 */
if ($method === 'GET') {

    //$whitout = rtrim(explode("&", $pathInfo)[0]);
    // print_r($whitout[0]);
    // print_r($pathInfo);
    if ($pathInfo === '/allTypesCertificates') {
        //print_r($method);
        getAllTypesCertificates($table_name_type_certificates, $conn);
    }
    if ($pathInfo === '/allOrganizations') {
        getAllOrganizations($table_name_organization, $nit, $conn);
    }
} else {
    //http_response_code(401);
    echo json_encode(array("message" => "Mehtod not allowed", "code" => "401"));
}
?>