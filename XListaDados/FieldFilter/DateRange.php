<?php

class Xlib_XListaDados_FieldFilter_DateRange extends Xlib_XListaDados_FieldFilterAbstract {

    protected $template = " \$key BETWEEN STR_TO_DATE('\$dateFrom 00:00:00','%d/%m/%Y %H:%i:%s') AND STR_TO_DATE('\$dateTo 00:00:00','%d/%m/%Y %H:%i:%s')" ;
    protected $defaultDateFrom = '';
    protected $defaultDateTo = '';

    public static function getInstance ( $field , $label , $defaultDateFrom = "" , $defaultDateTo = "" ) {
    	return new Xlib_XListaDados_FieldFilter_DateRange ( $field , $label , $defaultDateFrom , $defaultDateTo ) ;
    }

    public function __construct ( $field , $label , $defaultDateFrom = "" , $defaultDateTo = "" ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
        $this->defaultDateFrom = $defaultDateFrom ;
        $this->defaultDateTo = $defaultDateTo ;
        if($field == ""){
        	$this->template="";
        }
    }

    public function getFieldFormatter ( ) {
        $script =
            'this.value=this.value.replace(/[^\d\/]+/,\'\');'
        ;

        return $script ;
    }

    public function formatQueryFilter ( ) {

        $dateFrom   = Xlib_Request::get($this->filterContainer.'['.$this->field.'][DE]');
        $dateTo     = Xlib_Request::get($this->filterContainer.'['.$this->field.'][ATE]');

        if ( !$dateFrom ) $dateFrom = $this->defaultDateFrom;
        if ( !$dateTo ) $dateTo = $this->defaultDateTo;

        $key = $this->field;

        if ( !$dateFrom ) return null ;
        if ( !$dateTo   ) return null ;

        return eval ( "return \"" . $this->template . "\" ;") ;
    }

    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {

        $dateFrom   = Xlib_Request::get($this->filterContainer.'['.$this->field.'][DE]');
        $dateTo     = Xlib_Request::get($this->filterContainer.'['.$this->field.'][ATE]');

        if ( !$dateFrom ) $dateFrom = $this->defaultDateFrom;
        if ( !$dateTo ) $dateTo = $this->defaultDateTo;

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
        $attributeSet['value']          = $dateFrom;
        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>" ;
        $output .= "De: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";

        $attributeSet['name']           = $this->filterContainer . "[$this->field][ATE]" ;
        $attributeSet['id']             = $baseId . "ATE" ;
        $attributeSet['value']          = $dateTo;
        $output .= " At&eacute;: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";

    // todass as classes filhas de Xlib_XListaDados_FieldFilterAbstract que enviarem um conjunto de dados
    // devem especificar qual classe gerou os dados e implementar um método que trate esses dados para a XListaDados
    $output .= "<input type=\"hidden\" name=\"" . $this->filterContainer . "[$this->field][class]\" value=\"".get_class($this)."\" />" ;

        $output .= "</div>";

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