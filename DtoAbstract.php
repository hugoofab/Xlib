<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author hugo.ferreira
 */
abstract class DtoAbstract {

    /**
	 * caso tenha passado um array de chave => valor com chaves identicas aos atributos declarados no objeto filho
	 * vamos setar usando o metodo set se ele existir
	 * os nomes dos atributos devem ser todos em minusculo
	 *
	*/
	final public function __construct ( $arrayData = null ) {

		$this->reset();

		if ( !empty ( $arrayData ) ) $this->loadFromArray( $arrayData )		;

		$this->init ( );

	}

	public function loadFromArray ( $arrayData = array ( ) ) {

		if ( !empty ( $arrayData ) && is_array ( $arrayData ) ) {
			foreach ( $arrayData as $key => $val ) {
				$method = "set" . ucwords ( strToLower ( $key ) ) ;
				if ( method_exists ( $this , $method ) ) {
					$this->{$method}( $val ) ;
				}
			}
		}

	}

    /**
     * precisa ser sobrescrito na classe filha
     */
    abstract public function isValid ( ) ;

    /**
     * inicializa as classes concretas
     * @return [type] [description]
     */
    abstract public function init ( );

    /**
     * reconfigura todos os parâmetros
     * @return [type] [description]
     */
    abstract public function reset ( );

	/**
	 * retorna true se todos os atributos deste objeto estiverem vazios
	 * retorna false se ao menos um atributo contiver algum valor
	*/
	public function isEmpty ( ) {
		$atributos = get_object_vars ( $this ) ;
		foreach ( $atributos as $key => $val ) {
			if ( !empty ( $val ) ) {
				return false ;
			}
		}
		return true ;
	}

	/**
	 * retorna true se todos os atributos deste objeto estiverem preenchidos
	 * retorna false se algum atributo não tiver valor algum atribuido
	*/
	public function isFull ( ) {
		$atributos = get_object_vars ( $this ) ;
		foreach ( $atributos as $key => $val ) {
			if ( empty ( $val ) ) {
				return false ;
			}
		}
		return true ;
	}

	/**
	 * retorna o proprio objeto convertido em array
	*/
	public function toArray ( ) {
		return (array) $this ;
	}

}