<?php

abstract class Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $fieldAlias ;
    protected $data ;
    protected $rowID ;
    protected $hideIf_list    = array ( ) ;
    protected $disableIf_list = array ( ) ;
    protected $styleIf_list   = array ( ) ;
    
    public abstract function format ( $input ) ;
    
    public final function setFieldAlias ( $alias ) {
        $this->fieldAlias = $alias;
        return $this;
    }
    
    public final function setData ( $data ) {
        $this->data = $data ;
        return $this;
    }
    
    public final function setRowID ( $rowId ) {
        $this->rowID = $rowId ;
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
    
}