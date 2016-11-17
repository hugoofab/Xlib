<?php

class Xlib_XListaDados_FieldFormatter_Date extends Xlib_XListaDados_FieldFormatterAbstract {
    
    protected $dia ;
    protected $mes ;
    protected $ano ;
    protected $hor = 0 ;
    protected $min = 0 ;
    protected $seg = 0 ;
    protected $mask ;
    
    public function __construct ( $mask = "d/m/Y H:i:s" ) {
        $this->mask = $mask ;
    }
    
    public function format ( $date ) {
        
        $date = trim ( $date );
        
        if ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})$/' , $date , $resultArray ) ) {
            $this->dia = (int) $resultArray[3] ;
            $this->mes = (int) $resultArray[2] ;
            $this->ano = (int) $resultArray[1] ;
        } else if ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})\s(\d\d):(\d\d):(\d\d)$/' , $date , $resultArray ) ) {
            $this->dia = (int) $resultArray[3] ;
            $this->mes = (int) $resultArray[2] ;
            $this->ano = (int) $resultArray[1] ;
            $this->hor = (int) $resultArray[4] ;
            $this->min = (int) $resultArray[5] ;
            $this->seg = (int) $resultArray[6] ;
        } else {
            return $date ;
        }
        
        return date ( $this->mask , mktime ( $this->hor , $this->min , $this->seg , $this->mes , $this->dia , $this->ano ) ) ;
        
    }
    
}