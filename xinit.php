<?php

session_start();

require_once "../includes/Xlib/xinitLib.php";



$pathList = array (
	'.',
    XLIB_DIR . DIRECTORY_SEPARATOR ,
    dirname ( XLIB_DIR . DIRECTORY_SEPARATOR ),
    XLIB_DIR . "/XListaDados/" ,
    dirname ( dirname (__file__) ) . "/Model/" ,
    dirname ( dirname (__file__) ) . "/MDB2/"
);

set_include_path ( implode ( PATH_SEPARATOR , $pathList ) . PATH_SEPARATOR . get_include_path ( ) );

define ( 'BASE_URL'           , ( ENVIRONMENT === 'development' ) ? "/novaintranet/SistemaReembolso/" : "/" ) ;
define ( 'LOCAL_ASSETS'       , ( ENVIRONMENT === 'development' ) ? BASE_URL . "layout" : "layout" ) ;
define ( 'ROOT_DIR'           , dirname ( __file__ ) ) ;
define ( 'CONFIGURATION_FILE' , realpath(ROOT_DIR."/../modulosRecargaOnline/configs/application.ini") );

XListaDados::setDefaultTemplate ( "XListaDadosGray" )  ;



if ( !XAccessControl::userIsAuthenticated ( ) ) throw new Exception ( "Por favor fa&ccedil;a login no sistema" );

XRegistry::set ( "TP05" , ModelAbstract::connect ( require ( XLIB_DIR . "/connList/LOJAVIRTUAL_" . ENVIRONMENT . '.php' ) ) ) ;
XRegistry::set ( "TP04" , ModelAbstract::connect ( require ( XLIB_DIR . "/connList/INTRANETRPC_" . ENVIRONMENT . '.php' ) ) ) ;

