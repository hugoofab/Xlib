<?php
/**
 * Description of ModelAbstract
 * Crie uma model (objeto DAO) extendendo esta classe para manter seu código limpo, livre de queries e
 * operações de banco de dados.
 *
 * O objetivo desta classe é criar uma interface para a criação de um Data Access Object (DAO)
 * simplificando a camada controladora e otimizando o uso do objeto MDB2 e MDB2->extended
 *
 * é necessário passar a referência do objeto MDB2 no construtor deste método
 *
 * CLASSES AUXILIARES
 * \MDB2\Extended.php
 * \MDB2\Driver\oci8.php
 *
 * @author hugo.ferreira
 */
class ModelAbstract {

    /**
     * o objeto $db (MDB2) usado como padrão em todos os projetos da empresa
     * @var object, precisa ser passado no construtor
     */
    private static $db = null ;
    private static $dbClass = '' ;

    private static $queryHistory = array() ;
    private static $totalExecutionTime = 0 ;

    private static $hasError = false ;
    private static $instances = 0 ;

    /**
     * É necessário passar o $db usado como padrão nos projetos de intranet da RPC,
     * geralmente criado pelo db/conexao.inc@init.php
     * @param MDB2 $db objeto MDB2 do framework Pear
     */
    public function __construct ( $db = null ) {
        ModelAbstract::$instances++;
        if ( $db !== null ) ModelAbstract::setDB ( $db );
        if ( method_exists ( $this , "init" ) ) {
            $this->init();
        }
    }


    /**
     * Classes de Connectors ($db) aceitos:
     * 'MDB2_Driver_oci8'
     * 'DB_oci8'
     * @param type $db3
     */
    public static function setDB ( $db ) {
        $class = get_class ( $db );
        if ( $class !== 'MDB2_Driver_oci8' && $class !== 'DB_oci8' ) throw new Exception ( "Conector inválido. esperado: objeto de MDB2_Driver_oci8 ou DB_oci8" ) ;
//pr($class);
        ModelAbstract::$dbClass = $class ;
        ModelAbstract::$db = $db;
    }

    public static function getDB ( ) {
        return ModelAbstract::$db;
    }

    public static function getDBType ( ) {
        return ModelAbstract::$dbClass;
    }

    /**
     * o uso deste método poderia ser simplificado fazendo uma chamada ao método $db->nextID ();
     * mas em testes, o valor retornado por nextID() foi sequencial iniciando de 1, desconsiderando
     * o valor real da sequence. por isso estamos fazendo o trabalho novamente, porém de forma simplificada
     * @param string $sequenceName nome da sequence
     * @return integer próximo valor de sequência
     */
    public function getSequenceNextVal ( $sequenceName = null ) {
    	if ( empty ( $sequenceName ) ) throw new Exception( "O nome da sequence está em branco" );
        $query = "SELECT ${sequenceName}.NEXTVAL FROM DUAL";
        $res = (int) $this->getOne($query);
        return $res;
    }

    public function getNextValFromSequencia ( $tableName ) {
        $query = "SELECT ID_ULTIMO_SEQ FROM SEQUENCIA WHERE NM_TABELA_SEQ = '$tableName' ";
        $res = $this->fetchOne ( $query );
        $res++;
        $this->execute ( "UPDATE SEQUENCIA SET ID_ULTIMO_SEQ = '$res' WHERE NM_TABELA_SEQ = '$tableName' " );
        return $res ;
    }

