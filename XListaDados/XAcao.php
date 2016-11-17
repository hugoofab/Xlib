<?php

class XAcao{
	
	private $label; // Imagem que aparece para o usuario
	private $alt; // Imagem que aparece qd o usuario coloca o nome sobre a imagem
	private $href; // Ipagian que abrira ao clicar no link ou I
	private $imagem;
	private $parametros;
	private $display;
	private $outros;
	
	public function __construct($imagem,$href = '',$alt = '',$label = '',$outros = '',$parametros="",$display=true){
		
		$this->href = $href;
		$this->alt = $alt;
		$this->imagem = $imagem;
		$this->label = $label;
		$this->outros = $outros;
		$this->display = $display;
		$this->parametros = $parametros;
		
	}
	
	public function setLabel($label){
		$this->label = $label;
	}
	
	public function getLabel(){
		return $this->label;
	}
	
	public function getDisplay(){
		
		return $this->display;
	}
	
	
	public function setParametros($par){
		$this->parametros = $par;
	}
	
	public function getParametros(){
		return $this->parametros;
	}
	
	public function setAlt($alt){
		$this->alt = $alt;
	}
	
	public function getAlt(){
		return $this->alt;
	}
	
	public function setHref($href){
		$this->href = $href;
	}
	
	public function getHref(){
		return $this->href;
	}
	public function setImagem($imagem){
		$this->imagem = $imagem;
	}
	
	public function getImagem(){
		return $this->imagem;
	}
	
	public function getOutros(){
		$outros=str_replace('#PARAMETROS#',$this->getParametros(),$this->outros);
		
		return $outros;
	}
	
	public function getParamentoOutros(){
		$outros=addcslashes($this->outros,"\'");  //addslashes($this->outros);
		
		return $outros;
	}
	
	public function setOutros($outros){
		$this->outros = $outros;
	}

	
}
