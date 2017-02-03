<?php

// =============================================================================
// = DEBUGGING AND ERROR TREATMENT =============================================

define ( 'XLIB_DIR' , dirname ( __file__ ) ) ;

require_once ( XLIB_DIR . "/ModelAbstract.php" );

if ( strpos ( $_SERVER['HTTP_HOST'], "manila.intranet" ) > -1 ) {
    define ( 'XLIB_ASSETS' , "/novaintranet/includes/Xlib/components" ) ;
    define ( 'ENVIRONMENT' , 'development' ) ;
} else {
    define ( 'XLIB_ASSETS' , "/includes/Xlib/components" ) ;
    define ( 'ENVIRONMENT' , 'production' ) ;
}

if ( !defined ( 'S_SKIN_RELATIVO' ) )  define ( 'S_SKIN_RELATIVO' , '../../skins/atendimento_9' ) ;
if ( !defined ( 'DEBUG' ) )            define ( 'DEBUG' , strpos ( $_SERVER['HTTP_DEBUG'] , 'DEBUG_MODE' ) ? TRUE : FALSE ) ;
if ( !defined ( 'SHOW_SQL_QUERIES' ) ) define ( 'SHOW_SQL_QUERIES' , strpos ( $_SERVER['HTTP_SHOW_SQL_QUERIES'] , 'SHOW_SQL_QUERIESasd62fa6sdf6a51sdf65a1sdf65a1sdf' ) ? TRUE : FALSE ) ;

if ( !function_exists ( "redirect" ) ) {
    function redirect ( $url ) {
        header ( "Location:$url") ;
        echo "<script>\n window.location.href = \"$url\" ;\n</script>";
        exit ;
    }
}

