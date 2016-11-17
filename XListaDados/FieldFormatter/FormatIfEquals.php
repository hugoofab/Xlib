<?php

class Xlib_XListaDados_FieldFormatter_FormatIfEquals extends Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $valToCompare = '';
    protected $formatter = null ;
    
    public function __construct ( $valToCompare , $formatter ) {
        $this->valToCompare = $valToCompare ;
        $this->formatter    = $formatter ;
    }
    
    public function format ( $dataIn ) {
        if ( $dataIn != $this->valToCompare ) return $dataIn ;
        
        return $this->formatter->format ( $dataIn );
    }
    
    
}