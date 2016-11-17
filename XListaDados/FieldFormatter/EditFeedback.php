<?php

class Xlib_XListaDados_FieldFormatter_EditFeedback extends Xlib_XListaDados_FieldFormatterAbstract {
    
    private $class = "";
    private $style = "";
    private $attributes = array ( );
    
    public function format ( $dataIn ) {
        
        $attributeSetString = '';
        
        $attributeSet = array ( 
            'style'         => $this->style ,
            'data-row-id'   => $this->rowID ,
            'class'         => $this->class ,
            'id'            => $this->fieldAlias . "-" . $this->rowID ,
            'name'          => $this->fieldAlias . "[" . $this->rowID . "]" ,
            'type'          => 'text' ,
            'value'         => $dataIn
        );
        
        if ( $this->isDisabled ( ) ) $this->attributes['disabled'] = 'disabled';
        foreach ( $this->attributes as $key => $value ) $attributeSet[$key] = $value ;
        foreach ( $attributeSet as $key => $value )     $attributeSetString .= " $key=\"$value\"" ;

        
//      classes possíveis para div-feedback-ID:   has-success , has-warning , has-error
//        classes possíveis para icon-feedback-ID: glyphicon-ok , glyphicon-warning-sign , glyphicon-remove etc...
        $output = 
        '<div id="div-feedback-' . $attributeSet['id'] . '" class="form-group has-feedback">
            <label id="label-' . $attributeSet['id'] . '" class="control-label" for="' . $attributeSet['id'] . '"></label>
            <input ' . $attributeSetString . ' />
            <span id="icon-feedback-' . $attributeSet['id'] . '" class="glyphicon form-control-feedback"></span>
        </div>' ;
        
        return $output ; 
        
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