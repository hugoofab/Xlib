<?php

class Xlib_XListaDados_TitleFormatter_Style extends Xlib_XListaDados_TitleFormatterAbstract {
    
    protected $style ;
        
    public function __construct ( $style ) {
        $this->style = $style;
    }
    
    public function format ( $dataIn ) {
        return "<div style=\"$this->style\" >" . $dataIn . "</div>";
    }
    
    
}