    /**
     * Route executions between MDB2 or DB_oci8 accordly with our current db connector
     * @param type $method
     * @param type $query
     * @throws Exception
     */
    private function _fetch ( $method , $query , $db = false ) {

//        metodos usados no DB_oci8 (Z:\prodata\Model\MonitoramentoTerminal.php)
//        $this->db->queryRow()
//        $this->db->extended->getAssoc()
//        $this->db->queryAll()
//        $this->db->query()
//          DB_Error
        try {

            $start_time = microtime ( TRUE ) ;

			if ( $db === false ) $db = ModelAbstract::$db;
			$dbClass = ModelAbstract::$dbClass ;

            if ( $dbClass === 'MDB2_Driver_oci8' ) {
                $method = 'get' . $method ;
                $res = $db->extended->${method}($query);
                if ( get_class ( $res ) === 'MDB2_Error' ) throw new Exception ( $res->message ) ;
            } else if ( $dbClass === 'DB_oci8' ) {
                $method = 'get' . $method ;
                $res = $db->{$method}($query);
                if ( get_class ( $res ) === 'DB_Error' ) throw new Exception ( $res->message ) ;
            } else {

                throw new Exception ( "Invalid Db Connector :" . $dbClass ) ;

            }

            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) , $db );

        } catch ( Exception $err ) {

            ModelAbstract::logQuery ( $query , $start_time , false , 0 , $res->userinfo , get_class ( $this ) , $db );

            throw new Exception ( $err->getMessage () ) ;
            //echo $err->getTraceAsString ( ) ;
        }


        return $res ;

    }

    /**
     * alias para o método getOne
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     */
    public function getOne ( $query , array $bindList = array ( ) ){
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'One' , $query );
    }
    public function fetchOne ( $query , array $bindList = array ( ) ) {
        return $this->getOne ( $query , $bindList );
    }

    /**
     * alias para o método getAll
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     */
    public function getAll ( $query , array $bindList = array ( ) , $db = false ){
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'All' , $query , $db );
    }
    public function fetchAll ( $query , array $bindList = array ( ) , $db = false ) {
        return $this->getAll ( $query , $bindList , $db );
    }

    public function fetchLimit ( $query , $start , $maxRows , array $bindList = array ( ) ) {

        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );

        try {
            $start_time = microtime ( TRUE ) ;

    //        if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
            ModelAbstract::$db->setLimit ( $maxRows , $start ) ;
            $result = ModelAbstract::$db->query ( $query ) ;

            if ( PEAR::isError ( $result ) ) throw new Exception ( $result->userinfo ) ;

            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) );

            return $result->fetchAll ( ) ;
    //        }

        } catch ( Exception $err ) {

            ModelAbstract::logQuery ( $query , $start_time , false , '0' , $err->getMessage() , get_class($this) );

            throw new Exception ( "Erro na consulta. Favor informar o desenvolvedor" ) ;
            //echo $err->getTraceAsString ( ) ;
        }


    }


    /**
     * alias para o método getRow
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     */
    public function getRow ( $query , array $bindList = array ( ) , $db = false ){
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return ModelAbstract::_fetch ( 'Row' , $query , $db );
    }
    public function fetchRow ( $query , array $bindList = array ( ) , $db = false ) {
        return $this->getRow ( $query , $bindList , $db );
    }

    /**
     * alias para o método getAssoc
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     */
    public function getAssoc ( $query , array $bindList = array ( ) ){
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList );
        return $this->fetchAssoc ( $query , $bindList );
