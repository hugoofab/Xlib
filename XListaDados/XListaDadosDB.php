<?php

class XListaDadosDB extends ModelAbstract {

	private $queryCount         = null ;
	private $queryOrder         = '';
	private $querySelect        = array ( ) ;
	private $queryFrom          = array ( ) ;
	private $queryWhere         = array ( ) ;
	private $queryAdicionais    = array ( ) ;
    private $queryGroup         = array ( ) ;
    private $queryJoin          = '';
    private $mainQuery 			= null ;
    private $queryResult 		= null ;

//	public function init ( ){
//
//	}

	public function setQueryCount($queryCount){
		$this->queryCount = $queryCount;
	}

	public function getQueryCount ( ) {
		return $this->queryCount ( ) ;
	}

    /**
	 * adiciona a cláusula ORDER BY
	 * para múltiplos order by, insira todos no $fieldToOrder, e ignore o $desc
	*/
	public function setQueryOrder ( $fieldToOrder , $desc = '' ) {
		$this->queryOrder = $fieldToOrder . ' ' . $desc ;
        return $this ;
	}
    public function order ( $fieldToOrder , $desc = '' ) {
        return $this->setQueryOrder ( $fieldToOrder , $desc = '' ) ;
    }

	public function getQueryOrder(){
		return $this->queryOrder;
	}

	/**
	 * monta um campo do select, com a opção de colocar um alias
	*/
	public function addQuerySelect ( $querySelect , $alias = false , $nvl = false ) {

		if ( $nvl ) $querySelect = "NVL ( " . $querySelect." , '" . $nvl . "' ) ";

		if ( !$alias ) {
			array_push ( $this->querySelect, $querySelect ) ;
		} else {
			array_push ( $this->querySelect, $querySelect . " AS \"" . $alias . "\"") ;
		}

        return $this ;

	}
    public function select ( $querySelect , $alias = false , $nvl = false ) { // $nvl = " "
        return $this->addQuerySelect( $querySelect , $alias , $nvl ) ;
    }

	/**
	 *	Adiciona uma cláusula FROM,
	 *	tudo o que for adicionado com a chamada deste metodo será adicionado na query separado por vírgula ',' (vírgula)
	*/
	public function addQueryFrom ( $queryFrom ) {
		array_push ( $this->queryFrom , $queryFrom ) ;
        return $this ;
	}
    public function from ( $queryFrom ) {
        return $this->addQueryFrom ( $queryFrom ) ;
    }

	public function addQueryWhere ( $queryWhere ) {
		array_push ( $this->queryWhere , $queryWhere ) ;
        return $this;
	}
    public function where ( $queryWhere ) {
        return $this->addQueryWhere ( $queryWhere ) ;
    }

    public function getWhereList ( ) {
    	return $this->queryWhere ;
    }

	public function addQueryGroup ( $queryGroup ) {
		array_push ( $this->queryGroup , $queryGroup ) ;
        return $this ;
	}
    public function group ( $queryGroup ) {
        return $this->addQueryGroup ( $queryGroup ) ;
    }


	/**
	 * coloca uma string adicional
	 * ainda não sei pra que serve isso...
	*/
	public function addQueryAdicionais ( $queryAdicionais ) {
		array_push ( $this->queryAdicionais , $queryAdicionais ) ;
	}

    public function join ( $queryJoin ) {
        $this->queryJoin .= "JOIN " . $queryJoin . " \n" ;
        return $this ;
    }
    public function innerJoin ( $queryJoin ) {
        $this->queryJoin .= "INNER JOIN " . $queryJoin . " \n" ;
        return $this ;
    }
    public function leftJoin ( $queryJoin ) {
        $this->queryJoin .= "LEFT JOIN " . $queryJoin . " \n" ;
        return $this ;
    }
    public function rightJoin ( $queryJoin ) {
        $this->queryJoin .= "RIGHT JOIN " . $queryJoin . " \n" ;
        return $this ;
    }

	/**
	 * retorna o numero total de linhas retornadas pela consulta
	*/
	public function queryCount ( ) {

        // se já foi feita a contagem antes para esta mesma query, retorne o valor anterior
		if ( $this->queryCount !== null ) return $this->queryCount ;

		// se não foi feita a contagem ainda, mas já pegamos todo o resultado da query,
		// retorne o numero de posições do array
		if ( $this->queryResult !== null ) {
			return sizeof ( $this->queryResult ) ;
		}

		// se não foi feita a contagem nem fizemos a consulta para buscar os dados, faça o count
		// e guarde para consultas futuras
		$query = "SELECT SUM(TOTAL) as '0' FROM ( SELECT COUNT(*) TOTAL FROM (" . $this->getQuery() . " )XYT76655 )YTAF965 ";

		$totalRegistros = $this->fetchOne($query);

		// if ( PEAR::isError ( $totalRegistros ) ) throw new Exception ( print_r ( $totalRegistros , true ) ) ;;

		$this->queryCount = $totalRegistros[0] ;

		return $totalRegistros[0];

	}


	/**
	 * constrói a query, armazena no atributo $this->mainQuery para consultas futuras e retorna a mesma
	*/
	public function getQuery ( ) {

		// se já executamos este método antes, não precisamos recalcular tudo novamente
		if ( $this->mainQuery !== null ) return $this->mainQuery ;

		/* Monta Query */
		$query = "SELECT \n\t" . implode ( ", \n\t" , $this->querySelect ) . " \n\nFROM " . implode ( ", \n\t" , $this->queryFrom ) . "\n" . $this->queryJoin ;
		if ( count ( $this->queryWhere ) > 0 )	$query .= "\nWHERE " . implode ( " \nAND " , $this->queryWhere ) ;

		$query .= implode ( " " , $this->queryAdicionais ) ;

        /* Adiciona a Ordem */
        if ( !empty ( $this->queryGroup ) ) $query .= " \n\nGROUP BY " . implode ( ", \n" , $this->queryGroup ) ;
        if ( !empty ( $this->queryOrder ) ) $query .= " \nORDER BY " . $this->queryOrder ;

		$this->mainQuery = stripslashes ( $query ) ;

		return $this->mainQuery ;

	}

	public function getResultadoQuery ( ) {

		$resultado  = $this->fetchAll ( $this->getQuery ( ) ) ;
		$this->queryCount = sizeof ( $resultado ) ;

		return $resultado;
		// if ( PEAR::isError ( $resultado ) ) {
		// 	print_r ( $resultado ) ;
		// 	return false;
		// } else {
		// 	return $resultado;
		// }

	}

	public function closeConnect(){
		ModelAbstract::$db->disconnect();
        ModelAbstract::$db = null ;
	}

}
