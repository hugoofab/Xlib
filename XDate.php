<?php
class Xlib_XDate {

	private $dateFormat = "d/m/Y H:i:s" ;
	private $emptyTimeFormat = "__/__/___ --:--:--";
	private $unixTime   = null ;

	protected $nomesMeses 	= array ( '' , 'Janeiro' , 'Fevereiro' , 'Março' , 'Abril' , 'Maio' , 'Junho' , 'Julho' , 'Agosto' , 'Setembro' , 'Outubro' , 'Novembro' , 'Dezembro' ) ;
	protected $weekNames	= array ( null , 'Segunda-feira' , 'Terça-feira' , 'Quarta-feira' , 'Quinta-feira' , 'Sexta-feira' , 'Sábado' , 'Domingo' ) ;
	protected $weekAbbr		= array ( null , 'Seg' , 'Ter' , 'Qua' , 'Qui' , 'Sex' , 'Sab' , 'Dom' ) ;

	public function __construct ( $dateTime = false , $dateFormat = false ) {

		if ( $dateFormat !== false ) $this->setFormat ( $dateFormat ) ;
		if ( $dateTime === false ) $dateTime = time();

		if ( !preg_match ( '/^\d+$/' , $dateTime ) ) {
			// formato string
			$this->setDateTime ( $dateTime , $this->dateFormat );
		} else {
			// unix dateTime
			$this->unixTime = $dateTime ;
		}

	}

