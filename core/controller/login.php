<?php 
//require_once 'core/model/paciente.php';
require_once 'core/functions/auth/auth.php';

/**
 * @author Haymer Barbeti
 * 
 */
class loginController{

	private $id;
	private $tipo_id;
	private $nombre;
	private $apellido;
	private $telefono;
	private $email;
	private $genero;
	
    public $page_title;
	public $view;
    public $pacienteObj;

	private $emailAuth;
	private $passwordAuth;

    public function __construct() {
		$this->view = 'list_paciente';
		$this->page_title = '';
		$this->emailAuth='';
		$this->passwordAuth='';
        //$this->pacienteObj = new Paciente();
	}

	/** 
	 * Lista todos los pacientes
	 */
	public function list(){
		$this->page_title = 'Listado de pacientes';
		return $this->pacienteObj->getPacientes();
	}

	public function toPrincipal(){
		$this->page_title = 'To principal';
		$this->view = 'principal';
	}

	public function doLogin($id = null){
		
		$password='';
		$this->page_title = 'Login';
		//$this->view = 'login';



		//login();
		//return $this->pacienteObj->getPacienteById($id);

		if(!validateLogin()){
			header("Location: index.php?controller=login&action=toPrincipal");
		}else{
			$configs=include('api/config/config.php');
			$email = '';
			$password = '';

			$databaseService = new DatabaseService();
			$databaseService->setConfig($configs);
			$conn = $databaseService->getConnection();

			$data = json_decode(file_get_contents("php://input"));
			print_r($data);
			$email = $data->email;
			$password = $data->password;

			$table_name = 'Users';

			$query = "SELECT id, first_name, last_name, password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";

			$stmt = $conn->prepare( $query );
			$stmt->bindParam(1, $email);
			$stmt->execute();
			$num = $stmt->rowCount();

				if($num > 0){
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$id = $row['id'];
					$firstname = $row['first_name'];
					$lastname = $row['last_name'];
					$password2 = $row['password'];
				
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
								"email" => $email
						));
				
						http_response_code(200);
				
						$headers = [
							'x-forwarded-for' => 'www.google.com'
						];
				
						$jwt = JWT::encode($token, $secret_key,'HS256',null,$headers);
						$json_out= json_encode(
							array(
								"message" => "Successful login.",
								"token" => $jwt,
								"email" => $email,
								"expireAt" => $expire_claim
							));
							session_start();
							$_SESSION["user"] =$json_out;
					}
					else{
				
						http_response_code(401);
						echo json_encode(array("message" => "Login failed.", "password" => $password));
					}
			}

		}

	}




	/**
	 * Cargar para edicion
	 */
	public function edit($id = null){
		$this->page_title = 'Editar paciente';
		$this->view = 'edit_paciente';
		
		if(isset($_GET["id"])) $id = $_GET["id"];
		return $this->pacienteObj->getPacienteById($id);
	}

	/** 
	 * Crear o actualizar pacientes 
	 **/
	public function save(){
		$this->view = 'edit_paciente';
		$this->page_title = 'Editar paciente';
		$id = $this->pacienteObj->save($_POST);
		$result = $this->pacienteObj->getPacienteById($id);
		$_GET["response"] = true;
		return $result;
	}

	/**
	 * Confirmar eliminar
	 */
	public function confirmDelete(){
		$this->page_title = 'Eliminar paciente';
		$this->view = 'confirm_delete_paciente';
		return $this->pacienteObj->getPacienteById($_GET["id"]);
	}

	/**
	 * Eliminar
	 */
	public function delete(){
		$this->page_title = 'Listado de pacientes';
		$this->view = 'delete_paciente';
		return $this->pacienteObj->deletePacienteById($_POST["id"]);
	}


	function validateLogin(){
		session_start();
		if (empty($_SESSION["user"])) {
			return true;
		}
		return false;
	}
}

?>