//        return ModelAbstract::_fetch ( 'Assoc' , $query );
    }
    public function fetchAssoc ( $query , array $bindList = array ( ) ) {
        $result = $this->getAll ( $query , $bindList );
        $result = $this->arrayAssoc ( $result );
        return $result ;
    }
    public function arrayAssoc ( $array ) {
        $output = array ( );
        if ( !empty ( $array ) ) foreach ( $array as $res ) {
            $output[(array_shift($res))] = array_shift($res);
        }
        return $output;
    }

    /**
     * faz bind seguro em uma query parametrizada com ?
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     * @return string query com os parâmetros adicionados de forma segura
    */
    public function bind ( $query , array $bindList ) {
    	$pos = 0 ;
        while ( $pos = strpos ( $query , "?" , $pos ) )  {
            $value = array_shift ( $bindList ) ;
            if ( $value !== null ) {
                // a função addslashes pode ser substituída por algo mais eficiente caso exista
                $value = "'" . addslashes ( $value ) . "'" ;
            } else {
                $value = " null " ;
            }
            $query = substr_replace ( $query , $value , $pos , 1 ) ;
            $pos += strlen($value);
        }
        return $query ;
    }

    /**
     * Executa uma query independente da classe connector que esteja sendo usada
     * @param type $query
     * @return type
     * @throws Exception
     */
    private function _query ( $query , $db ) {

    	if ( $db === false ) $db = ModelAbstract::$db;

        try {
            $start_time = microtime ( TRUE ) ;
            if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
                $res = $db->exec ( $query );
                if ( get_class ( $res ) === 'MDB2_Error' ) throw new Exception ( $res->message ) ;
            } else if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
                $res = $db->query ( $query );
                if ( get_class ( $res ) === 'MDB2_Error' ) throw new Exception ( $res->message ) ;
            } else {
                throw new Exception ( "Invalid Db Connector :" . ModelAbstract::$dbClass ) ;
            }
            ModelAbstract::logQuery ( $query , $start_time , true , '0' , '' , get_class($this) , $db );
            return $res ;

        } catch ( Exception $err ) {
            $errorMessage = $err->getMessage();
            if ( DEBUG ) $errorMessage .= "<br>" . $res->userinfo;
            ModelAbstract::logQuery ( $query , $start_time , false , 0 , $errorMessage , get_class ( $this ) , $db );
            throw new Exception ( $err->getMessage () ) ;
        }

    }

    public static function getQueryHistory ( ) {
        return array (
            'totalTime' => ModelAbstract::$totalExecutionTime ,
            'queryList' => ModelAbstract::$queryHistory
        ) ;
    }

    public static function dumpQueries ( ) {

        $queries = ModelAbstract::getQueryHistory();

        $db = ModelAbstract::getDB();

        $output = '<div style="background:#000;color:#0F0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
            '<span style="color:#0F0;font-size:1.3em;">Connection default: ' .
            strtoupper($db->dsn['username']."@".$db->dsn['hostspec']).
            '</span>' .
        '</div>' ;

        $count = 1;
        foreach ( $queries['queryList'] as $query ) {

            $output .= '<div style="background:#000;color:#0F0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
                '<div style="width:100%;height:20px;overflow:hidden;white-space:nowrap">Query '.$count++.' ['.$query['class'].']: ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>' .
                ModelAbstract::queryBeautifier ( $query['query'] ) .
//                ModelAbstract::queryBeautifier ( str_replace ( "\n" , "<br>" , $query['query'] ) ) .
                '<span style="color:#FF0;">' .
                    '<br><br>Time: ' . $query['time'] .
                    '<br>Status: ' . $query['status'] .
                '</span>' .
//            '<br><div style="width:100%;height:10px;overflow:hidden;">---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</div>' .
                $query['backtrace'] .
            '</div>' ;

        }
        $output .= '<div style="background:#000;color:#FF0;font-size:12px;font-family:courier new;width:95%;padding:10px;" >' .
            "<br>Total Time: " . $queries['totalTime'] .
        '</div>' ;

//        $output .= "<pre>" . print_r ( ModelAbstract::$db->dsn , true ) . "</pre>" ;

        return $output ;
    }

    /**
     * executa uma query com menos burocracia. geralmente se usa este método para executar um INSERT,DELETE ou UPDATE
     * nada de SELECT.
     * @param string $query query parametrizada (ou não) com '?' nos parâmetros
     * @param array $bindList array contendo os valores dos parâmetros. pode ser omitido, nesse caso a query será
     * executada como está. valores NULL nos parâmetros serão respeitados e gravados no banco como NULL
     */
    public function execute ( $query , array $bindList = array ( ) , $db = false ) {
        if ( !empty ( $bindList ) ) $query = $this->bind ( $query , $bindList ) ;
        return $this->_query ( $query , $db );
    }
    public function query ( $query , array $bindList = array ( ) , $db = false ) {
    	return $this->execute ( $query , $bindList , $db ) ;
    }

    public function limitQuery ( $query , $start , $linhas ) {

        try {

            $start_time = microtime ( TRUE ) ;

            if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
                $result = ModelAbstract::$db->limitQuery ( $query , $start , $linhas ) ;
    //        } else if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
            } else {
                throw new Exception ( "Invalid DB Connector" ) ;
            }
            ModelAbstract::logQuery ( $query , $start_time , true , $linhas , '' , get_class ( $this ) );

            return $result ;

        } catch ( Exception $err ) {
            ModelAbstract::logQuery ( $query , $start , $linhas , '0' , '' , get_class($this) );
            throw new Exception ( $err->getMessage () ) ;
        }

    }

    /**
     *
     */
    private static function logQuery ( $query , $startTime = false , $executionStatus = true , $linhas = '0' , $errorMessage = '' , $class = '' , $db = false ) {

		$backTrace = debug_backtrace ();
		array_shift($backTrace);
		$backTrace = array_reverse( $backTrace );

		$backtraceOutput = '<br>';
        foreach( $backTrace as $key => $bt ) {
        	if ( $bt['file'] === __file__ ) continue ;
        	// foreach ( $bt['args'] as &$arg ) if ( gettype ( $arg ) === 'object' ) $arg = "Object of " . get_class($arg) ;
        	foreach ( $bt['args'] as &$arg ) {
    			$rand = md5(mt_rand());
        		if ( gettype ( $arg ) === 'object' ) {
        			$arg = "<a style=\"cursor:pointer\" onclick=\"var el=document.getElementById('$rand');if (el.style.display==='none'){el.style.display='block'}else{el.style.display='none'}\"><span style=\"color:#088\">Object of:</span> " . get_class($arg) . "</a>" .
        			"<div id=\"$rand\" style=\"display:none;border:1px solid #000;width:600px;height:150px;overflow:scroll;position:absolute;background:#000;z-index:9999999;\"><pre>" . print_r ( $arg , true ) . "</pre></div>" ;
        		} else if ( gettype ( $arg ) === 'array' ) {
        			$arg = "<a style=\"cursor:pointer\" onclick=\"var el=document.getElementById('$rand');if (el.style.display==='none'){el.style.display='block'}else{el.style.display='none'}\"><span style=\"color:#088\">Array:</span>  " . count ( $arg ) . " elements</a>" .
        			"<div id=\"$rand\" style=\"display:none;border:1px solid #000;width:600px;height:150px;overflow:scroll;position:absolute;background:#000;z-index:9999999;\"><pre>" . print_r ( $arg , true ) . "</pre></div>" ;
        		} else {
        			$arg = "<span style=\"color:#088\">" . gettype ( $arg ) . ":</span> " . $arg ;
        		}
        	}
			$implode         = @implode ( "</span>] , [<span style=\"color:#0FF;\">" , $bt['args'] ) ;
			$function        = $bt['function'] . " ( [<span style=\"color:#0FF;\">" . $implode . "</span>] ) " ;
			if ( is_dir ( ROOT_DIR ) ) $bt['file'] = str_replace( ROOT_DIR , '' , $bt['file'] );
			$backtraceOutput .= "\n<span style=\"font-family:courier new;font-size:10px;\"><span style=\"margin-top:0px;color:#888;padding-left:4px;\">" . $bt['file'] . ":" . $bt['line'] . "&nbsp;</span>-&gt;" . $function . "</span><br>";
        }

		if ( $db === false ) $db = ModelAbstract::$db;

        if ( $class ) {
            $class = "<span style=\"color:#FF0\">" . strtoupper ( $db->dsn['username']."@".$db->dsn['hostspec'] ) . "</span> - class " . $class . "()";
        } else {
            $class = "<span style=\"color:#FF0\">" . strtoupper ( $db->dsn['username']."@".$db->dsn['hostspec'] ) . "</span> - class ModelAbstract::()";
        }

        if ( !$executionStatus ) {
            ModelAbstract::$hasError = true ;
        }

        if ( $executionStatus ) {
            $executionStatus = 'OK';
        } else if ( $errorMessage == '' ) {
            $executionStatus = '<span style="background:#A00;color:#FFF;font-weight:bold;">&nbsp;ERROR&nbsp;</span>';
        } else {
            $formatedMessage = '';
            if ( preg_match ( '/.*Error message:\s*([^\]]+)/' , $errorMessage , $resultArr ) ) $formatedMessage .= "<br/>&nbsp;" . $resultArr[1] . "&nbsp;";
            if ( preg_match ( '/.*Native message:\s*([^\]]+)/' , $errorMessage , $resultArr ) ) $formatedMessage .= "<br/>&nbsp;Native Message: " . $resultArr[1] ;
            if ( $formatedMessage !== '' ) $errorMessage = '<span style="background:#A00;color:#FFF;font-weight:bold;">&nbsp;ERROR&nbsp;</span>' . $formatedMessage ;
            $executionStatus = '<span style="background:#A00;color:#FFF;font-weight:bold;">' . $errorMessage . '&nbsp;</span>' ;
        }

        if ( $startTime !== false ) {
            ModelAbstract::$totalExecutionTime += $endTime = microtime ( TRUE ) - $startTime ;
        } else {
            $endTime = $startTime = '-' ;
        }

        ModelAbstract::$queryHistory[] = array (
			'query'     => $query ,
			'time'      => $endTime ,
			'status'    => $executionStatus ,
			'class'     => $class ,
			'backtrace' => $backtraceOutput
        );

    }

    public function startTransaction ( ) {
        try {
            $start_time = microtime ( TRUE )  ;

            if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
                return ModelAbstract::$db->beginTransaction();
            } else if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
                return ModelAbstract::$db->beginTransaction();
            } else {
                throw new Exception ( "Invalid Db Connector" ) ;
            }

            ModelAbstract::logQuery ( 'START TRANSACTION' , $start_time , true , '0' , '' , get_class($this) );

        } catch ( Exception $err ) {
            throw new Exception ( $err->getMessage () ) ;
            //echo $err->getTraceAsString ( ) ;
        }

	}

	public function commit ( ) {

        try {
            $start_time = microtime ( TRUE )  ;

            if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
                return ModelAbstract::$db->commit();
            } else if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
                return ModelAbstract::$db->commit();
            } else {
                throw new Exception ( "Invalid Db Connector" ) ;
            }

            ModelAbstract::logQuery ( 'COMMIT' , $start_time , true , '0' , '' , get_class($this) );

        } catch ( Exception $err ) {
            throw new Exception ( $err->getMessage () ) ;
            //echo $err->getTraceAsString ( ) ;
        }
	}

	public function rollback ( ) {
        try {
            $start_time = microtime ( TRUE )  ;

            if ( ModelAbstract::$dbClass === 'MDB2_Driver_oci8' ) {
                return ModelAbstract::$db->rollback();
            } else if ( ModelAbstract::$dbClass === 'DB_oci8' ) {
                return ModelAbstract::$db->rollback();
            } else {
                throw new Exception ( "Invalid Db Connector" ) ;
            }

            ModelAbstract::logQuery ( 'ROLLBACK' , $start_time , true , '0' , '' , get_class($this) );

        } catch ( Exception $err ) {
            throw new Exception ( $err->getMessage () ) ;
            //echo $err->getTraceAsString ( ) ;
        }
	}

    /**
     *
     * @param array $dsn    um array completo com as chaves phptype , username , password e hostspec
     * phptype é opcional. nesse caso será setado como 'oci8'
     * @param string $connector 'MDB2_Driver_oci8' or 'DB_oci8'
     * @throws Exception
     * @return $db armazena em uma variável estática e em seguida retorna
     */
    public static function connect ( $dsn , $connector = 'MDB2_Driver_oci8' ) {

        if ( gettype ( $dsn ) === 'string' && strpos ( $dsn , "@" ) > -1 ) {
            $dsn = ModelAbstract::getDsn ( $dsn ) ;
        }

        try {
            $start_time = microtime ( TRUE ) ;

            if ( !isset ( $dsn['phptype'] ) ) $dsn['phptype'] = 'oci8';

            if ( $connector === 'MDB2_Driver_oci8' ) {

                $db = MDB2::singleton ( $dsn ) ;

                if ( PEAR::isError ( $db ) ) {
                    if ( DEBUG ) pr ( $db );
                    throw new Exception ( "Não foi possível conectar" ) ;
                }

                $db->setOption ( 'persistent' , true ) ;
                $db->setOption ( 'field_case' , CASE_UPPER ) ;
                $db->setFetchMode ( MDB2_FETCHMODE_ASSOC ) ;
                $db->loadModule ( 'Extended' ) ;


            } else if ( $connector === 'DB_oci8' ) {

                $db = DB::connect ( $dsn ) ;

                if ( PEAR::isError ( $db ) ) {
                    if ( DEBUG ) pr ( $db );
                    throw new Exception ( "Não foi possível conectar" ) ;
                }

                $db->setOption ( 'persistent' , true ) ;
                $db->setFetchMode ( DB_FETCHMODE_ASSOC ) ;

            }

            ModelAbstract::setDB ( $db ) ;

            ModelAbstract::logQuery ( 'CONNECT' , $start_time , true , '0' , '' , get_class($this) );

        } catch ( Exception $err ) {
            ModelAbstract::logQuery ( 'CONNECT' , $start_time , true , '0' , '' , get_class($this) );
            throw new Exception ( $err->getMessage () ) ;
        }

        return ModelAbstract::$db ;

    }

    /**
     *
     * @param type $schemaAtDB
     * @return array
     * @throws Exception
     */
    public static function getDsn ( $schemaAtDB ) {

        $schemaAtDB = strtoupper($schemaAtDB);

        $dsnList = array (
            'CA_USER@TD10' => array(
                'phptype'  => 'oci8',
                'username' => 'CA_USER',
                'password' => 'CA#USER',
                'hostspec' => 'td10'
            ),
            'JLOJA_ADM@TD10' => array(
                'phptype'  => 'oci8',
                'username' => 'jloja_adm',
                'password' => 'jloja#adm',
                'hostspec' => 'td10'
            ),
            'JLOJA_CONSULTA@TP04' => array(
                'phptype'  => 'oci8',
                'username' => 'jloja_consulta',
                'password' => 'qryconsulta',
                'hostspec' => 'tp04'
            ),
            'JLOJA_ADM@TP04_DESENV' => array(
                'phptype'  => 'oci8',
                'username' => 'jloja_adm',
                'password' => 'jloja#adm',
                'hostspec' => 'tp04_desenv'
            ),
			'ECOMERCE_HOMOL@HOMOL_TP04' => array(
				'phptype'  => 'oci8',
	            'username' => "ecomerce_homol",
	            'password' => "ecomerce123homol",
	            'hostspec' => "homol_tp04"
			),
            'JLOJA_CONSULTA@HOMOL_TP04' => array(
                'phptype'  => 'oci8',
                'username' => 'jloja_consulta',
                'password' => 'qryconsulta',
                'hostspec' => 'homol_tp04'
            ),
            'INTRANET_RPC@TP04_DESENV' => array(
                'phptype'  => 'oci8',
                'username' => 'INTRANET_RPC',
                'password' => 'INTRANET#RPC',
                'hostspec' => 'TP04_DESENV'
            )
        ) ;

        if ( array_key_exists ( $schemaAtDB , $dsnList ) ) {
            return $dsnList[$schemaAtDB];
        } else {
            throw new Exception ( "Combinação de SCHEMA e DB desconhecida" ) ;
        }

    }

    public function __destruct ( ) {
        ModelAbstract::$instances--;
        if ( ModelAbstract::$instances > 0 ) return false ;
        if ( empty ( ModelAbstract::$queryHistory ) ) return false ;

        if ( ( ModelAbstract::$hasError && DEBUG === TRUE ) || SHOW_SQL_QUERIES === true ) {
            echo ModelAbstract::dumpQueries() . "<br><br>" ;
            ModelAbstract::$queryHistory = null ;
        }

    }

    public static function queryBeautifier ( $query ) {
        $query = preg_replace ( '/("[^"]*")/i' , "<strong style=\"color:#F80;\">$1</strong>" , $query );
        $query = preg_replace ( '/(\'[^\']*\')/i' , "<strong style=\"color:#F80;\">$1</strong>" , $query );
        $query = preg_replace ( '/(\(|\))/i' , "<strong style=\"font-weight:bold;color:#0FF;\"> $1 </strong>" , $query );
        $query = preg_replace ( '/\b(select|from|inner join|join|left join|right join|where|between|decode|is|null|to_char|to_date|sum|nvl|count|group by|order by|and|or|on|as)\b/i' , "<strong style=\"color:#FF0;text-transform:uppercase;\">$1</strong>" , $query );
        $query = preg_replace ( '/\b(union)\b/i' , "<strong style=\"color:#F00;text-transform:uppercase;\">$1</strong>" , $query );
//        $query = preg_replace ( '/\b(select)\b/i' , "$1\n" , $query );
//        $query = preg_replace ( '/\b(and|inner join|left join|join|right join)\b/i' , "\n$1" , $query );
//        $query = preg_replace ( '/\b(where|group by|order by)\b/i' , "\n\n$1" , $query );

        return $query ;
    }

}
