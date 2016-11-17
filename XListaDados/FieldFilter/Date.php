<?php

class Xlib_XListaDados_FieldFilter_Date extends Xlib_XListaDados_FieldFilterAbstract {
    
    public function __construct ( $field , $label = "" ) {
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
        
        $output         = $this->getSufix() ;
        $attributeSet   = $this->getAttributeSet ( );
        $attributeSet['style'] = empty ( $attributeSet['style'] ) ? "width:101px" : $attributeSet['style'] . ";width:101px" ;
        $attributeSet['placeholder']    = "__/__/____";
        $attributeSet['onKeyPress']     = $this->getFieldFormatter();
        $attributeSet['onKeyUp']        = $this->getFieldFormatter();
        $attributeSet['onKeyDown']      = $this->getFieldFormatter();
        $attributeSet['maxlength']      = '10';
       
        
        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';
        
        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>" ;
        $output .= "<input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        $output .= "</div>";

        $output .= "
        <script>
            $(function(){
                $(\"#".$attributeSet['id']."\").datepicker();
            });
        </script>
        " ;
        
        $output .= $this->getPrefix ();
        return $output;
        
    }
    
}