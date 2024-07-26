<?php

error_reporting(E_ALL);

$configs=include('./config/config.php');
include_once './config/database.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

// header("Access-Control-Allow-Origin: http://localhost:8080");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: GET");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$pathInfo = $_SERVER['PATH_INFO'];

$opciones = array(
    'http'=>array(
      'method'=>"GET",
      'header'=>"Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n"
    )
  );

$contexto = stream_context_create($opciones);

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input",false,$contexto));

$table_name = 'Users';

$querySelect = "SELECT id, first_name, last_name FROM " . $table_name . " WHERE id = ? LIMIT 1";

function updateUser($conn,$idUser,$namesUser,$lastNamesuser,$passwordUser){
  $errors=array();
  if($idUser==""||$idUser==null){
    array_push($errors,array(
        "message"=>"Error: Field idUser is required"        
    )
);
  }  
  if($namesUser==""||$namesUser==null){
    array_push($errors,array(
        "message"=>"Error: Field namesUser is required"        
        )
    );
  }
  if($lastNamesuser==""||$lastNamesuser==null){
    array_push($errors,array(
        "message"=>"Error: Field lastNamesuser is required"        
    )
    );
  }
  if($passwordUser==""||$passwordUser==null){
    array_push($errors,array(        
        "message"=>"Error: Field passwordUser is required"        
    ));
  }

 if(count($errors)>0){
    header("Status: 401 Not Found");
    http_response_code(401);
    echo json_encode(
            array(
                "status"=>401,
                "message"=>"Errors found in the request",
                "error"=>$errors)
            );
 }else{
    $queryUpdate='update users set first_name =:namesUser, 
                    last_name=:lastNamesuser,
                    password=:passwordUser
                    where id_user =:idUser ';

    $stmt = $conn->prepare( $queryUpdate );

    $hash=password_hash($passwordUser, PASSWORD_BCRYPT);

    $stmt->bindParam(':idUser', $idUser);
    $stmt->bindParam(':namesUser', $namesUser);
    $stmt->bindParam(':lastNamesuser', $lastNamesuser);
    $stmt->bindParam(':passwordUser', $hash);  
    
    $insert=$stmt->execute();
    if($insert){
        http_response_code(200);
        echo json_encode(
            array("status"=>200, 
                  "message" => "User was successfully updated. "),JSON_INVALID_UTF8_IGNORE);
      }
 }

}
function changePassword($conn,$passwordUser){
    $errors=array();
    if($passwordUser==""||$passwordUser==null){
      array_push($errors,array(        
          "message"=>"Error: Field passwordUser is required"        
      ));
    }
  
   if(count($errors)>0){
      header("Status: 401 Not Found");
      http_response_code(401);
      echo json_encode(
              array(
                  "status"=>401,
                  "message"=>"Errors found in the request",
                  "error"=>$errors)
              );
   }else{
      $queryUpdate='update users set password=:passwordUser
                      where id_user =:idUser ';
  
      $stmt = $conn->prepare( $queryUpdate );
  
      $hash=password_hash($passwordUser, PASSWORD_BCRYPT);
      $stmt->bindParam(':passwordUser', $hash);  
      
      $insert=$stmt->execute();
      if($insert){
          http_response_code(200);
          echo json_encode(
              array("status"=>200, 
                    "message" => "User was successfully updated. "),JSON_INVALID_UTF8_IGNORE);
        }
   }
  
  }

function getRolsByIdUser($conn, $idUser)
{
    $errors = array();
    if ($idUser == "" || $idUser == null) {
        array_push(
            $errors,
            array(
                "message" => "Error: Field idUser is required"
            )
        );
    }

    if (count($errors) > 0) {
        header("Status: 401 Not Found");
        http_response_code(401);
        echo json_encode(
            array(
                "status" => 401,
                "message" => "Errors found in the request",
                "error" => $errors
            )
        );
    } else {
        $querySelect='select ru.id_rol_user ,ru.id_user , ru.id_rol, r.name_rol 
                                    from rol_users ru
                                    inner join rol r on r.id_rol = ru.id_rol
                                    inner join users u on u.id_user = ru.id_user
                                    where u.id_user = :idUser';
        $stmt = $conn->prepare($querySelect);
        $stmt->bindParam(':idUser', $idUser);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0){
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode(
                array("status"=>200, 
                      "message" => "User was successfully updated. ",
                      "result"=>$row
                    ),JSON_INVALID_UTF8_IGNORE);
          }
    }
}
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