if ( !function_exists ( "pr" ) ) {

	// pr die
	function prd ( ) {
		if ( !defined ( 'DEBUG' ) || DEBUG === false ) return ;
    	$backTrace = debug_backtrace ();
		$varList   = func_get_args ( );
		_pr ( $varList , "#0F0" , "#000" , $backTrace ) ;
		exit;
	}

	// pr error
    function pre ( ) {
    	$backTrace = debug_backtrace ();
		$varList   = func_get_args ( );
		_pr ( $varList , "#FFF" , "#8B0000" ,  $backTrace ) ;
    }

    // pr success
    function prs ( ) {
    	$backTrace = debug_backtrace ();
		$varList   = func_get_args ( );
		_pr ( $varList , "#FFF" , "#005F08" , $backTrace ) ;
    }

    function pr ( ) {
		$varList   = func_get_args ( );
		$backTrace = debug_backtrace ();
    	_pr ( $varList , "#0F0" , "#000" , $backTrace );
    }

    function _pr ( $varList = "" , $foreground = "#0F0" , $background = "#000" , $backTrace = false ) {
	    
	    if ( !defined ( 'DEBUG' ) || DEBUG === false ) return ;
	    if ( $backTrace === false ) $backTrace = debug_backtrace ();
	    $options = array(
		'File' => $backTrace[0]['file'] ,
		'Line' => $backTrace[0]['line']
	    );
	    $file = $options['File'];
	    $line = $options['Line'];
	    $id = uniqid();// md5 ( print_r ( $varList , true ) . rand ( 0 , 100 ) ) ;
	    if ( !empty ( $varList ) ) {
		echo "<pre id=\"$id\" class='hf_debug' style=\"font-size:12px;line-height:1em;background:${background};color:${foreground};position:relative;z-index:99999;filter:alpha(opacity=80); -moz-opacity:0.80; opacity:0.80;font-family:courier new;white-space: pre-wrap;margin:0;margin-bottom:10px;\">\n";
		foreach ( $varList as $var ) {
		    echo _getVarDetails($var);
		}
	    } else {
		echo "<pre id=\"$id\" class='hf_debug' style=\"font-size:12px;line-height:1em;background:${background};color:${foreground};position:relative;z-index:99999;filter:alpha(opacity=80); -moz-opacity:0.80; opacity:0.80;font-family:courier new;white-space: pre-wrap;margin:0;margin-bottom:10px;\">\n" ;
	    }
	    array_shift ( $backTrace ) ;
	    $backTrace = array_reverse ( $backTrace );
	    foreach( $backTrace as $key => $bt ) {
		$outArg = [];
		foreach ( $bt['args'] as &$arg ) {
		    if ( gettype ( $arg ) === 'object' ) {
			$outArg[] = "Object:" . get_class($arg) ;
		    } else if ( gettype ( $arg ) === 'boolean' ) {
			$outArg[] = $arg ? "Bool:TRUE":"Bool:FALSE";
		    } else {
			$outArg[] = ucwords(gettype($arg)).":".$arg ;
		    }
		}
		$implode = @implode ( "] , [" , $outArg ) ;
		$function = $bt['function'] . " ( [" . $implode . "] ) " ;
		echo "\n<span style=\"margin-top:3px;padding-left:4px;background:#070;color:#000;font-weight:bold;\">" . $bt['file'] . ":" . $bt['line'] . "&nbsp;</span> -&gt;" . $function ;
	    }
	    echo "\n<span style=\"margin-bottom:10px;padding-left:4px;background:#0F0;color:#000;font-weight:bold;line-height:1.5em;\">" . $file . ":" . $line . "&nbsp;&nbsp;&nbsp;<a style=\"color:#FFF;background:#000;padding-left:5px;\" onclick=\"document.getElementById('$id').innerHTML=''\" href=\"javascript:;\">fechar este &nbsp;&nbsp;</a><a onclick=\"$('.hf_debug').hide()\" style=\"color:#FFF;background:#000;padding-left:5px;\" href=\"javascript:;\">fechar todos</a></span></pre>" ;
	}

	function _getVarDetails ( $mixedVar ) {
	    $output = "Type: " . gettype ( $mixedVar ) . "\n" ;
	    if ( gettype ( $mixedVar ) == 'boolean' ) {
		$output .= ( $mixedVar ) ? "TRUE" : "FALSE" ;
	    } else {
		$output .= print_r ( $mixedVar , true );
	    }
	    $output .= "<hr>";
	    return $output ;
	}

}

function obj2array ( &$Instance ) {
    $clone = (array) $Instance;
    $rtn = array ();
    $rtn['___SOURCE_KEYS_'] = $clone;

    while ( list ($key, $value) = each ($clone) ) {
        $aux = explode ("\0", $key);
        $newkey = $aux[count($aux)-1];
        $rtn[$newkey] = &$rtn['___SOURCE_KEYS_'][$key];
    }

    return $rtn;
}

function translateError ( $errorMessage ) {

    // errorBase.php return a array with key=>value pair where key is the original error and value is translated error
    $errorBase = include ( "errorBase.php" );

    if ( !array_key_exists ( $errorMessage , $errorBase ) ) return $errorMessage ;

    $output = $errorBase[$errorMessage];
    if ( DEBUG ) $output .= "<br>[" . $errorMessage . "]" ;

    return $output ;

}

/**
 * Tratamento de erro padrão
 * aqui pode ser definido entre mostrar uma mensagem de erro ou redirecionar para uma página de erro
 * procure logar o erro nesse momento
 * */
function defaultExceptionHandler ( $exception ) {

    $errorData = array (
        'message'   => $exception->getMessage ( ) ,
        'file'      => $exception->getFile ( ) ,
        'line'      => $exception->getLine ( ) ,
        'code'      => $exception->getCode ( )
    ) ;

    Request::getFeedback();

    debugErrorHandler ( "Exception: " . $errorData['code'] , $errorData['message'] , $errorData['file'] , $errorData['line'] ) ;

    // o php morre automaticamente aqui

}

