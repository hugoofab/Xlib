<?php

class Xlib_XListaDados_FieldFormatter_Link extends Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $id = 'link' ;
    protected $class ;
    protected $href ; 
    protected $title ;
    protected $onClick ;
    
    public function __construct ( $href = "" , $class = '' , $id = '' , $onclick = '' , $title = "" ) {
        
        if ( empty ( $href ) ) $href = 'javascript:;' ;
        
        $this->setId ( $id ) ;
        $this->setOnClick ( $onclick ) ;
        $this->setClass ( $class ) ;
        $this->setHref ( $href ) ;
        $this->setTitle ( $title ) ;
        
    }

    public function setOnClick ( $onClick ) {
        $this->onClick = (string) $onClick ;
    }

    public function setId ( $id ) {
        $this->id = strtolower ( $id ) ;
    }

    public function setClass ( $class ) {
        $this->class = (string) $class ;
    }

    public function setHref ( $href ) {
        $this->href = (string) $href ;
    }

    public function setTitle ( $title ) {
        $this->title = (string) $title ;
    }
    
    public function format ( $dataIn ) {
        if ( empty ( $this->id ) ) $this->setId ( "link-" . $this->fieldAlias );
//        style=\"display:block;\"
        return "<a id=\"" . $this->id . "-" . $this->rowID . "\" class=\"$this->class\" href=\"$this->href\" onclick=\"$this->onClick\" data-row-id=\"$this->rowID\">" . $dataIn . "</a>";
    }
    
    
}