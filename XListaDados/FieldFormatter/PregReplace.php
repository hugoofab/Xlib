<?php

class Xlib_XListaDados_FieldFormatter_PregReplace extends Xlib_XListaDados_FieldFormatterAbstract {

	private $ereg ;
	private $replace ;

	public function __construct ( $ereg , $replace ) {
		$this->ereg    = $ereg ;
		$this->replace = $replace ;
	}

    public function format ( $dataIn ) {
    	return preg_replace ( $this->ereg , $this->replace , $dataIn ) ;
    }


}