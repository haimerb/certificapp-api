<?php
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
//  header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
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

$table_name = 'Users';

//$query = "SELECT id_user, first_name, last_name, password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";


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
    $nit=$row['nit'];

    if(password_verify($password, $password2))
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "THE_ISSUER"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60; // expire time in seconds
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
                "nit"=>$nit
            ));
    }
    else{
        echo json_encode(array("message" => "Login failed.", "password" => $password,
                "code" => "401"));
        //http_response_code(401);
    }
}

?>