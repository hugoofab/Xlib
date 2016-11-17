<?php

class Xlib_XListaDados_FieldFilter_SelectQuery extends Xlib_XListaDados_FieldFilterAbstract {

    protected $label;
    protected $data             = array ();
    protected $emptyValue       ;
    protected $default          ;
    protected $keysField        ;
    protected $valuesField      ;

    public static function getInstance ( $field , $label , $query , $keysField , $valuesField , $default = "" , $emptyValue = "Selecione" ) {
    	return new Xlib_XListaDados_FieldFilter_SelectQuery ( $field , $label , $query , $keysField , $valuesField , $default , $emptyValue ) ;
    }

    public function __construct ( $field , $label , $query , $keysField , $valuesField , $default = "" , $emptyValue = "Selecione" ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
        $this->emptyValue   = $emptyValue ;
        $this->keysField    = $keysField ;
        $this->valuesField  = $valuesField ;
        $model  = new ModelAbstract();
        $res    = $model->fetchAll ( $query );
        if ( !empty ( $res ) ) $this->data = $res ;
    }

    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {

        $output             = $this->getPrefix ();
        $attributeSet       = $this->getAttributeSet ( );

        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';


        $selected = Xlib_Request::get ( $this->filterContainer . "[$this->field]" , $this->default ) ;
        $attributeSet['name']           = $this->filterContainer . "[$this->field]" ;
        $output .= "\n<div class=\"form-group\">\n";
        $output .= "\t<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>\n" ;
        $output .= "\t<select " . $this->getAttributeSetAsString ( $attributeSet ) . " >\n";

        // se vai ter uma opção vazia como "Selecione uma opção" por exemplo
        if ( $this->emptyValue !== false ) {
            $selectedAttr = ( $selected === "" ) ? " selected " : "" ;
            $output .= "\t\t<option value=\"\" $selectedAttr >" . $this->emptyValue . "</option>\n";
        }

        // restante dos valores
        foreach ( $this->data as $data ) {
            $selectedAttr = ( $selected === $data[$this->keysField] ) ? " selected " : "" ;
            $output .= "\t\t<option value=\"" . $data[$this->keysField] . "\" $selectedAttr >" . $data[$this->valuesField] . "</option>\n";
        }
        $output .= "\t</select>\n";
        $output .= "</div>\n";

        $output .= $this->getSufix() ;
        return $output;

    }

}