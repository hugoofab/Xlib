<?php

class Xlib_XListaDados_FieldFormatter_Bold extends Xlib_XListaDados_FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
        return "<strong>" . $dataIn . "</strong>";
    }
    
    
}