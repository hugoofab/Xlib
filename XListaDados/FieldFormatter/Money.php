<?php

class Xlib_XListaDados_FieldFormatter_Money extends Xlib_XListaDados_FieldFormatterAbstract {
    
    private $symbol = "R$ ";
    
    public function getInstance ( ) {
        return new Xlib_XListaDados_FieldFormatter_Money ;
    }
        
    
    public function format ( $dataIn ) {
        
        $color = ( $dataIn < 0 ) ? "color:#F00;" : "" ;
        $numeroFormatado = number_format ( $dataIn , 2 , ',' , '.' ) ;
        $output = "";
        if ( $this->symbol !== false ) {
            $output = "<span style=\"float:left;\">$this->symbol</span> <span style=\"float:right;$color\">" . $numeroFormatado . "</span>";
        } else {
            $output = $numeroFormatado ;
        }
        return $output;
    }
    
    public function setSymbol ( $symbol ) {
        $this->symbol = $symbol;
        return $this;
    }
    
}