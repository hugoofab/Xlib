<?php

class Xlib_XListaDados_FieldFormatter_Prefix extends Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $prefix = "";
    
    public function __construct ( $prefix ) {
        $this->prefix = $prefix;
    }
    
    public function format ( $dataIn ) {
        return $this->prefix . $dataIn ;
    }
    
}