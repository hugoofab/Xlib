<?php

class Xlib_XListaDados_FieldFormatter_ColorByValue extends Xlib_XListaDados_FieldFormatterAbstract {

	protected $colorByValueArray = array ( ) ;

    public function __construct ( $colorByValueArray ) {
        $this->colorByValueArray = $colorByValueArray ;
    }

    public function format ( $dataIn ) {
    	if ( isset ( $this->colorByValueArray[$dataIn] ) ) return "<span style=\"color:" . $this->colorByValueArray[$dataIn] . ";\">" . $dataIn . "</span>";
        return  $dataIn ;
    }

}