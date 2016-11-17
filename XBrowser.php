<?php

/**
 * XBrowser is a virtual browser class that simulates a web browser
 *
 * CURLOPT_HTTPHEADER vars
 * ------------------------------------------------------------------------------------
 * para configurar algum utilize:
 * 		$XBrowser->setHeader ( 'Accept-Language' , 'en-US' )
 *   	$XBrowser->setHeader ( 'Cookie' , 'PHPSESSID=737060cd8c284d8af7ad3082f209582d&foo=bar')
 * 'Accept: text/plain'	                                                                // Content-Types that are acceptable for the response. See Content negotiation.
 * 'Accept: text/html'
 * 'Accept-Charset: utf-8'	                                                            // Character sets that are acceptable
 * 'Accept-Encoding: gzip, deflate'	                                                    // List of acceptable encodings. See HTTP compression.
 * 'Accept-Language: en-US'	                                                            // List of acceptable human languages for response. See Content negotiation.
 * 'Accept-Datetime: Thu, 31 May 2007 20:35:00 GMT'	                                    // Acceptable version in time
 * 'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ=='	                                // Authentication credentials for HTTP authentication
 * 'Cache-Control: no-cache'	                                                        // Used to specify directives that must be obeyed by all caching mechanisms along the request-response chain
 * 'Connection: keep-alive'	                                                            // What type of connection the user-agent would prefer
 * 'Cookie: $Version=1; Skin=new;'	                                                    // An HTTP cookie previously sent by the server with Set-Cookie (below)
 * 'Content-Length: 348'	                                                            // The length of the request body in octets (8-bit bytes)
 * 'Content-MD5: Q2hlY2sgSW50ZWdyaXR5IQ=='	                                            // A Base64-encoded binary MD5 sum of the content of the request body
 * 'Content-Type: application/x-www-form-urlencoded'	                                // The MIME type of the body of the request (used with POST and PUT requests)
 * 'Date: Tue, 15 Nov 1994 08:12:31 GMT'	                                            // The date and time that the message was sent (in "HTTP-date" format as defined by RFC 7231)
 * 'Expect: 100-continue'	                                                            // Indicates that particular server behaviors are required by the client
 * 'From: user@example.com'	                                                            // The email address of the user making the request
 * 'Host: en.wikipedia.org:80 '	                                                        // The domain name of the server (for virtual hosting), and the TCP port number on which the server is listening. The port number may be omitted if the port is the standard port for the service requested. [8] Mandatory since HTTP/1.1.
 * 'If-Match: "737060cd8c284d8af7ad3082f209582d"'	                                    // Only perform the action if the client supplied entity matches the same entity on the server. This is mainly for methods like PUT to only update a resource if it has not been modified since the user last updated it.
 * 'If-Modified-Since: Sat, 29 Oct 1994 19:43:31 GMT'	                                // Allows a 304 Not Modified to be returned if content is unchanged
 * 'If-None-Match: "737060cd8c284d8af7ad3082f209582d"'	                                // Allows a 304 Not Modified to be returned if content is unchanged, see HTTP ETag
 * 'If-Range: "737060cd8c284d8af7ad3082f209582d"'	                                    // If the entity is unchanged, send me the part(s) that I am missing; otherwise, send me the entire new entity
 * 'If-Unmodified-Since: Sat, 29 Oct 1994 19:43:31 GMT'	                                // Only send the response if the entity has not been modified since a specific time.
 * 'Max-Forwards: 10'	                                                                // Limit the number of times the message can be forwarded through proxies or gateways.
 * 'Origin: http://www.example-social-network.com'	                                    // Initiates a request for cross-origin resource sharing (asks server for an 'Access-Control-Allow-Origin' response field) .
 * 'Pragma: no-cache'	                                                                // Implementation-specific fields that may have various effects anywhere along the request-response chain.
 * 'Proxy-Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ=='	                        // Authorization credentials for connecting to a proxy.
 * 'Range: bytes=500-999'	                                                            // Request only part of an entity. Bytes are numbered from 0. See Byte serving.
 * 'Referer: http://en.wikipedia.org/wiki/Main_Page'	                                // This is the address of the previous web page from which a link to the currently requested page was followed. (The word “referrer” has been misspelled in the RFC as well as in most implementations to the point that it has become standard usage and is considered correct terminology)
 * 'TE: trailers, deflate'	                                                            // The transfer encodings the user agent is willing to accept: the same values as for the response header field Transfer-Encoding can be used, plus the "trailers" value (related to the "chunked" transfer method) to notify the server it expects to receive additional fields in the trailer after the last, zero-sized, chunk.
 * 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/21.0'	// The user agent string of the user agent
 * 'Upgrade: HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11'	                                    // Ask the server to upgrade to another protocol.
 * 'Via: 1.0 fred, 1.1 example.com (Apache/1.1)'	                                    // Informs the server of proxies through which the request was sent.
 * 'Warning: 199 Miscellaneous warning'	                                                // A general warning about possible problems with the entity body.
 *
 *
 * @author hugo.ferreira
 */
