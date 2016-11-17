<?php
class XSmarty {

	private 		$session ;
    protected 		$view_vars        	= array ( ) ;   	// variaveis que serão disponiveis na camada view
	public static 	$DISPLAY_ID 		= 1 ;				// ID único para cada ->display() de uma instância
    protected       $permission         = array ( );        // usada para validar permissões de acesso da RPC
    
	/**
	 *	executes and return the template result
	 *	you can assign the html content into a variable
	*/
	public function getDisplay ($resource_name, $cache_id = null, $compile_id = null) {

		// poderia ser feito simplesmente assim:
		// $result = $this->fetch ( $resource_name , $cache_id , $compile_id ) ;
		// mas não é feito assim porque não sabemos se usaremos o smarty como compilador de template ou não
		
		ob_start();
			$this->display($resource_name, $cache_id, $compile_id );
			$result = ob_get_contents();
		ob_end_clean();
		return $result ;
        
    }

    public function setPermission ( $requestedPermission , $tipoAcao = null , $permissoes = null ) {
        $this->permission = array (
            'permission'        => $requestedPermission ,
            'tipoAcao'          => $tipoAcao ,
            'permissoes'        => $permissoes
        ) ;
        return $this ;
    }
    
    /**
	 * RPC Specific
	 * verifica se o usuário tem persmissão para acessar esta página
	*/
	public function hasPermission ( $requestedPermission = null , $tipoAcao = null , $permissoes = null ) {
        
        try {
            
            if ( is_array ( $requestedPermission ) && !empty ( $requestedPermission['permission'] ) ) {
                $tipoAcao               = $requestedPermission['tipoAcao'];
                $permissoes             = $requestedPermission['permissoes'];
                $requestedPermission    = $requestedPermission['permission'];
            } else if ( $requestedPermission === null ) {
                $tipoAcao               = $this->permission['tipoAcao'];
                $permissoes             = $this->permission['permissoes'];
                $requestedPermission    = $this->permission['permission'];
            }

            if ( $tipoAcao === null )   $tipoAcao = Permissoes::TIPO_ACAO;
            if ( $permissoes === null ) $permissoes = Permissoes::CONSULTAR;

            if ( Permissoes::hasPermission ( $requestedPermission , $tipoAcao , $permissoes ) === "S" ) {
                return true ;
            } else {
                return false ;
            }
            
        } catch ( Exception $err ) {
            if ( DEBUG ) throw new Exception ( "Erro na definição de permissão de acesso: " . $err->getMessage( ) ) ;
            throw new Exception ( "Erro na definição de permissão de acesso" ) ;
        }

	}
    
    /**
     * 
     */
    public function validaSessaoRPC ( ) {
        
        if ( empty ( $_SESSION['USUARIO'] ) ) {
            if ( $_SERVER['SERVER_NAME'] === 'manila.intranet' ) {
                die ( "<script>\n // POR FAVOR FAÇA LOGIN NO SISTEMA\n\nwindow.location.href = \"http://manila.intranet/novaintranet/modulos/login/index.php\";\n </script> " ) ;
        //        header ( "Location: http://manila.intranet/novaintranet/modulos/login/index.php" ) ;
            } else {
                die ( "<script>\n // POR FAVOR FAÇA LOGIN NO SISTEMA\n\nwindow.location.href = \"http://intranetrpc.intranet/modulos/login/index.php\";\n </script> " ) ;
        //        header ( "Location: http://intranetrpc.intranet/modulos/login/index.php" ) ;
            }
        }
        
    }    

	/**
	 * @override
	 *
	 * */
	public function display( $template = "" , $enableUserMessages = true ){

		$this->assign ( 'DISPLAY_ID' 	, Smarty2::$DISPLAY_ID++ ) ;
        
		if ( $template == "" ) {
            $template =  'tpl/' . basename ( basename ( $_SERVER['SCRIPT_FILENAME'] ) , ".php" ) . ".phtml" ;
		}

		try {
            $this->__display ( $template ) ;
		} catch ( Exception $err ) {
			pr ( $template , __file__ , __line__ , 'DIE' ); // DEBUG
		}


	}

    /**
     * realy display content on the screen making available just the vars we had assigned
    */
    private function __display ( $view ) {
        foreach ( $this->view_vars as $key => $value ) {
            $$key = $value ;
        }
        require $view ;
    }

	/**
     * to give user feedback messages
     * @return string
     */
	public static function getUserMessage ( ) {
		if ( $this->session->hasMessage() ) {
			$error_messages = XSmarty::getErrorMessagesHTML();
		} else {
			$error_messages = "" ;
		}
		return $error_messages ;
	}

	/**
	 * get all error messages stored in session and generate html code
	 * */
	public static function getErrorMessagesHTML ( ) {
		$html = '';

		if ( $this->hasMessage ( ) ) {
			while ( $errorMessage = $this->getMessage ( ) ) {
				$msg = str_replace ( "'" , '' , $errorMessage['message'] ) ;
				$msg = str_replace ( "\n" , '<br>' , $msg ) ;
				$html .= "
					<script>
						alert ( '" . $msg . "' , '" . $errorMessage['type'] . "' ) ;
					</script>
				" ;
			}
		}

		return $html ;
	}

	/**
	 * check if has messages for send to the user
	 * */
	public static function hasMessage ( ){
		return !empty ( $_SESSION['session.messages'] ) ;
	}

	/**
	 * return one array of one message and remove it from session
	 * @$message_key = message key, optional, if not given, first message will be returned
	 * */
	public static function getMessage ( $message_key = null ) {
        // precisa ser melhorado
		if ( empty ( $_SESSION['session.messages'] ) ) return false ;

		if ( $message_key === null ) {
			return array_shift($_SESSION['session.messages']) ;
		}

		$requested_message = $_SESSION['session.messages'][$message_key] ;
		unset($_SESSION['session.messages'][$message_key]);
		return $requested_message;
	}
    
    
    public static function pushMessage ( $message , $type ) {
        // precisa ser implementado
    }

	/**
     * Guarda uma variavel para disponibilizar na view
     * ou assina para o smarty de acordo com o templeteEngine utilizado
    */
    public function assign ( $label , $value ) {
        $this->view_vars[$label] = $value ;
    }

}
