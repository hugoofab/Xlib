<?php
/**
 * author: Hugo Ferreira
 * last update: 07-out-2014
 */


class KeyGen2 {

    private $salt ;
    private $minutesToExpire     = 5 ;

    /**
     *
     * @param type $hashPadding
     * @param type $minutesToExpire
     */
    public function __construct ( $hashPadding = '' , $minutesToExpire = 5 ) {
    	$this->salt = APPLICATION_SALT ;
        $this->setMinutesToExpire($minutesToExpire);
    }

    /**
     * em quantos minutos o hash irá expirar
     * @param type $min
     */
    public function setMinutesToExpire ( $min ) {
        $this->minutesToExpire = $min ;
    }

    /**
     * em quantos dias o hash irá expirar
     * @param type $min
     */
    public function setDaysToExpire ( $day ) {
        $this->minutesToExpire = $day * 60 * 24 ;
    }

    /**
     * Gera uma chave que expira em x minutos
     * @param string $optionalPass uma string opcional para ajudar na criptografia. Será obrigatória para validar a chave gerada
     * @param int $minutesToExpire em quantos minutos a chave deverá expirar
     * @return string chave temporária
     */
    public function getKey ( $optionalPass = '' , $minutesToExpire = false ) {
        if ( $minutesToExpire === false ) $minutesToExpire = $this->minutesToExpire ;
        $time               = (int) time();
        $random             = sha1 ( mt_rand ( ) ) ;
        $chave1             = hash ( "sha256" , $random . $this->salt . $optionalPass . $random ) ;
        $expira             = ( $this->minutesToExpire * 60 ) + $time ;
		$segundoHash = hash ( "sha256" , $chave1 . $this->salt . $optionalPass . dechex ( $expira ) );

        $output             = $chave1 . $segundoHash . dechex ( $expira );

        return $output ;
    }

    /**
     *
     * @param type $key
     * @return boolean
     */
    public function checkKey ( $key , $optionalPass = '' ) {

        if ( strlen ( $key ) < 128 ) return false ;
        $time               = (int) time();
        $random             = substr ( $key , 0 , 64 ) ;
        $hashReceived       = substr ( $key , 64 , 64 ) ;
        $expiresHex         = substr ( $key , 128 ) ;
        $expires            = hexdec ( $expiresHex );

        $hashGenerated      = hash ( "sha256" , $random . $this->salt . $optionalPass . $expiresHex );

        if ( $time > $expires ) return false ;
        if ( $hashReceived === $hashGenerated ) {
            return true ;
        } else {
            return false ;
        }
    }

    /**
     * criptografa um texto com uma chave de forma que possa ser descriptografado, utilizando AES-256 e modo CBC
     *
     * o excesso de operações com a senha e entropia tem o objetivo de tornar o processamento de criptografia/descriptografia mais lento
     * e utilizando um trecho do base64 conseguimos aumentar o range de caracteres na senha final, para que (vai saber) em um ataque
     * de força bruta, não seja necessário apenas caracteres entre 0-9 e A-F, tornando inviável qualquer tentativa de descriptografar
     * @param  string $string texto a ser criptografado
     * @param  string $key    senha
     * @return string         texto criptografado
     */
    public function encrypt ( $string , $key = '' ) {
		return mcrypt_encrypt ( MCRYPT_RIJNDAEL_256 , substr ( base64_encode ( md5 ( base64_encode ( $this->salt ) ) . sha1 ( $key . $this->salt ) ) , 3 , 32 ) , $string , MCRYPT_MODE_CBC , substr ( base64_encode ( md5 ( $this->salt . $key ) . md5 ( $key ) ) , 2 , 32 ) )  ;

		// ****************************************************************
		// INSECURE MODE BELOW ********************************************
		// ****************************************************************
        // $key    = sha1 ( $this->secretHashPadding . $key ) ;
        // $result = '';
        // for($i = 0; $i < strlen($string); $i++) {
        //     $char = substr($string, $i, 1);
        //     $keychar = substr($key, ($i % strlen($key))-1, 1);
        //     $char = chr(ord($char) + ord($keychar));
        //     $result .= $char;
        // }
        // return base64_encode($result) ;
    }

    /**
     * descriptografa um texto criptografado com uma chave utilizando AES-256 e modo CBC
     *
     * o excesso de operações com a senha e entropia tem o objetivo de tornar o processamento de criptografia/descriptografia mais lento
     * e utilizando um trecho do base64 conseguimos aumentar o range de caracteres na senha final, para que (vai saber) em um ataque
     * de força bruta, não seja necessário apenas caracteres entre 0-9 e A-F, tornando inviável qualquer tentativa de descriptografar
     * @param  string $string texto a ser descriptografado
     * @param  string $key    senha
     * @return string         texto descriptografado
     */
    public function decrypt ( $string , $key = '' ) {
		return mcrypt_decrypt ( MCRYPT_RIJNDAEL_256 , substr ( base64_encode ( md5 ( base64_encode ( $this->salt ) ) . sha1 ( $key . $this->salt ) ) , 3 , 32 ) , $string , MCRYPT_MODE_CBC , substr ( base64_encode ( md5 ( $this->salt . $key ) . md5 ( $key ) ) , 2 , 32 ) )  ;

		// ****************************************************************
		// INSECURE MODE BELOW ********************************************
		// ****************************************************************
		// $key    = sha1 ( $this->secretHashPadding . $key ) ;
		// $string = base64_decode( $string ) ;
		// for($i = 0; $i < strlen($string); $i++) {
		//     $char = substr($string, $i, 1);
		//     $keychar = substr($key, ($i % strlen($key))-1, 1);
		//     $char = chr(ord($char) - ord($keychar));
		//     $result .= $char;
		// }
		// return $result;
    }


}