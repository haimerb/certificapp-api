<?php 
require_once 'model/organization.php';

/**
 * @author Haymer Barbeti
 *  
 */
class organizationController{

	private $id;
	private $tp_rete;
	private $nit;
	private $dv;
	private $razon_social;
	private $email;
	private $genero;
	
    public $page_title;
	public $view;
    public $organizationObj;

    public function __construct() {
		///$this->view = 'list_organization';
		//$this->page_title = '';
        $this->organizationObj = new organization();
	}

	/** 
	 * Lista todos los organizations
	 */
	public function list(){
		$this->page_title = 'Listado de organizations';
		return $this->organizationObj->getorganizations();
	}

	/**
	 * Cargar para edicion
	 */
	public function edit($id = null){
		$this->page_title = 'Editar organization';
		$this->view = 'edit_organization';
		
		if(isset($_GET["id"])) $id = $_GET["id"];
		return $this->organizationObj->getorganizationById($id);
	}

	/** 
	 * Crear o actualizar organizations 
	 **/
	public function save(){
		$this->view = 'edit_organization';
		$this->page_title = 'Editar organization';
		$id = $this->organizationObj->save($_POST);
		$result = $this->organizationObj->getorganizationById($id);
		$_GET["response"] = true;
		return $result;
	}

	/**
	 * Confirmar eliminar
	 */
	public function confirmDelete(){
		$this->page_title = 'Eliminar organization';
		$this->view = 'confirm_delete_organization';
		return $this->organizationObj->getorganizationById($_GET["id"]);
	}

	/**
	 * Eliminar
	 */
	public function delete(){
		$this->page_title = 'Listado de organizations';
		$this->view = 'delete_organization';
		return $this->organizationObj->deleteorganizationById($_POST["id"]);
	}



}

?>