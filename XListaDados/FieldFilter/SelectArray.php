<?php

class Xlib_XListaDados_FieldFilter_SelectArray extends Xlib_XListaDados_FieldFilterAbstract {
    
    protected $label;
    protected 
    
    public function __construct ( $field , $label = "" , $query , $keysField , $valuesField , $default = "" ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
    }    
    
    public function getFieldFormatter ( ) {
        $script = 
            'this.value=this.value.replace(/[^\d\/]+/,\'\');' 
        ;
        
//$('#cmb_CIMtrek_DailyshipCo_CustomerName select').bind('keypress', function(e) {
//    var code = (e.keyCode ? e.keyCode : e.which);
//     if(code == 13) { //Enter keycode
//       //Do something
//         alert("Enter key Pressed");
//     }
//});
        
        return $script ;
    }
    
    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {
        
        $output = $this->getPrefix ();
        $attributeSet   = $this->getAttributeSet ( );
        $attributeSet['style'] = empty ( $attributeSet['style'] ) ? "width:101px" : $attributeSet['style'] . ";width:101px" ;
        $attributeSet['placeholder']    = "__/__/____";
        $attributeSet['onKeyPress']     = $this->getFieldFormatter();
        $attributeSet['onKeyUp']        = $this->getFieldFormatter();
        $attributeSet['onKeyDown']      = $this->getFieldFormatter();
        $attributeSet['maxlength']      = '10';
        $baseId                         = $attributeSet['id'];
        
        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';
        
        
        $attributeSet['name']           = $this->filterContainer . "[$this->field][DE]" ;
        $attributeSet['id']             = $baseId . "DE" ;
        $attributeSet['value']          = Request::get($this->filterContainer . "[$this->field][DE]");
        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>" ;
        $output .= "De: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        
        $attributeSet['name']           = $this->filterContainer . "[$this->field][ATE]" ;
        $attributeSet['id']             = $baseId . "ATE" ;
        $attributeSet['value']          = Request::get($this->filterContainer . "[$this->field][ATE]");
        $output .= " Até: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        $output .= "</div>";
        
//        $output .= "<div class=\"form-group\">";
//        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>" ;
//        $output .= "</div>";

        $output .= "
        <script>
            $(function(){
                $(\"#".$baseId."DE\").datepicker();
                $(\"#".$baseId."ATE\").datepicker();
            });
        </script>
        " ;
        
        $output .= $this->getSufix() ;
        return $output;
        
    }
    
}