<?php 
/**
 * @author Haymer Barbetti
 * 
 * 
 */
class Certificate {

	private $id_certificados_generado;
	private $tipo_certificado;
	private $nombre;
	private $organization_asociate;
	private $ret_razon_social;
	private $ret_nit;
	private $ret_concepto_retencion;
	private $anio_gravable;
	private $createat;
	private $id_certificate_value;
	private $id_values;
	private $concepto;
	private $base_retencion;
	private $valor_retenido;

	private $table = 'inui.paciente';
	private $conection;

	public function __construct() {
		$this->id_certificados_generado='';
		$this->tipo_certificado='';
		$this->nombre='';
		$this->organization_asociate='';
		$this->ret_razon_social='';
		$this->ret_nit='';
		$this->ret_concepto_retencion='';
		$this->anio_gravable='';
		$this->id_certificate_value='';
		$this->id_values='';
		$this->concepto='';
		$this->base_retencion='';
		$this->valor_retenido='';
	}

	// public function constructOverload($id , $tipo_id, $nombre, $apellido, $telefono, $email, $genero) {
	// 	$this->id=$id;	
	// 	$this->tipo_id=$tipo_id;	
	// 	$this->nombre=$nombre;	
	// 	$this->apellido=$id;	
	// 	$this->telefono=$telefono;	
	// 	$this->email=$email;	
	// 	$this->genero=$genero;	

	// 	$this->id='';
	// 	$this->first_name='';
	// 	$this->last_name='';
	// 	$this->email='';
	// 	$this->password='';
	// }

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

	 public function set_nombre($nombre){
		$this->nombre = $nombre;
	 }

	public function get_nombre(){
		return $this->get_nombre();
	 }

	public function get_genero(){
		return $this->get_genero();
	 }

	/**
	 * Get the value of id_certificados_generado
	 */ 
	public function getId_certificados_generado()
	{
		return $this->id_certificados_generado;
	}

	/**
	 * Set the value of id_certificados_generado
	 *
	 * @return  self
	 */ 
	public function setId_certificados_generado($id_certificados_generado)
	{
		return $this->id_certificados_generado = $id_certificados_generado;
	}

	/**
	 * Get the value of tipo_certificado
	 */ 
	public function getTipo_certificado()
	{
		return $this->tipo_certificado;
	}

	/**
	 * Set the value of tipo_certificado
	 *
	 * @return  self
	 */ 
	public function setTipo_certificado($tipo_certificado)
	{
		$this->tipo_certificado = $tipo_certificado;

		return $this;
	}

	/**
	 * Get the value of nombre
	 */ 
	public function getNombre()
	{
		return $this->nombre;
	}

	/**
	 * Set the value of nombre
	 *
	 * @return  self
	 */ 
	public function setNombre($nombre)
	{
		$this->nombre = $nombre;

		return $this;
	}

	/**
	 * Get the value of organization_asociate
	 */ 
	public function getOrganization_asociate()
	{
		return $this->organization_asociate;
	}

	/**
	 * Set the value of organization_asociate
	 *
	 * @return  self
	 */ 
	public function setOrganization_asociate($organization_asociate)
	{
		$this->organization_asociate = $organization_asociate;

		return $this;
	}

	/**
	 * Get the value of ret_razon_social
	 */ 
	public function getRet_razon_social()
	{
		return $this->ret_razon_social;
	}

	/**
	 * Set the value of ret_razon_social
	 *
	 * @return  self
	 */ 
	public function setRet_razon_social($ret_razon_social)
	{
		$this->ret_razon_social = $ret_razon_social;

		return $this;
	}

	/**
	 * Get the value of ret_nit
	 */ 
	public function getRet_nit()
	{
		return $this->ret_nit;
	}

	/**
	 * Set the value of ret_nit
	 *
	 * @return  self
	 */ 
	public function setRet_nit($ret_nit)
	{
		$this->ret_nit = $ret_nit;

		return $this;
	}

	/**
	 * Get the value of ret_concepto_retencion
	 */ 
	public function getRet_concepto_retencion()
	{
		return $this->ret_concepto_retencion;
	}

	/**
	 * Set the value of ret_concepto_retencion
	 *
	 * @return  self
	 */ 
	public function setRet_concepto_retencion($ret_concepto_retencion)
	{
		$this->ret_concepto_retencion = $ret_concepto_retencion;

		return $this;
	}

	/**
	 * Get the value of anio_gravable
	 */ 
	public function getAnio_gravable()
	{
		return $this->anio_gravable;
	}

	/**
	 * Set the value of anio_gravable
	 *
	 * @return  self
	 */ 
	public function setAnio_gravable($anio_gravable)
	{
		$this->anio_gravable = $anio_gravable;

		return $this;
	}

	/**
	 * Get the value of createat
	 */ 
	public function getCreateat()
	{
		return $this->createat;
	}

	/**
	 * Set the value of createat
	 *
	 * @return  self
	 */ 
	public function setCreateat($createat)
	{
		$this->createat = $createat;

		return $this;
	}

	/**
	 * Get the value of id_certificate_value
	 */ 
	public function getId_certificate_value()
	{
		return $this->id_certificate_value;
	}

	/**
	 * Set the value of id_certificate_value
	 *
	 * @return  self
	 */ 
	public function setId_certificate_value($id_certificate_value)
	{
		$this->id_certificate_value = $id_certificate_value;

		return $this;
	}

	/**
	 * Get the value of id_values
	 */ 
	public function getId_values()
	{
		return $this->id_values;
	}

	/**
	 * Set the value of id_values
	 *
	 * @return  self
	 */ 
	public function setId_values($id_values)
	{
		$this->id_values = $id_values;

		return $this;
	}

	/**
	 * Get the value of concepto
	 */ 
	public function getConcepto()
	{
		return $this->concepto;
	}

	/**
	 * Set the value of concepto
	 *
	 * @return  self
	 */ 
	public function setConcepto($concepto)
	{
		$this->concepto = $concepto;

		return $this;
	}

	/**
	 * Get the value of base_retencion
	 */ 
	public function getBase_retencion()
	{
		return $this->base_retencion;
	}

	/**
	 * Set the value of base_retencion
	 *
	 * @return  self
	 */ 
	public function setBase_retencion($base_retencion)
	{
		$this->base_retencion = $base_retencion;

		return $this;
	}

	/**
	 * Get the value of valor_retenido
	 */ 
	public function getValor_retenido()
	{
		return $this->valor_retenido;
	}

	/**
	 * Set the value of valor_retenido
	 *
	 * @return  self
	 */ 
	public function setValor_retenido($valor_retenido)
	{
		$this->valor_retenido = $valor_retenido;

		return $this;
	}
}

?>