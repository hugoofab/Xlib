<?php

class Xlib_XListaDados_FieldFormatter_FloatRight extends Xlib_XListaDados_FieldFormatterAbstract {
    
    public function format ( $dataIn ) {
        return "<div style=\"float:right;\">" . $dataIn . "</div>";
    }
    
    
}