<?php

class Xlib_Request {

    public static function get ( $key , $default = "" ) {
        if ( strpos ( $key , "[" ) ) return Xlib_Request::getArrayRecursiveKey ( $key , $_REQUEST , $default ) ;
        if ( isset ( $_REQUEST[$key] ) ) return Xlib_Request::basicFilter ( $_REQUEST[$key] ) ;
        return $default ;
    }

    public static function getArrayRecursiveKey (  $key , $REQUEST , $default = "") {
        $value      = $REQUEST;
        $key        = str_replace( "]" , "" , $key );
        $keyList    = explode ( "[" , $key );
        foreach ( $keyList as $currKey ) {
            if ( !isset ( $value[$currKey] ) ) return $default ;
            $value = $value[$currKey];
        }
        return $value ;
    }

    /**
     * recebe um array de keys e retorna true se AO MENOS UMA delas estiver setada
     * @param array $keyList lista de chaves para buscar no request
     * @return boolean
     */
    public static function isSetOneOf ( Array $keyList ) {
        foreach ( $keyList as $key ) {
            $val = Xlib_Request::get($key) ;
            if ( !empty ( $val ) ) return true ;
        }
        return false ;
    }

    /**
     * recebe um array de keys e retorna true se TODAS delas estiverem setadas
     * @param array $keyList lista de chaves para buscar no request
     * @return boolean
     */
    public static function isSetAllOf ( Array $keyList ) {
        foreach ( $keyList as $key ) {
            $val = Xlib_Request::get($key);
            if ( empty ( $val ) ) return false ;
        }
        return true ;
    }

    public static function getDate ( $key ) {
    	if ( !preg_match ( '/^\d\d\/\d\d\/\d\d\d\d$/' , $_REQUEST[$key] ) ) return false ;
    	return $_REQUEST[$key];
    }

    public static function getInt ( $key , $default = 0 ) {
        return (int) preg_replace ( '/[^\d]+/' , '' , Xlib_Request::get($key,$default) ) ;
    }

    public static function getPost ( $key , $default = "" ) {
        if ( strpos ( $key , "[" ) ) return Xlib_Request::getArrayRecursiveKey ( $key , $_POST , $default ) ;
        if ( isset ( $_POST[$key] ) ) return Xlib_Request::basicFilter ( $_POST[$key] ) ;
        return $default ;
    }

    public static function getGet ( $key , $default = "" ) {
        if ( isset ( $_GET[$key] ) ) return Xlib_Request::basicFilter ( $_GET[$key] ) ;
        return $default ;
    }

    public static function set ( $key , $value ) {
        $_REQUEST[$key] = $value ;
    }

    public static function setPost ( $key , $value ) {
        $_POST[$key] = $value ;
    }

    public static function setGet ( $key , $value ) {
        $_GET[$key] = $value ;
    }

    public static function isPost ( ) {
        return !empty ( $_POST );
    }

    public static function isGet ( ) {
        return !empty ( $_GET );
    }

    public static function getURI ( ) {
    	$URI = "//";
    	$URI .= $_SERVER['HTTP_HOST'] ;
    	$URI .= $_SERVER['REQUEST_URI'] ;
    	return $URI ;
    }

    /**
     * adiciona uma mensagem de feedback para ser capturada na view
     * @param string $feedback  mensagem (text|html) de feedback
     * @param string $type      success|info|warning|danger que é respectivamente verde|azul|amarelo|vermelho
     * @param string|boolean    $icon TRUE=usa icone default do $type. FALSE=não usa icone. STRING=usa um icone qualquer do twitter bootstrap
     * @param string $nameSpace namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     */
	public static function addFeedback ( $feedback , $type = "info" , $icon = true , $nameSpace = "defaultNameSpace" ) {

        $feedback = translateError ( $feedback ) ;

        $typeToIcon = array (
            "success"   => "glyphicon-ok-sign" ,
            "info"      => "info-sign",
            "warning"   => "warning-sign" ,
            "danger"    => "exclamation-sign"
        ) ;
        if ( $icon === true ) $icon = $typeToIcon[$type];

		$_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace][] = array (
			'message'	=> $feedback ,
			'type'		=> $type ,
            'icon'      => $icon
		) ;

	}

    /**
     * recupera todas as mensagens de feedback para exibir na view e em seguida, apaga da sessão para evitar que se exiba novamente
     * @param type $nameSpace   namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     * @return string
     */
	public static function getFeedback ( $nameSpace = "defaultNameSpace" ) {

        if ( empty ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace] ) ) return "";
        $output = "";

        foreach ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace] as $key => $feedback ) {
            $icon = ( $feedback['icon'] !== false ) ? "<span class=\"glyphicon " . $feedback['icon'] . "\"></span> " : "" ;
            $output .= "<div class=\"alert alert-" . $feedback['type'] . "\">" ;
            $output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>";
            $output .= "$icon" . $feedback['message'] . "</div>" ;
            unset ( $_SESSION['USER-FEEDBACK-MESSAGES'][$nameSpace][$key] ) ;
        }

        return $output ;

	}

    public static function basicFilter ( $data ) {
        if ( gettype ( $data ) === 'string' ) {
            return trim($data);
        }
        return $data ;
    }

    public static function getFiles ( ) {
        if ( empty ( $_FILES ) ) return false ;
        return $_FILES ;
    }

    /**
     *
     * @param type $key
     * @return boolean
     */
    public static function hasFile ( $key ) {
        if ( empty ( $_FILES[$key] ) ) return false ;
        return true ;
    }

    /**
     *
     * @param type $key
     * @return boolean
     * @throws Exception
     */
    public static function getFile ( $key ) {

        if ( empty ( $_FILES[$key] ) ) return false ;

        switch ( $_FILES[$key]['error'] ) {
            case UPLOAD_ERR_OK          : break ; // 0
            case UPLOAD_ERR_INI_SIZE    : throw new Exception ( "Tamanho de arquivo ultrapassa o limite de " . ini_get( 'upload_max_filesize' ) ) ; // 1
            case UPLOAD_ERR_FORM_SIZE   : throw new Exception ( "Tamanho de arquivo ultrapassa o limite permitido" ) ; // 2
            case UPLOAD_ERR_PARTIAL     : throw new Exception ( "O upload foi feito parcialmente" ) ; // 3
            case UPLOAD_ERR_NO_FILE     : throw new Exception ( "Não foi feito o upload do arquivo, favor tentar novamente" ) ; //4
            case UPLOAD_ERR_NO_TMP_DIR  : throw new Exception ( "O servidor não possui um diretório temporário" ) ; //5
            case UPLOAD_ERR_CANT_WRITE  : throw new Exception ( "Erro ao escrever no disco" ) ; //6
            case UPLOAD_ERR_EXTENSION   : throw new Exception ( "Uma extensão do PHP parou o upload do arquivo" ) ; //7
            default                     : throw new Exception ( "Erro de upload não identificado" ) ; //8
        }

        return $_FILES[$key] ;

    }

}