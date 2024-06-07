<?php 

/**
 * @author Haymer Barbeti
 * 
 * 
 */
class Usuario {

	private $id;
	private $first_name;
	private $last_name;
	private $email;
	private $password;

	private $tipo_id;
	private $nombre;
	private $apellido;
	private $telefono;	
	private $genero;
	

	private $table = 'inui.paciente';
	private $conection;

	public function __construct() {
		$this->id='';
		$this->first_name='';
		$this->last_name='';
		$this->email='';
		$this->password='';
	}

	public function constructOverload($id , $tipo_id, $nombre, $apellido, $telefono, $email, $genero) {
		$this->id=$id;	
		$this->tipo_id=$tipo_id;	
		$this->nombre=$nombre;	
		$this->apellido=$id;	
		$this->telefono=$telefono;	
		$this->email=$email;	
		$this->genero=$genero;	

		$this->id='';
		$this->first_name='';
		$this->last_name='';
		$this->email='';
		$this->password='';
	}


	
	/** 
	 * Estaablece la configuracion
	 * 
	 */
	public function getConection(){
		//$dbObj = new Db();
		//$this->conection = $dbObj->conection;
	}

	/**
	 * recupera todos los pacientes
	 */
	public function getPacientes(){
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table;
		$stmt = $this->conection->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/* 
	 * recupera pacientes por id
	 */
	public function getPacienteById($id){
		if(is_null($id)) return false;
		$this->getConection();
		$sql = "SELECT * FROM ".$this->table. " WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		$stmt->execute([$id]);
		return $stmt->fetch();
	}

	/** 
	 * Crea el paciente 
	 */
	public function save($param){
		$this->getConection();
		/**
		 * Seteamos valores por defecto
		 */
		$title = $content = "";
		/**
		 * Se verifica si exite
		 */
		$exists = false;
		if(isset($param["id"]) and $param["id"] !=''){
			$actualPaciente = $this->getPacienteById($param["id"]);
			if(isset($actualPaciente["id"])){
				$exists = true;	
				/**
				 * Valores actuales
				 */
				$id = $param["id"];
				$tipo_id = $param["tipo_id"];
				$nombre = $param["nombre"];
				$apellido = $param["apellido"];
				$telefono = $param["telefono"];
				$email = $param["email"];
				$genero = $param["genero"];

				
			}
		}
		/**
		 * Recepción de valores
		 */
		if(isset($param["id"])) $id = $param["id"];
		if(isset($param["tipo_id"])) $tipo_id = $param["tipo_id"];
		if(isset($param["nombre"])) $nombre = $param["nombre"];
		if(isset($param["apellido"])) $apellido = $param["apellido"];
		if(isset($param["telefono"])) $telefono = $param["telefono"];
		if(isset($param["email"])) $email = $param["email"];
		if(isset($param["genero"])) $genero = $param["genero"];
		/**
		 * Operaciones de bases de datos
		 */
		if($exists){
			$sql = "UPDATE ".$this->table. " SET id=?,tipo_id=?, nombre=?, apellido=?, telefono=?, email=?, genero=?  WHERE id=?";
			$stmt = $this->conection->prepare($sql);
			$res = $stmt->execute([$id,$tipo_id,$nombre,$apellido,$telefono,$email,$genero, $id]);
		}else{
			$sql = "INSERT INTO ".$this->table. " (id,tipo_id, nombre, apellido, telefono, email, genero) values(?,?,?,?,?,?,?)";
			$stmt = $this->conection->prepare($sql);
			$stmt->execute([$id,$tipo_id,$nombre,$apellido,$telefono,$email,$genero]);
			$id = $this->conection->lastInsertId();
		}	

		return $id;	

	}
	/**
	 * Eliminar por id
	 */
	public function deletePacienteById($id){
		$this->getConection();
		$sql = "DELETE FROM ".$this->table. " WHERE id = ?";
		$stmt = $this->conection->prepare($sql);
		return $stmt->execute([$id]);
	}


	public function set_id($id){
		$this->id = $id;
	 }

	public function get_id(){
		return $this->get_id();
	 }



	 public function set_tipo_id($tipo_id){
		$this->tipo_id = $tipo_id;
	 }

	public function get_tipo_id(){
		return $this->get_tipo_id();
	 }

	 public function set_nombre($nombre){
		$this->nombre = $nombre;
	 }

	public function get_nombre(){
		return $this->get_nombre();
	 }



	 public function set_apellido($apellido){
		$this->apellido = $apellido;
	 }

	public function get_apellido(){
		return $this->get_apellido();
	 }


	 public function set_telefono($telefono){
		$this->telefono = $telefono;
	 }

	public function get_telefono(){
		return $this->get_telefono();
	 }

	 public function set_email($email){
		$this->email = $email;
	 }

	public function get_email(){
		return $this->get_email();
	 }

	 public function set_genero($genero){
		$this->genero = $genero;
	 }

	public function get_genero(){
		return $this->get_genero();
	 }

}

?>