<?php

class Xlib_Response {

    protected $status = 'OK';
    protected $message = '' ;
    protected $data = array ( );
    protected $outputType = 'json' ;

    public function setType ( $type ) {
        if ( !in_array ( $type , array ( 'json' ) ) ) throw new Exception ( "Tipo desconhecido" ) ;
        $this->outputType = $type ;
    }

    public function setError ( $error ) {

        if ( gettype ( $error ) === 'object' ) {
            if ( method_exists ( $error , 'getMessage' ) ) {
                $error = $error->getMessage ( );
            }
        }

        $this->status = 'ERROR' ;
        $this->message = $error ;

    }

    public function setData ( $data ) {
        $this->data = $data ;
    }

    public function __toString (  ) {

        $response = array (
            'STATUS'    => $this->status ,
            'MESSAGE'   => $this->message ,
            'DATA'      => $this->data
        ) ;

        switch ( $this->outputType ) {
            case "json" :  return Xlib_Json::encode ( $response ) ;
        }


    }

    /**
     * adiciona uma mensagem de feedback para ser capturada na view
     * @param string $feedback  mensagem (text|html) de feedback
     * @param string $type      success|info|warning|danger que é respectivamente verde|azul|amarelo|vermelho
     * @param string|boolean    $icon TRUE=usa icone default do $type. FALSE=não usa icone. STRING=usa um icone qualquer do twitter bootstrap
     * @param string $nameSpace namespace para dividir certas partes da aplicação que se encontra na mesma sessão
     */
	public static function addFeedback ( $feedback , $type = "info" , $icon = true , $nameSpace = "defaultNameSpace" ) {

		if ( $feedback instanceof Exception ) {
			$feedback = $feedback->getMessage();
			$type     = "danger";
		}

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
	
	public static function forceUserDownloadByFile ( $filename ) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
		echo readfile($file_url);
	}
	
	public static function forceUserDownloadByString ( $fileContent , $filename ) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . $filename . "\"");
		echo $fileContent;
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

}