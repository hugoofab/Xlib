<?php

class Xlib_XListaDados_FieldFormatter_Translate extends Xlib_XListaDados_FieldFormatterAbstract {

	protected $translateArray = array ( ) ;

    public function __construct ( $translateArray ) {
        $this->translateArray = $translateArray ;
    }

    public function format ( $dataIn ) {
    	if ( isset ( $this->translateArray[$dataIn] ) ) return $this->translateArray[$dataIn];
        return  $dataIn ;
    }

}