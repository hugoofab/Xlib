<?php

class Xlib_XListaDados_FieldFormatter_Center extends Xlib_XListaDados_FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
        return "<center>" . $dataIn . "</center>";
    }
    
    
}