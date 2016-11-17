<?php

/**
 * Filtro de date range que permite selecionar uma data entre uma lista de datas 
 * Ex.: Filtrar por Data: (INICIO|FIM|APROVAÇÃO|RETORNO) que esteja entre DD/MM/YYYY e DD/MM/YYYY
 * 
 * Uso:
 * $arrayDatasDisponiveis = array ( 
 *      "C.DT_INICIO_CBA" => "Início:" , 
 *      "P.DT_VENCIM_CAP" => "Vencimento:" 
 * ) ;
 * 
 * $filtroMultiData = new Xlib_XListaDados_FieldFilter_DateRangeMultifield ( "Data:" , $arrayDatasDisponiveis ) ;
 * 
 */

class Xlib_XListaDados_FieldFilter_DateRangeMultifield extends Xlib_XListaDados_FieldFilterAbstract {
    
    protected $template = " \$field BETWEEN TO_DATE ( '\$dateFrom 00:00:00' , 'DD/MM/YYYY HH24:MI:SS') AND TO_DATE ( '\$dateTo 23:59:59' , 'DD/MM/YYYY HH24:MI:SS') " ;
    protected $keyValueFieldList = array ( );
    protected $defaultDateFrom  = '';
    protected $defaultDateTo    = '';
    protected $defaultField     = '';
    /**
     * Filtro de date range que permite selecionar uma data entre uma lista de datas 
     * Ex.: Filtrar por Data: (INICIO|FIM|APROVAÇÃO|RETORNO) que esteja entre DD/MM/YYYY e DD/MM/YYYY
     * @param string $label texto para apresentação
     * @param array $keyValueFieldList array de conjunto key => value contendo 'NOME_DO_CAMPO' => 'Descrição do campo' 
     */
    public function __construct ( $label , array $keyValueFieldList , $defaultField = "" , $defaultDateFrom = "" , $defaultDateTo = "" ) {
        
        $this->keyValueFieldList = $keyValueFieldList;
        $this->setField ( implode ( "" , array_keys ( $keyValueFieldList ) ) );
        $this->setLabel($label);
        
        $this->defaultDateFrom  = $defaultDateFrom ;
        $this->defaultDateTo    = $defaultDateTo ;
        $this->defaultField     = $defaultField ;
        
    }    
    
    public function getFieldFormatter ( ) {
        $script = 
            'this.value=this.value.replace(/[^\d\/]+/,\'\');' 
        ;
        
        return $script ;
    }
    
    public function formatQueryFilter ( ) {
        $field      = Request::get($this->filterContainer.'['.$this->field.'][FIELD]');
        $dateFrom   = Request::get($this->filterContainer.'['.$this->field.'][DE]');
        $dateTo     = Request::get($this->filterContainer.'['.$this->field.'][ATE]');
        
        if ( !$field ) $field = $this->defaultField;
        if ( !$dateFrom ) $dateFrom = $this->defaultDateFrom;
        if ( !$dateTo ) $dateTo = $this->defaultDateTo;

        if ( !$field    ) return null ;
        if ( !$dateFrom ) return null ;
        if ( !$dateTo   ) return null ;
        
        return eval ( "return \"" . $this->template . "\" ;") ;
    }
    
    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {
        $selectedField = Request::get($this->filterContainer.'['.$this->field.'][FIELD]');
        $dateFrom   = Request::get($this->filterContainer.'['.$this->field.'][DE]');
        $dateTo     = Request::get($this->filterContainer.'['.$this->field.'][ATE]');
        
        if ( !$selectedField ) $selectedField = $this->defaultField;
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
        
        // VARIOS FIELDS
        $output .= "<select name=\"" . $this->filterContainer . "[$this->field][FIELD]\" class=\" form-control\" >";
        
        foreach ( $this->keyValueFieldList as $key => $value ) {
            if ( $key === $selectedField ) {
                $output .= "<option value=\"$key\" selected >$value</option>";
            } else {
                $output .= "<option value=\"$key\">$value</option>";
            }
        }
        $output .= "</select>&nbsp;";
        
        $output .= "De: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        
        $attributeSet['name']           = $this->filterContainer . "[$this->field][ATE]" ;
        $attributeSet['id']             = $baseId . "ATE" ;
        $attributeSet['value']          = $dateTo;
        $output .= " Até: <input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        
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