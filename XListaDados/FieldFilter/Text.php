<?php

class Xlib_XListaDados_FieldFilter_Text extends Xlib_XListaDados_FieldFilterAbstract {

    protected $placeholder      = "";

    public static function getInstance ( $field , $label = "" , $placeholder = "" ) {
    	return new Xlib_XListaDados_FieldFilter_Text ( $field , $label , $placeholder );
    }

    public function __construct ( $field , $label = "" , $placeholder = "" ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
        $this->setAttribute ( 'placeholder' , $placeholder ) ;
    }

}