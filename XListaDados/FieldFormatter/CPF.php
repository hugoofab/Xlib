<?php

class Xlib_XListaDados_FieldFormatter_CPF extends Xlib_XListaDados_FieldFormatterAbstract {

    public function getInstance ( ) {
        return new Xlib_XListaDados_FieldFormatter_CPF ;
    }

    public function format ( $dataIn ) {

    	if ( preg_match ( '/^\d{11}$/' , $dataIn ) ) {
    		return preg_replace ( '/^(\d{3})(\d{3})(\d{3})(\d{2})/' , "$1.$2.$3-$4" , $dataIn );
    	}

        return $dataIn;
    }


}