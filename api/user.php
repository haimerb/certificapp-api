<?php
include_once './config/database.php';
include_once './config/database.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$pathInfo = $_SERVER['PATH_INFO'];

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
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input",false,$contexto));
$id_user=$data->user;
$table_name = 'Users';

$querySelect = "SELECT id, first_name, last_name FROM " . $table_name . " WHERE id = ? LIMIT 1";

/**
 * 
 */
function usersById($id_user,$querySelect,$conn){
    $stmt = $conn->prepare( $querySelect );
    $stmt->bindParam(1, $id_user);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $firstname = $row['first_name'];
        $lastname = $row['last_name'];

            http_response_code(200);
            $headers = [
                'x-forwarded-for' => 'www.google.com'
            ];
    
            echo json_encode(
                array(
                    "id" => $id,
                    "firstname" => $firstname,
                    "lastname" => $lastname                   
                ));        
    }else{    
        http_response_code(401);
        echo json_encode(array("message" => "QueSQL failed."));
    }
}

if($pathInfo==='/user'){
    usersById($id_user,$querySelect,$conn);
}

?>