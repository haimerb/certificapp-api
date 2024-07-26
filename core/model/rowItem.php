<?php 
/**
 * @author Haymer Barbetti
 * 
 * 
 */
class RowItem  {


	private $code;
	private $id_certificate_data;
	private $nit;
	private $razonSocial;
	private $nombreConcepto;

	public function __construct() {
		
		$this->		code='';
		$this->id_certificate_data='';
		$this->nit='';
		$this->razonSocial='';
		$this->nombreConcepto='';
	}

	

	/**
	 * Get the value of code
	 */ 
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Set the value of code
	 *
	 * @return  self
	 */ 
	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Get the value of id_certificate_data
	 */ 
	public function getId_certificate_data()
	{
		return $this->id_certificate_data;
	}

	/**
	 * Set the value of id_certificate_data
	 *
	 * @return  self
	 */ 
	public function setId_certificate_data($id_certificate_data)
	{
		$this->id_certificate_data = $id_certificate_data;

		return $this;
	}

	/**
	 * Get the value of nit
	 */ 
	public function getNit()
	{
		return $this->nit;
	}

	/**
	 * Set the value of nit
	 *
	 * @return  self
	 */ 
	public function setNit($nit)
	{
		$this->nit = $nit;

		return $this;
	}

	/**
	 * Get the value of razonSocial
	 */ 
	public function getRazonSocial()
	{
		return $this->razonSocial;
	}

	/**
	 * Set the value of razonSocial
	 *
	 * @return  self
	 */ 
	public function setRazonSocial($razonSocial)
	{
		$this->razonSocial = $razonSocial;

		return $this;
	}

	/**
	 * Get the value of nombreConcepto
	 */ 
	public function getNombreConcepto()
	{
		return $this->nombreConcepto;
	}

	/**
	 * Set the value of nombreConcepto
	 *
	 * @return  self
	 */ 
	public function setNombreConcepto($nombreConcepto)
	{
		$this->nombreConcepto = $nombreConcepto;

		return $this;
	}


}

?>