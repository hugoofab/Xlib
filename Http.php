<?php
/**
 * PARA USAR COM SERVLETS, E COMUNICAÇÃO SERVER TO SERVER WEBSERVICE ETC
 */

class Http {

    protected $baseUrl  = '';
    protected $port = '';

    protected $connectionTimeOut = 30 ;
    protected $timeOut = 40 ;
    
    protected $headers = array ( 'Content-Type: application/json' ) ;
    
    protected $urlHistory = array ( );
    
    /**
     * 
     * @param string $baseUrl 
     * @param integer $port
     */
    public function __construct ( $baseUrl = "" , $port = "" ) {
        if ( !empty ( $baseUrl ) ) $this->baseUrl = preg_replace ( '/\/$/' , '' , $baseUrl ) ;
        if ( !empty ( $port ) ) $this->port = $port ;
    }
    
    /**
     * Cria a URL baseada em uma URL base ($this->baseUrl) se houver e porta padrão ($this->port) se houver
     * @param string $url
     * @return string
     * @throws Exception
     */
    protected function makeUrl ( $url ) {
        
        if ( !empty ( $this->baseUrl ) && !empty ( $this->port ) ) {
            // temos a baseUrl e a porta
            $url = $this->baseUrl . ":" . $this->port . DIRECTORY_SEPARATOR . preg_replace ( '/^\//' , '' , $url ) ;
        } else if ( !empty ( $this->url ) && empty ( $this->port ) ) {
            // temos a baseUrl mas não temos a porta
            $url = $this->baseUrl . DIRECTORY_SEPARATOR . preg_replace ( '/^\//' , '' , $url ) ;
        } else if ( empty ( $this->url ) && !empty ( $this->port ) ) {
            // temos a porta e não temos a baseUrl
            throw new Exception ( "baseUrl indefinida" ) ;
        } 
        // não temos nem a baseUrl nem a porta

        $this->urlHistory[] = $url ;
        return $url ;
        
    }
    

    /**
     * Chama uma servlet e devolve uma string
     * @param string $url
     * @param string $post string para postar Ex.: var1=val1&var2=val2
     * @return string
     * @throws Exception
     */
    public function getAsString ( $url , HttpPost $post = null ) {
        
        $ch = curl_init ( $this->makeUrl ( $url ) ) ;
        
        if ( !empty ( $post ) ) {
            curl_setopt ( $ch , CURLOPT_POST , true ) ;
            curl_setopt ( $ch , CURLOPT_POSTFIELDS , $post->get() ) ;
        }
        
        curl_setopt ( $ch , CURLOPT_MUTE , 1 ) ;
        curl_setopt ( $ch , CURLOPT_SSL_VERIFYHOST , 0 ) ;
        curl_setopt ( $ch , CURLOPT_SSL_VERIFYPEER , 0 ) ;
        curl_setopt ( $ch , CURLOPT_HTTPHEADER , $this->headers ) ;
        curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 ) ;

        curl_setopt ( $ch , CURLOPT_CONNECTTIMEOUT , $this->connectionTimeOut ) ;
        curl_setopt ( $ch , CURLOPT_TIMEOUT , $this->timeOut ) ;

        
        $result = curl_exec ( $ch ) ;

        if ( curl_errno ( $ch ) ) throw new Exception ( curl_error ( $ch ) , curl_errno ( $ch ) ) ;

        curl_close ( $ch ) ;

        return $result ;
        
    }
    
    /**
     * Chama uma servlet e devolve o objeto
     * @param type $url
     * @return type
     */
    public function get ( $url ) {
        return Json::decode ( $this->getAsString ( $url ) );
    }
    
    public function __destruct ( ) {
//        pr ( $this->urlHistory );
    }
    
}