<?php

class Xlib_XListaDados_TitleFormatter_Tooltip extends Xlib_XListaDados_TitleFormatterAbstract {
    
    protected $tooltip ;
        
    /**
     * @TODO! Esta classe deveria colocar o tooltip no titulo da tabela e não em cada campo
     * @param type $tooltip
     */
    public function __construct ( $tooltip ) {
        $this->tooltip = $tooltip;
    }
    
    public function format ( $dataIn ) {
        return "<span style=\"cursor:help;\" title=\"$this->tooltip\">" . $dataIn . "</span>";
    }
    
    
}