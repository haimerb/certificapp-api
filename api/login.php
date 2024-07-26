<?php
$configs=include('./config/config.php');
include_once './config/database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$email = '';
$password = '';

$databaseService = new DatabaseService();
$databaseService->setConfig($configs);
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$table_name = 'users';

$query ='SELECT u.id_user , u.first_name , u.last_name, u.password,
         u.email,o.id_organization ,o.name,o.nit,o.dv  
FROM '. $table_name . ' u  
inner join users_organizations uo ON  uo.id_user =u.id_user 
inner join organizations o  ON o.id_organization=uo.id_organization 
WHERE u.email  = ? LIMIT 1';



$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['id_user'];
    $firstname = $row['first_name'];
    $lastname = $row['last_name'];
    $password2 = $row['password'];
    $email=$row['email'];
    $id_organization=$row['id_organization'];
    $organizationName=$row['name'];
    $names=$row['first_name'];
    $lastnames=$row['last_name'];
    $nit=$row['nit'];

    //echo $id;
    $queryRols='select ru.id_rol_user ,ru.id_user , ru.id_rol, r.name_rol 
                from rol_users ru
                inner join rol r on r.id_rol = ru.id_rol
                inner join users u on u.id_user = ru.id_user
                where u.id_user =?';

    $stmt = $conn->prepare( $queryRols );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $numRols = $stmt->rowCount();
    $rowRols=null;
    //echo $numRols;
    if( $numRols > 0){
        $rowRols = $stmt->fetchAll(PDO::FETCH_ASSOC);        
    }

    if(password_verify($password, $password2))
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "THE_ISSUER"; 
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); 
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 60; 
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email,
                "id_organization"=>$id_organization
        ));

        http_response_code(200);

        $headers = [
            'x-forwarded-for' => 'www.google.com'
        ];

        $jwt = JWT::encode($token, $secret_key,'HS256',null,$headers);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "token" => $jwt,
                "email" => $email,
                "expireAt" => $expire_claim,
                "id_organization"=>$id_organization,
                "ornganization_name"=>$organizationName,
                "names"=>$names,
                "lastname" => $lastnames,
                "nit"=>$nit,
                "idUser"=>$id,
                "code"=>200,
                "rols"=>$rowRols
            ));
    }
    else{
        echo json_encode(array("message" => "Login failed.", "password" => $password,
                "code" => "401"));        
    }
}

?>