function myErrorHandler ( $errno , $errstr , $errfile , $errline ) {
    $verboseMode = strpos ( $_SERVER['HTTP_USER_AGENT'] , 'DEBUG_MODE_8f40861230f65284d6f2058249344c00_VERBOSE' );

    if ( !$verboseMode ) {
        if ( !( error_reporting ( ) & $errno ) ) {
            // This error code is not included in error_reporting
            return ;
        }
    }

    debugErrorHandler ( "Error: " . $errno , $errstr , $errfile , $errline ) ;
    // acho que deve executar uma ou outra
    Request::addFeedback ( $errstr , 'danger' ) ;

    return true;

}

function debugErrorHandler ( $errno , $errstr , $errfile , $errline ) {

    $errstr         = translateError ( $errstr );
    $backTrace      = debug_backtrace ( ) ;
    $bt             = '';

    if ( DEBUG ) {
        array_shift($backTrace);
        $backTrace = array_reverse ( $backTrace );
        foreach ( $backTrace as $key => $trace ) {
            if ( !$trace['file'] ) continue ;
            $bt = "[" . ($key+1) . "]" . $trace['file'] . ":" . $trace['line'] . "<br>" . $bt ;
        }
        if ( $bt !== '' ) $bt ='<hr><div style="color:#000;">' . $bt . "</div><br>" ;
    }

    $message =
    '<div style="min-width:500px;background:#FFFAFA;padding:10px;min-height:100px;text-align:center;margin-left:auto;margin-right:auto;text-align:center;color:#F00;font-family:verdana;font-size:12px;margin:10px;">' .
        '<div style="display:block;text-align:left;">' .
            '<div style="font-family:courier new;overflow:auto;border:2px solid #D00;border-radius:10px;font-size:1.3em;color:#D00;padding:10px;"> <span style="font-size:2em;" class="glyphicon glyphicon-warning-sign"></span> ' . $errstr .
            $bt .
            '</div>' .
            //$errfile . ':' . $errline . '<br>' .
        '</div>' .
    '</div>'
    ;

    echo $message ;

}

/**
 * @TODO: salvar os caminhos encontrados na sessão caso um dia a verificação dos arquivos em disco representem problema de desempenho
 * @param  [type] $className [description]
 * @return [type]            [description]
 */
function findClassInPath ( $className ) {

	$pathList = explode ( PATH_SEPARATOR , get_include_path ( ) ) ;
	$dump = 'Class <strong>' . $className . "</strong>\n";

	foreach ( $pathList as $path ) {
		$filename =	$path . DIRECTORY_SEPARATOR . str_replace ( '_' , DIRECTORY_SEPARATOR , $className ) . ".php";
		$filename = str_replace( "//", "/", $filename);
    	$realFilename = realpath ( $filename ) ;
	    if ( file_exists ( $realFilename ) ) {
    		return $realFilename ;
    	} else {
    		$dump .= "not found: $filename\n";
    	}
	}

	pr($dump);
	return $className . ".php";

}

function autoload ( $className ) {

    if ( $className === 'MDB2_Driver_Datatype_oci8' ) return ;

    $fileName = findClassInPath ( $className );

    require_once $fileName ;

}

class parandaricotirimihuaro {
    public function __destruct ( ) {
        echo "<div style=\"border:2px dashed #000;z-index:9999999;transform: rotate(90deg);color:#FFF;height:23px;width:130px;font-size:15px;text-align:center;position:fixed;top:100px;left:-56px;\" onclick=\"this.style.display='none'\"><div style=\"opacity: 0.4;background:#000;\">".ENVIRONMENT."</div></div>";
    }
}
// if ( ENVIRONMENT !== 'production' ) new parandaricotirimihuaro ( );;

set_error_handler ( "myErrorHandler" );
set_exception_handler( 'defaultExceptionHandler' ) ;
spl_autoload_register ( 'autoload' ) ;
date_default_timezone_set ( 'America/Sao_Paulo' );