class XBrowser {

	protected $url ;

	/**
	 * usado para enviar parâmetros via GET
	 * @var array
	 */
	protected $getVars = array ( );

	/**
	 * usado para enviar parâmetros via POST
	 * @var array
	 */
	protected $postVars = array ( );

	/**
	 * usado para upload de arquivos, não implementado ainda
	 */
	// protected $fileVars = array ( );

	/**
	 * CURL OPTS para configurar o nosso virtual Browser
	 * @var array
	 */
	protected $headers = array (

		CURLOPT_SSL_VERIFYHOST  => 0 ,
		CURLOPT_SSL_VERIFYPEER  => 0 ,
		CURLOPT_CONNECTTIMEOUT  => 5 ,
		CURLOPT_RETURNTRANSFER  => 1 ,

		CURLOPT_CONNECTTIMEOUT  => 60 ,
		CURLOPT_TIMEOUT         => 60

		// CURLOPT_HTTPHEADER => array ( 'Content-Type: application/json' ) ,
		// CURLOPT_URL
		// CURLOPT_FAILONERROR
		// CURLOPT_SSL_VERIFYPEER
		// CURLOPT_SSL_VERIFYHOST
		// CURLOPT_CAINFO
		// CURLOPT_SSLVERSION
		// CURLOPT_CONNECTTIMEOUT
		// CURLOPT_CONNECTTIMEOUT
		// CURLOPT_TIMEOUT
		// CURLOPT_TIMEOUT
		// CURLOPT_RETURNTRANSFER
		// CURLOPT_POST
		// CURLOPT_POSTFIELDS

	);

	public function __construct ( $url = "" ) {
		$this->url = $url ;
	}

	public static function getInstance ( $url ) {
		return new XBrowser ( $url );
	}

	/**
	 * Adiciona ou sobrescreve um parâmetro padrão ao CURL
	 * ex.: $obj->setParam ( CURLOPT_TIMEOUT , 30 );
	 * @param CURL OPT $param CURL OPT (http://curl.haxx.se/libcurl/c/curl_easy_setopt.html)
	 * @param mixed $value valor a ser atribuído ao CURL OPT
	 */
	public function setParam ( $param , $value ) {
		$this->headers[$param] = $value ;
		return $this;
	}

	/**
	 * Adiciona um par de chave => valor ao post
	 * @param string $key   chave, nome da variável post
	 * @param string $value valor da variável post
	 */
	public function addPost ( $key , $value ) {
		$this->headers[CURLOPT_POST] = 1 ;
		$this->postVars[$key]        = $value ;
		return $this ;
	}

	/**
	 * Adiciona um header para ser enviado ao servidor
	 * Ex.:
	 * $XBrowser->addHeader ( "User-Agent" , 'Mozilla Firefox' );
	 * @param string $key   chave do header
	 * @param string $value valor do header
	 */
	public function addHeader ( $key , $value ) {
		$this->headers[CURLOPT_HTTPHEADER][] = "$key: $value" ;
		return $this ;
	}

	public function getUrl ( ) {
		return $this->url;
	}

	/**
	 * Envia a requisição
	 * @return string response do server
	 */
	public function send ( ) {

		$ch = curl_init ( $this->url ) ;

		foreach ( $this->headers as $key => $value ) curl_setopt ( $ch , $key , $value ) ;

		if ( !empty ( $this->postVars ) ) curl_setopt ( $ch , CURLOPT_POSTFIELDS , http_build_query ( $this->postVars ) ) ;

		$result = curl_exec ( $ch ) ;

        if ( curl_errno ( $ch ) ) throw new Exception ( curl_error ( $ch ) , curl_errno ( $ch ) ) ;

        curl_close ( $ch ) ;

        return $result ;

	}


}