	/**
	 * Este metodo ainda não está corretamente implementado. é um exemplo de
	 * como fazer uma validação. precisa ser implementado ainda
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function isValid ( $data , $mask = 'd/m/Y' ) {
		
		if ( !preg_match ( '/^(\d\d)\/(\d\d)\/(\d\d\d\d)$/' , $data , $result ) ) return false ;
		// still need to implement the application of mask
		if ( $mask !== 'd/m/Y' ) throw new Exception ( "Mask yet not implemented" );
		
		$month = (int) $result[2];
		$day   = (int) $result[1];
		$year  = (int) $result[3];

		if ( !checkdate ( $month , $day , $year ) ) return false ;

		return true ;

	}

	public function getInstance ( $timestamp = false , $dateFormat = false ) {
		$instance = new XDate ( $timestamp , $dateFormat );
		return $instance ;
	}

	public function setEmptyTimeFormat ( $emptyTimeFormat ) {
		$this->emptyTimeFormat = $emptyTimeFormat ;
		return $this ;
	}

	public function setDate ( $date ) {
		$this->date = $date ;
		return $this ;
	}

	public static function timeToSeconds ( $time ) {
		
		if ( !preg_match ( '/^\d\d:\d\d:\d\d$/' , $time ) ) return false ;
		// if ( !preg_match ( '/^\d\d:\d\d:\d\d$/' , $time ) ) throw new Exception ( "Formato inválido" );

		$horaMinutoSegundo = explode ( ":" , $time ) ;

		$output = $horaMinutoSegundo[2] + ( $horaMinutoSegundo[1] * 60 ) + ( $horaMinutoSegundo[0] * 3600 ) ;

		return $output ;

	}

	public function getFormatTime ( $format ) {
		if ( empty ( $this->unixTime ) ) return $this->emptyTimeFormat ;
		return date ( $format , $this->unixTime ) ;
	}

	public function setTime ( $time ) {
		$this->time = $time ;
		return $this ;
	}

	public function setDateTime ( $dateTime , $dateFormat = false ) {

		if ( empty ( $dateTime ) ) return false ;
		$hora = $minuto = $segundo = $dia = $mes = $ano = "";

		if ( preg_match ( '/^\d+$/' , $dateTime ) ) {
			$this->unixTime = $dateTime ;
			return $this ;
		} else if ( preg_match ( '/^(\d\d)\/(\d\d)\/(\d\d\d\d)\s(\d\d):(\d\d):(\d\d)$/' , $dateTime , $match ) ) {
			$dia     = $match[1] ;
			$mes     = $match[2] ;
			$ano     = $match[3] ;
			$hora    = $match[4] ;
			$minuto  = $match[5] ;
			$segundo = $match[6] ;
		} else if ( preg_match ( '/^(\d\d)\/(\d\d)\/(\d\d\d\d)\s(\d\d):(\d\d)$/' , $dateTime , $match ) ) {
			$dia     = $match[1] ;
			$mes     = $match[2] ;
			$ano     = $match[3] ;
			$hora    = $match[4] ;
			$minuto  = $match[5] ;
			$segundo = $match[6] ;
		} else if ( preg_match ( '/^(\d\d\d\d)-(\d\d)-(\d\d)\s(\d\d):(\d\d):(\d\d)$/' , $dateTime , $match ) ) {
			$ano     = $match[1] ;
			$mes     = $match[2] ;
			$dia     = $match[3] ;
			$hora    = $match[4] ;
			$minuto  = $match[5] ;
			$segundo = $match[6] ;
		} else if ( preg_match ( '/^(\d\d\d\d)-(\d\d)-(\d\d)$/' , $dateTime , $match ) ) {
			$ano     = $match[1] ;
			$mes     = $match[2] ;
			$dia     = $match[3] ;
			$hora    = 0 ;
			$minuto  = 0 ;
			$segundo = 0 ;
		} else if ( preg_match ( '/^(\d\d)\/(\d\d)\/(\d\d\d\d)$/' , $dateTime , $match ) ) {
			$dia     = $match[1] ;
			$mes     = $match[2] ;
			$ano     = $match[3] ;
			$hora    = 0 ;
			$minuto  = 0 ;
			$segundo = 0 ;
		} else {
			pr('$dateTime: '.$dateTime , '$dateFormat: '.$dateFormat);
			throw new Exception ( "Formato de data [${dateTime}] não reconhecido" );
		}

		$this->unixTime = mktime ( $hora , $minuto , $segundo , $mes , $dia , $ano ) ;
		return $this ;

	}

	public function now (){
		$this->unixTime = time();
		return $this ;
	}

	public function __toString ( ) {
		
		if ( empty ( $this->unixTime ) ) return $this->emptyTimeFormat ;

		return date ( $this->dateFormat , $this->unixTime ) ;

	}

	public function setFormat ( $format ) {
		$this->dateFormat = $format ;
		return $this ;
	}

	public function getDateTimeDb ( ) {
		return date ( "Y-m-d H:i:s" , $this->unixTime ) ;
	}

	public function getDateDb ( ) {
		return date ( "Y-m-d" , $this->unixTime ) ;
	}

	public function getDateTimeBr ( ) {
		return date ( "d/m/Y H:i:s" , $this->unixTime ) ;
	}

	public function getDateBr ( ) {
		return date ( "d/m/Y" , $this->unixTime ) ;
	}

	public function getTime ( ) {
		return date ( "H:i:s" , $this->unixTime ) ;
	}

	public function getWeekNameAbr ( ) {
		$week   = date ( "N" , $this->unixTime );
		$output = $this->weekAbbr[$week];
		return $output ;
	}

	// public function toBr ( ) {
	// 	$this->date = Xlib_XDate::brToBr($this->date);
	// 	return $this ;
	// }

    public static function brToDb ( $date , $time = "00:00:00" ) {
        $date = preg_replace ( '/^(\d\d)\/(\d\d)\/(\d\d\d\d)$/' , "$3-$2-$1" , $date ) ;
        if ( $time !== false ) $date = $date . " " . $time ;
        return $date ;
    }

    public static function dbToBr ( $date ) {
        $date = trim ( $date );
        if ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})$/' , $date , $resultArray ) ) {
            return $resultArray[3] . "/" . $resultArray[2] . "/" . $resultArray[1] ;
        } else if ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})\s(\d\d:\d\d:\d\d)$/' , $date , $resultArray ) ) {
            return $resultArray[3] . "/" . $resultArray[2] . "/" . $resultArray[1] . " " . $resultArray[4];
        } else {
            return $date ;
        }
    }

    /**
     * Converte um número de dias para uma informação literal melhor interpretada por humanos
     * Ex.:
     * Entrada: 502  Saída: 1 ano, 4 mêses e 17 dias
     * Entrada: 395  Saída: 1 ano e 1 mês
     * Entrada: 35   Saída: 1 mês e 5 dias
     * @param integer $days dias para conversão
     * @param boolean $week se quer especificar as semanas ou não
     * @param array $arrResult usado pelo método
     * @return string
     */
    public static function daysToHuman ( $days , $week = false , $arrResult = array ( ) ) {

        if ( $days >= 365 ) {
            $years          = (int) ( $days / 365 ) ;
            $arrResult[]    = $years . ( ( $years === 1 ) ? " ano" : " anos" ) ;
            $resto          = $days % 365 ;
            if ( $resto > 0 ) return Xlib_XDate::daysToHuman ( $resto , $week , $arrResult ) ;
        } else if ( $days >= 30 ) {
            $month          = (int) ( $days / 30 ) ;
            $arrResult[]    = $month . ( ( $month === 1 ) ? " mês" : " meses" ) ;
            $resto          = $days % 30 ;
            if ( $resto > 0 ) return Xlib_XDate::daysToHuman ( $resto , $week , $arrResult ) ;
        } else if ( $week && $days >= 7 ) {
            $weeks          = (int) ( $days / 7 ) ;
            $arrResult[]    = $weeks . ( ( $weeks === 1 ) ? " semana" : " semanas" ) ;
            $resto          = $days % 7 ;
            if ( $resto > 0 ) return Xlib_XDate::daysToHuman ( $resto , $week , $arrResult ) ;
        } else if ( $days > 0 ) {
            $arrResult[] = $days . ( ( $days === 1 ) ? " dia" : " dias" ) ;
        }

        $output = $arrResult[0] ;
        for ( $i = 1 ; $i < count ( $arrResult ) ; $i++ ) {
            $sep = ( $i+1 === count ( $arrResult ) ) ? ' e ' : ', ' ;
            $output .= $sep . $arrResult[$i] ;
        }

        return $output ;

    }

