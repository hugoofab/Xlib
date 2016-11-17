<?php

class Xlib_XListaDados_FieldFilter_Hidden extends Xlib_XListaDados_FieldFilterAbstract {
    
    protected $value      = "";
    
    public function __construct ( $field , $value ) {
        $this->setField ( $field );
        $this->setAttribute ( 'value' , $value ) ;
        $this->setAttribute ( 'type' , "hidden" ) ;
    }

        
    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {
        return $this->sufix . "<input " . $this->getAttributeSetAsString ( $this->getAttributeSet ( ) ) . " >" . $this->pre ;
    }
    
}