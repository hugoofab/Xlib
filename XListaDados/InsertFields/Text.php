<?php

class Xlib_XListaDados_InsertFields_Text extends Xlib_XListaDados_InsertFieldsAbstract {
    
    protected $placeholder      = "";

    public function __construct ( $field , $label = "" , $placeholder = "" ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
        $this->setAttribute ( 'placeholder' , $placeholder ) ;
    }

}