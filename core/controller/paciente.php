<?php 
require_once 'core/model/paciente.php';

/**
 * @author Haymer Barbeti
 * 
 * 
 */
class pacienteController{

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

    public function __construct() {
		$this->view = 'list_paciente';
		$this->page_title = '';
        //$this->pacienteObj = new Paciente();
	}

	/** 
	 * Lista todos los pacientes
	 */
	public function list(){
		$this->page_title = 'Listado de pacientes';
		return $this->pacienteObj->getPacientes();
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



}

?>