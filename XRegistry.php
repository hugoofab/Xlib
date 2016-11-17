<?php
class XRegistry {

	public static $namespace = 'SUPERGLOBALS' ;
	private static $registryArray = array ();

    public static function set ( $key , $value ) {
    	XRegistry::$registryArray[XRegistry::$namespace][$key] = $value ;
    }

    public static function get ( $key ) {
    	return XRegistry::$registryArray[XRegistry::$namespace][$key];
    }

}