<?php

abstract class Xlib_XListaDados_InsertFieldsAbstract {
    
//    (
//        UPPER ( TRANSLATE ( CAMPO_DA_TABELA , 'ãõÃÕüÜâêîôûÂÊÎÔÛçÀàáéíóúÇÁÉÍÓÍÚ' , 'aoAOuUaeiouAEIOUcAaaeiouCAEIOU' ) )
//        LIKE 
//        UPPER ( TRANSLATE ( '%STRING A SER PROCURADA%' , 'ãõÃÕüÜâêîôûÂÊÎÔÛçÀàáéíóúÇÁÉÍÓÍÚ' , 'aoAOuUaeiouAEIOUcAaaeiouCAEIOU' ) )
//    )
    
//    protected $tableID          = "";
//    protected $filterContainer  = "";
//    protected   static $filtersGlobalID     = "FILTERS";
    protected $field            = "";
    protected $label            = "";
    protected $template         = '
<div class="form-group">
    $label
    <div class="col-sm-10">
        $field
    </div>
</div>' ;
    protected $prefix           = "";
    protected $sufix            = "";
    private     static $defaultPrefix       = "";
    private     static $defaultSufix        = "&nbsp;&nbsp;";
    protected $attributes       = array ( );
    protected $disableIf_list   = array ( ) ;
    
    //if true, ignores other filters if is set
    public $mandatory        = false ;

    public function formatQueryFilter ( ) {
        $value = Request::get($this->filterContainer."[".$this->field."]");
        if ( !$value ) return null ;
        return eval ( "return \"" . $this->template . "\" ;") ;
    }
    
    public function setMandatory ( $mandatory ) {
        $this->mandatory = $mandatory ;
        return $this;
    }
    
    public final function resetFilter ( ) {
        unset ( $_COOKIE[$this->filterContainer.'['.$this->field.']'] ) ;
//        setcookie($this->filterContainer.'['.$this->field.']', null, -1, '/');
    }
    
    public function isMandatory (  ) {
        return $this->mandatory ;
    }
    
    public final function setTemplate ( $template ) {
        $this->template = $template ;
        return $this;
    }
    
    public static function getFiltersGlobalID ( ) {
        return self::$filtersGlobalID ;
    }
    
    public final function setTableID ( $tableID ) {
        $this->tableID = $tableID ;
        $this->filterContainer = $tableID . Xlib_XListaDados_FieldFilterAbstract::getFiltersGlobalID ( ) ;
        return $this;
    }
    
    public final function setData ( $data ) {
        $this->data = $data ;
        return $this;
    }
    
    public final function setField ( $field ) {
        $this->field = $field ;
        return $this;
    }
    
    public final function getField ( ) {
        return $this->field;
    }
    
    public final function setLabel ( $label ) {
        $this->label = $label ;
        return $this;
    }
    
    public final function addDisableIf ( $condition ) {
        $this->disableIf_list[] = $condition ;
        return $this;
    }
    
    public function isDisabled ( ) {
        $data = $this->data ; // só para conveniencia do programador ;)
        foreach ( $this->disableIf_list as $condition ) if ( eval ( "return ( $condition ) ; " ) ) return true ;
        return false ;
    }
    
    public function setSufix ( $sufix ) {
        $this->sufix = $sufix;
        return $this;
    }
    
    public function setPrefix ( $prefix ) {
        $this->prefix = $prefix;
        return $this;
    }
    
    public function getSufix ( ) {
        return Xlib_XListaDados_FieldFilterAbstract::$defaultSufix . $this->sufix ;
    }
    
    public function getPrefix ( ) {
        return Xlib_XListaDados_FieldFilterAbstract::$defaultPrefix . $this->prefix ;
    }
    
    public static function setDefaultPrefix ( $prefix ) {
        Xlib_XListaDados_FieldFilterAbstract::$defaultPrefix = $prefix ;
        return $this;
    }
    
    public static function setDefaultSufix ( $sufix ) {
        Xlib_XListaDados_FieldFilterAbstract::$defaultSufix = $sufix ;
        return $this;
    }
    
    public function setAttribute ( $attrKey , $attrVal ) {
        $this->attributes[$attrKey] = $attrVal;
        return $this;
    }
    
    public function getAttributeSet ( ) {
        
        $attributeSet = array ( 
            'name'          => $this->filterContainer . "[$this->field]" ,
            'id'            => preg_replace ( '/[^a-zA-Z0-9_-]+/' , "-" , $this->filterContainer . "-$this->field" ),
            'type'          => "text" ,
            'class'         => "",
            'value'         => Request::get($this->filterContainer."[$this->field]")
        );
        
        if ( $this->isDisabled ( ) ) $this->attributes['disabled'] = 'disabled';
        
        foreach ( $this->attributes as $key => $value ) $attributeSet[$key] = $value ;
        
        $attributeSet['class'] .= " form-control";
        
        return $attributeSet ;
        
    }
    
    public function getAttributeSetAsString ( Array $attributeSet = null ) {
        
        $attributeSetString = '';
        if ( empty ( $attributeSet ) ) $attributeSet = $this->getAttributeSet ( );
        
        foreach ( $attributeSet as $key => $value ) $attributeSetString .= " $key=\"$value\" " ;
        
        return $attributeSetString ; 
        
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
        
        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br>" ;
        $output .= "<input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        $output .= "</div>&nbsp;&nbsp;";

        $output .= $this->pre;
        return $output;
        
    }
    
}