    // PASSAR TUDO ABAIXO PARA ESTATICO
    //
    //
    //
//
//	/**
//	 * compara duas datas no formato brasileiro DD/MM/YYYY
//	 * a lógica aplicada é a mesma usada no strcmp() do c++
//	*/
//	public function compareDateBrFormat ( $date1 , $date2 ) {
//
//		$date1 = $this->dateToTimeStamp ( $date1 ) ;
//		$date2 = $this->dateToTimeStamp ( $date2 ) ;
//
//		if ( $date1 > $date2 ) {
//			return 1 ;
//		} else if ( $date1 < $date2 ) {
//			return -1 ;
//		} else { // é igual
//			return 0 ;
//		}
//
//	}
//
//	/**
//	 * reconhece o formato da data informada e retorna seu Unix timestamp
//	 * @param $date (string)
//	 * DD/MM/YYYY HH:MM:SS
//	 * DD/MM/YYYY HH:MM
//	 * DD/MM/YYYY HH
//	 * DD/MM/YYYY
//	*/
//	public function dateToTimeStamp ( $date ) {
//		//* DD/MM/YYYY HH:MM:SS
//		//* DD/MM/YYYY HH:MM
//		//* DD/MM/YYYY HH
//		//* DD/MM/YYYY
//		$date = preg_match ( '/^(\d{2})\/(\d{2})\/(\d{4})\s*(\d*)?:?(\d*)?:?(\d*)?$/' , $date , $result ) ;
//
//		array_shift ( $result ) ;
//		foreach ( $result as &$r ) $r = ( int ) $r ;
//
//		$timestamp = mktime ( $result[3] , $result[4] , $result[5] , $result[1] , $result[0] , $result[2] ) ;
//
//    //[0] => 13
//    //[1] => 9
//    //[2] => 2012
//    //[3] => 10
//    //[4] => 52
//    //[5] => 0
//		return $timestamp ;
//
//	}
//
//	/**
//	 *
//	*/
//	public function getLastDayOfMonth ( $month , $year ) {
//		return date ( 'd' , mktime ( 0 , 0 , 0 , $month + 1 , 0 , $year ) ) ;
//	}
//
//	/**
//	 * cria um array com um range de numeros do ano no formato YYYY
//	 * @param $sub (integer) valor, normalmente negativo de anos passados (0 para começar do ano atual)
//	 * @param $add (integer) valor, normalmente positivo de anos para frente (0 para terminar no ano atual)
//	 *  caso este parâmetro seja omitido, o valor de sub será forçado a ser negativo e o $add será a versão de $sub positiva
//	 * @return um array com um range de numeros do ano no formato YYYY contando a partir do ano atual + $sub e indo até ano atual + $add
//	*/
//	public function getYearRange ( $sub , $add = false ) {
//		if ( $add === false ) {
//			if ( $sub > 0 ) $sub = $sub * -1 ;
//			$add = $sub * -1 ;
//		}
//		$yearRange 	= array ( ) ;
//		$year 		= date ( "Y" ) ;
//		$exaust 	= 1000 ;
//
//		for ( $cont = ( $year + $sub ) ; $cont <= ( $year + $add ) ; $cont++ ) {
//			if ( $exaust-- <= 0 ) exception ( "Limite máximo atingido" , "Date::getYearRange Limite máximo atingido!" ) ;
//			$yearRange[] = $cont ;
//		}
//
//		return $yearRange ;
//
//	}
//
//	/**
//	 * retorna um range de meses
//	 * @param $start (integer) o mês de início 1 = janeiro
//	 * @param $end (integer) o mês de fim 12 = dezembro. se for ignorado, o $start será também o $end e só será retornado um único mes
//	 * @return (array) array associativo com número e nome dos meses como chave e valor respectivamente
//	 * Ex.: getMonthRange ( 1 , 2 ) = array ( '01' => 'Janeiro' , '02' => 'Fevereiro' ) ;
//	*/
//	public function getMonthRange ( $start , $end = false ) {
//		if ( $end === false ) $end = $start ;
//		$range = array ( ) ;
//		for ( $cont = $start ; $cont <= $end ; $cont++ ) {
//			$monthNum = str_pad ( $cont , 2 , "0" , STR_PAD_LEFT ) ;
//			$range[$monthNum] = $this->nomesMeses[$cont] ;
//		}
//		return $range ;
//	}
//
//	/**
//	 * recebe a abreviação em 3 letras de um dia da semana e retorna a tradução conforme atributo atual do objeto
//	*/
//	public function translateWeekAbbr ( $weekAbbr ) {
//
//		switch ( strtoupper ( $weekAbbr ) ) {
//			case "SUN" : return $this->weekAbbr[0] ;
//			case "MON" : return $this->weekAbbr[1] ;
//			case "TUE" : return $this->weekAbbr[2] ;
//			case "WED" : return $this->weekAbbr[3] ;
//			case "THU" : return $this->weekAbbr[4] ;
//			case "FRI" : return $this->weekAbbr[5] ;
//			case "SAT" : return $this->weekAbbr[6] ;
//			default : return false ;
//		}
//
//	}
//
//	public function getNomeMes ( $mes = false ) {
//		if ( !$mes ) {
//			return $this->nomesMeses ;
//		} else {
//			return $this->nomesMeses[$mes] ;
//		}
//	}

}