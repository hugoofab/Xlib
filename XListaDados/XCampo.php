<?php

class XCampo{	
	
	private $tipoSaida;
	private $nome;
	private $label;
    private $dbField ;
	private $nowrap;
	private $align;
	private $checkbox;
    private $allowOrder = true ;
    private $primaryKey = false ;
	
	public function __construct($nome, $label, $tipoSaida = 'T', $nowrap = true,$align = "center",$order= '',$format){
		
		// 'T' ou 't' para Tela
		// 'A' ou 'a' para Ambos
		// 'P' ou 'p' para Parametro somente
		// 'CK'  para Parametro somente
		 
		$this->tipoSaida = strtoupper($tipoSaida);		
        if ( $this->tipoSaida === "A" ) $this->primaryKey = true ;
		$this->nome=$nome;
		$this->label=$label;
		$this->nowrap=$nowrap;
		$this->align=$align;
		$this->order=$order;	
		$this->format=$format;
		
	}
    
    public function getDbField () {
        return $this->dbField ;
    }

    public function setDbField ( $dbField ) {
        $this->dbField = $dbField ;
    }
	
    public function isPrimaryKey ( ) {
        return $this->primaryKey ;
    }
	
	public function getNome(){
		return $this->nome;
	}
	
	public function getLabel(){
		return $this->label;
	}
	
	public function getNowrap(){
		return $this->nowrap;
	}
	
	public function getAlign(){
		return $this->align;
	}
	
	public function getTipoSaida(){
		return $this->tipoSaida;
	}
	
	public function allowOrder ( $order = null ) {
        if ( $order !== null ) $this->allowOrder = $order ;
        return $this->allowOrder ;
    }
	
	public function setOrder($order){
		$this->order = $order;
	}
	
	public function getOrder(){
		return $this->order;
	}
	
	public function setFormat($format){
		$this->format = $format;
	}
	
	public function getFormat(){
		return $this->format;
	}
		
}// fim classe
