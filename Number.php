<?php

class Number {

    /**
     * retorna o valor float mesmo que o número seja uma string com , no lugar de .
     * @param $numero (mixed) numero que pode ser uma string ou float
     * @return (float)
     */
    public static function toFloat ( $numero , $br = false ) {

        $originalFloat  = (float) $numero ;
        $numero         = preg_replace ( '/[^\d\.,]+/' , '' , $numero ) ;
        $temPonto       = ( strpos ( $numero , '.' ) === false ) ? false : true ;
        $temVirgula     = ( strpos ( $numero , ',' ) === false ) ? false : true ;

        if ( $temPonto && $temVirgula && preg_match ( '/^(.*)([^\d])(\d+)$/' , $numero , $arrayRes ) ) $numero = ( preg_replace ( '/[^\d]+/' , '' , $arrayRes[1] ) . "." . $arrayRes[3] ) ;
        if ( $temVirgula ) $numero = str_replace ( "," , '.' , $numero ) ;

        $numero = (float) $numero;
        if ( $originalFloat < 0 ) $numero *= -1 ;

        if ( $br === true ) return str_replace ( "." , "," , $numero );

        return $numero ;

    }

    public static function toMoney ( $numero , $casasDecimais = 2 , $symbol = "R$ " ) {

        $numero = Number::toFloat ( $numero );
        return $symbol . number_format ( $numero , $casasDecimais , ',' , '.' );

    }

    public static function strip ( $numero ) {

		return preg_replace ( '/[^\d]+/' , '' , $numero ) ;

    }

}