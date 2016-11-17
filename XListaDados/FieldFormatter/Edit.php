<?php

class Xlib_XListaDados_FieldFormatter_Edit extends Xlib_XListaDados_FieldFormatterAbstract {
    
    private $class = "";
    private $style = "";
    private $attributes = array ( );
    
    public function getInstance ( ) {
        return new Xlib_XListaDados_FieldFormatter_Edit ;
    }
    
    public function format ( $dataIn ) {
        
        $attributeSetString = '';
        
        $attributeSet = array ( 
            'style'         => $this->style ,
            'data-row-id'   => $this->rowID ,
            'class'         => $this->class ,
            'name'          => $this->fieldAlias . "[" . $this->rowID . "]" ,
            'type'          => 'text' ,
            'value'         => $dataIn
        );
        
        if ( $this->isDisabled ( ) ) $this->attributes['disabled'] = 'disabled';
        foreach ( $this->attributes as $key => $value ) $attributeSet[$key] = $value ;
        foreach ( $attributeSet as $key => $value )     $attributeSetString .= " $key=\"$value\"" ;
        
        return "<input $attributeSetString />" ; 
        
    }
    
    public function setClass ( $class ) {
        $this->class = $class ;
        return $this;
    }
    
    public function setStyle ( $style ) {
        $this->style = $style;
        return $this;
    }
    
    public function setAttribute ( $attrKey , $attrVal ) {
        $this->attributes[$attrKey] = $attrVal;
        return $this;
    }
    
    
}