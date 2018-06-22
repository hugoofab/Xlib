<?php

class Xlib_XListaDados_XButton {
	
    protected $elementId ;
    protected $elementClass ;
    protected $iconClass ;
    protected $style ;
    protected $cellParams       = array ( ) ;
    protected $label            = "" ;
    protected $rowID            = "" ;
    protected $data ;
    protected $onClick          = "" ;
    protected $buttonType       = "button";
    protected $attributes       = array ( );
    protected $hideIf_list      = array ( ) ;
    protected $disableIf_list   = array ( ) ;
    protected $styleIf_list     = array ( ) ;
    
    public function __construct ( $label , $class = "" , $iconClass = "" ) {
        $this->label = $label ;
        $this->elementClass = $class ;
        $this->iconClass = $iconClass ;
    }
    
    public static function getInstance ( $label , $class = "" , $iconClass = "" ) {
        $instance = new Xlib_XListaDados_XButton ( $label , $class , $iconClass ) ;
        return $instance ;
    }
    
    public function __toString ( ) {
        $attributeSetString = '';
        $this->style = "";
        
        foreach ( $this->hideIf_list as $key => $value ) {
        	if ( $this->data[$key] == $value ) {
        		$this->style .= ";display:none;";
        	}
        }

        $attributeSet = array ( 
            'type'           => $this->buttonType ,
            'onClick'        => $this->onClick,
            'class'          => "btn " . $this->elementClass ,
            'data-row-id'    => $this->rowID ,
            'style'          => $this->style
        ) ;
        
        foreach ( $this->cellParams as $cellParam ) {
            $attributeSet[strtolower($cellParam)] = $this->data[$cellParam] ;
        }

        if ( $this->isDisabled ( ) ) {
            $this->attributes['disabled'] = 'disabled';
        } else {
            unset ( $this->attributes['disabled'] );
        }
        
        foreach ( $this->attributes as $key => $value ) $attributeSet[$key] = $value ;
        foreach ( $attributeSet as $key => $value )     $attributeSetString .= " $key=\"$value\"" ;
        
        $output = 
            "<button $attributeSetString>" .
                "<span class=\"".$this->iconClass."\" ></span> " .
                $this->label .
            "</button>"
        ;
            
        return $output;
        
    }
    
    public function addHideIf ( $condition ) {
    	$this->hideIf_list = $condition;
    	return $this ;
    }
    
    public function isDisabled ( ) {

        $data = $this->data ; // só para conveniencia do programador ;)
        
//        foreach ( $this->data as $key => $val ) {
//            if ( !preg_match ( '//')
//            $$key = $val ;
//        }

        foreach ( $this->disableIf_list as $condition ) {
            if ( eval ( "return ( $condition ) ; " ) ) return true ;
        }
        return false ;
    }
    
    public function addDisableIf ( $condition ) {
        $this->disableIf_list[] = $condition ;
        return $this;
    }
    
    public function addStyleIf ( $condition , $style ) {
//        @TODO 
//        $this->styleIf_list
    }
    
    public function setRowID ( $id ) {
        $this->rowID = $id ;
        return $this;
    } 
    
    public function setData ( $data , $paramList = array ( ) ) {
        $this->cellParams = $paramList;
        $this->data = $data ;
        return $this;
    }
    
    public function onClick ( $function ) {
        $this->onClick = $function;
        return $this;
    }
    
    public function align ( $direction ) {
        return $this->addStyle ( "float:$direction" );
    }
    
    public function addStyle ( $style ) {
        $styleList = explode ( ";" , $this->style );
        $styleList[] = $style ;
        $this->style = implode ( ";" , $styleList );
        return $this;
    }
    
    public function setType ( $type ) {
        $this->buttonType = $type ;
        return $this;
    }
    
    public function setAttribute ( $key , $value = "" ) {
        $this->attributes[$key] = $value;
        return $this;
    }
    
}
