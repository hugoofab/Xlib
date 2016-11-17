<?php

class Xlib_XListaDados_FieldFilter_TextMultifield extends Xlib_XListaDados_FieldFilterAbstract {
    
    protected $placeholder      = "";
    protected $keyValueFieldList = array ( );
    protected $template         = ' $key = \'$value\' ';

    public function __construct ( array $keyValueFieldList , $label = "" , $placeholder = "" ) {
        $this->setLabel ( $label );
        $this->setAttribute ( 'placeholder' , $placeholder ) ;
        $this->keyValueFieldList = $keyValueFieldList;
        $this->setField ( implode ( "" , array_keys ( $keyValueFieldList ) ) );
    }
        
    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {
        
        $output         = $this->sufix;
        $attributeSet   = $this->getAttributeSet ( );
        
        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';
        
        $output .= "<input type=\"hidden\" name=\"" . $this->filterContainer . "[$this->field][class]\" value=\"".get_class($this)."\" />\n" ; 
        
        $output .= "<div class=\"form-group\" style=\"margin-right:10px;\">\n";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br />\n" ;
$output .= "<table style=\"border:0;padding:0;margin:0;\"><tr><td style=\"border:0;padding:0;margin:0;\">\n";
        $output .= "\t<select name=\"" . $this->filterContainer . "[$this->field][FIELD]\" class=\" form-control\" style=\"\" >\n";
        $selectedField = Request::get($this->filterContainer . "[$this->field][FIELD]");
        foreach ( $this->keyValueFieldList as $key => $value ) {
            if ( $key === $selectedField ) {
                $output .= "\t\t<option value=\"$key\" selected >$value</option>\n";
            } else {
                $output .= "\t\t<option value=\"$key\">$value</option>\n";
            }
        }
        $output .= "\t</select>\n";

        $attributeSet['name']           = $this->filterContainer . "[$this->field][VALUE]" ;
        $attributeSet['value']          = Request::get($this->filterContainer."[$this->field][VALUE]");

$output .= "</td><td style=\"border:0;padding:0;margin:0;\">\n";
        $output .= "<input " . $this->getAttributeSetAsString ( $attributeSet ) . " >\n";
$output .= "</td></tr></table>\n";
        $output .= "</div>\n";

        $output .= $this->pre;
        return $output;
        
    }
    
    public function formatQueryFilter ( ) {
        $key      = Request::get($this->filterContainer.'['.$this->field.'][FIELD]');
        $value    = Request::get($this->filterContainer.'['.$this->field.'][VALUE]');
        
        if ( !$key    ) return null ;
        if ( !$value  ) return null ;
        
        return eval ( "return \"" . $this->template . "\" ;") ;
    }
    
}
