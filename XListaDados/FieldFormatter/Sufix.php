<?php

class Xlib_XListaDados_FieldFormatter_Sufix extends Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $sufix = "";
    
    public function __construct ( $sufix ) {
        $this->sufix = $sufix;
    }
    
    public function format ( $dataIn ) {
        return $this->sufix . $dataIn ;
    }
    
}