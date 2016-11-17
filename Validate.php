<?php
class Validate {
	
    /**
     * Valida uma data no formato DD/MM/YYYY
     * @param string $date string no formato DD/MM/YYYY
     */
    public static function date ( $date ) {
        
        if ( !preg_match ( '/^\d\d\/\d\d\/\d\d\d\d$/' , $date ) ) return false ;
        $dateArr = explode ( "/" , $date );
        $totalDiasMes = date ( "t" , mktime ( 0 , 0 , 0 , $dateArr[1] , 1 , $dateArr[2] ) );
        if ( $dateArr[1] > 12 ) return false ;
        if ( $dateArr[0] > $totalDiasMes ) return false ;
        
        return true ;
        
    }
    
    public static function cpf ( $number ) {
        die ( "Precisa implementar");
    }

    public static function cnpj ( $number ) {
        die ( "Precisa implementar");
    }

}