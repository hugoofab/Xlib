<?php

class Xlib_Json {

    /**
     * Gera um json com base em um array
     * @staticvar array $jsonReplaces
     * @param type $array
     * @return string
     */
    public static function encode ( $array = false ) {

        if ( is_null ( $array ) ) return 'null' ;
        if ( $array === false ) return 'false' ;
        if ( $array === true ) return 'true' ;
        if ( is_scalar ( $array ) ) {
            if ( is_float ( $array ) ) {
                // Always use "." for floats.
                return floatval ( str_replace ( "," , "." , strval ( $array ) ) ) ;
            }

            if ( is_string ( $array ) ) {
                static $jsonReplaces = array ( array ( "\\" , "/" , "\n" , "\t" , "\r" , "\b" , "\f" , '"' ) , array ( '\\\\' , '\\/' , '\\n' , '\\t' , '\\r' , '\\b' , '\\f' , '\"' ) ) ;
                return '"' . str_replace ( $jsonReplaces[0] , $jsonReplaces[1] , $array ) . '"' ;
            }
            else return $array ;
        }
        $isList = true ;
        for ( $i = 0 , reset ( $array ) ; $i < count ( $array ) ; $i++ , next ( $array ) ) {
            if ( key ( $array ) !== $i ) {
                $isList = false ;
                break ;
            }
        }
        $result = array ( ) ;
        if ( $isList ) {
            foreach ( $array as $v )
                $result[] = Xlib_Json::encode ( $v ) ;
            return '[' . join ( ',' , $result ) . ']' ;
        } else {
            foreach ( $array as $k => $v )
                $result[] = Xlib_Json::encode ( $k ) . ':' . Xlib_Json::encode ( $v ) ;
            return '{' . join ( ',' , $result ) . '}' ;
        }
    }

    /**
     * precisa criar uma versão compatível com a acentuação do português brasileiro
     * @param string $json
     * @return object
     */
    public static function decode ( $json ) {

		// $json = '{"template_name":"Noticia1","template_file":"template.html","thumb":"thumb.png","description":"Pode ser utilizado para"}';

		// $json = '{
		// 	"template_name" : "Tabela 1",
		// 	"template_file" : "template.html",
		// 	"thumb"         : "thumb.png" ,
		// 	"description"   : "Pode ser utilizado para apresentação de uma dados tabulados, a tabela pode ser editada podendo adicionar linhas e colunas."
		// }';

		$json = utf8_encode($json);
		$output = json_decode($json); 
		return $output;
		
    }



}