<?php

class Xlib_XListaDados_FieldFilter_Checkbox extends Xlib_XListaDados_FieldFilterAbstract {
    
    protected $placeholder      = "";
    protected $buttonLabel      = "";
    protected $checkedWhere     = "";
    protected $uncheckedWhere   = "";
    protected $default          = false ;
    
    public function __construct ( $label = "" , $buttonLabel = "" , $checkedWhere , $uncheckedWhere , $default = false ) {
        $this->setField ( substr ( md5 ( $label . $buttonLabel . $checkedWhere . $uncheckedWhere ) , 0 , 10 ) ) ;
        $this->setLabel ( $label );
        $this->buttonLabel = $buttonLabel ;
        $this->checkedWhere = $checkedWhere ;
        $this->uncheckedWhere = $uncheckedWhere ;
        $this->default = $default ;

        if ( !Request::get ( $this->filterContainer . "[$this->field][VALUE]" ) ) {
            $fieldValue = $this->default ? "1" : "0" ;
        }
        
        Request::setPost($this->filterContainer,array ( $this->field => array ( 'class' => get_class($this) , 'VALUE' => $fieldValue ) ) ) ;
    }
    
    /**
     * @return type
     */
    public function __toString ( ) {
        
        $output         = $this->sufix;
        $attributeSet   = $this->getAttributeSet ( );
        
        
        if ( Request::get ( $this->filterContainer . "[$this->field][VALUE]" ) === '1' ) {
            $this->default = true ;
        } else if ( Request::get ( $this->filterContainer . "[$this->field][VALUE]" ) === '0' ) {
            $this->default = false ;
        }
        
        if ( $this->default ) {
            $fieldValue = "1" ;
            $defaultIconClass = "glyphicon glyphicon-check" ;
        } else {
            $fieldValue = "0" ;
            $defaultIconClass = "glyphicon glyphicon-unchecked" ;
        }
        
        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';
        
        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br>" ;
        $output .= "<input type=\"hidden\" name=\"" . $this->filterContainer . "[$this->field][class]\" value=\"".get_class($this)."\" />" ; 
        $output .= "<input type=\"hidden\" name=\"" . $this->filterContainer . "[$this->field][VALUE]\" value=\"$fieldValue\" id=\"" . $this->filterContainer . "[$this->field]\" />" ;
        $output .= "<button type=\"button\" onclick=\"if ( document.getElementById('" . $this->filterContainer . "[$this->field]').value === '0' ){document.getElementById('" . $this->filterContainer . "[$this->field]').value = '1';document.getElementById('" . $this->filterContainer . "[$this->field]-icon').setAttribute('class','glyphicon glyphicon-check');}else{document.getElementById('" . $this->filterContainer . "[$this->field]').value = '0';document.getElementById('" . $this->filterContainer . "[$this->field]-icon').setAttribute('class','glyphicon glyphicon-unchecked')}\" class=\"btn btn-default\" data-toggle=\"button\"><span id=\"" . $this->filterContainer . "[$this->field]-icon\" class=\"$defaultIconClass\"></span> $this->buttonLabel</button>" ;
//        $output .= "<input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        $output .= "</div>";

        $output .= $this->pre;
        return $output;
        
    }
        
    public function formatQueryFilter ( ) {
        
        $value     = Request::get($this->filterContainer.'['.$this->field.'][VALUE]',$this->default);
        
        
        if ( Request::get ( $this->filterContainer . "[$this->field][VALUE]" ) === '1' ) {
            $this->default = true ;
        } else if ( Request::get ( $this->filterContainer . "[$this->field][VALUE]" ) === '0' ) {
            $this->default = false ;
        }
        
        if ( $this->default ) {
            return $this->checkedWhere ;
        } else {
            return $this->uncheckedWhere ;
        }
        
    }    

}

?>
    
