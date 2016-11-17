<?php

class XListaDados {

/**
 * AS VARIAVEIS PROTECTED QUE ESTÃO COLADAS NA MARGEM ESQUERDA FORAM PASSADAS DE PRIVATE PRA PROTECTED RECENTEMENTE
 * @var type
 */
protected $id; // identificacao do objeto
    protected $filtersName      = "" ;
	public $paginacao           = true ; // determina o uso de paginacao
	public $ordenacao           = true ; // determina o uso de ordenacao
	public $linhasPorTabela     = 10 ; // quantidade de linhas por tabela
protected $listaDb; // query que ira retornar os campos
protected $paginaAtual        = 1 ; // pagina atual da paginacao
protected $campos             = array ( ); // campos que devem ser colocados na tabela
protected $fieldFormatters    = array ( );
protected $buttons            = array ( );
protected $filters            = array ( );
protected $acoes              = array ( ); // botoes de ações de cada campo
static protected $defaultTemplate    = "XListaDados";
protected $template           = "";
protected $tableClassList       = array ( 'table' , 'table-condensed' , 'listaDados' );
protected $tableTitle           = false ;
protected $tablePrimaryKeyList= array ( );
protected $showLineCounter    = false;
protected $comboPagina; // se mostra a listagem p-ara escolher a pagina atual
protected $dadosQuery; // Array de dados que serão apresentados na tela
protected $queryCount;
protected $totColum           = 2;
protected $AutoDetalhes       = FALSE;
protected $AutoDetalhesStr;
//	private $error              = array();
protected $condicao           = array();
protected $UsaBackgroundIndex = "";
protected $checkbox           = array();
    protected $Helper           = null ; // para implementar
    protected static $instanceCount = 1;
protected $ListaDadosDisplay      = null ;
    protected $permission         = array ( );        // usada para validar permissões de acesso da RPC
    protected $flagHasMandatoryFilter = false ;
    protected $filterRequired = false ;

    protected $createPageLink = "";

	public function __construct ( $db = null ) {

		$this->listaDb = new XListaDadosDB ( $db );

        $this->id = 'GRID' . substr ( md5 ( $_SERVER['SCRIPT_FILENAME'] ) , 0 , 6 ) . "_" . XListaDados::$instanceCount++ ;
        $this->filtersName = $this->id . Xlib_XListaDados_FieldFilterAbstract::getFiltersGlobalID ( ) ;
//        $this->setComboPagina(true );
	}

	public static function getInstance ( $db = null ) {
        return new XListaDados ( $db );
    }

    /**
     * INSERE A COLUNA APENAS NA TABELA (T)
     * @param type $querySelect
     * @param boolean $alias
     * @param null $align
     * @param null $nowrap
     * @param boolean $nvl
     * @param string $formatNumber
     * @return type
     */
    public function select ( $querySelect = null , $alias = false , $align = null , $nowrap = null , $nvl = false , $formatNumber = "N" ) { //$nvl = " "
        if ( $querySelect === null ) {
            return $this->addEmptyColumn ( );
        }
        return $this->_select ( $querySelect , $alias , $align , $nowrap , $nvl , $formatNumber , 'T' ) ;
    }

    /**
     * INSERE A COLUNA APENAS NOS PARAMETROS (P)
     * @param type $querySelect
     * @param boolean $alias
     * @param null $align
     * @param null $nowrap
     * @param boolean $nvl
     * @param string $formatNumber
     * @return type
     */
    public function selectParam ( $querySelect , $alias = false , $align = null , $nowrap = null , $nvl = false , $formatNumber = "N" ) { //$nvl = " "
        return $this->_select ( $querySelect , $alias , $align , $nowrap , $nvl , $formatNumber , 'P' ) ;
    }

    /**
     * INSERE A COLUNA NOS PARAMETROS E NA TABELA (A)
     * @param type $querySelect
     * @param boolean $alias
     * @param null $align
     * @param null $nowrap
     * @param boolean $nvl
     * @param string $formatNumber
     * @return type
     */
    public function selectID ( $querySelect , $alias = false , $align = null , $nowrap = null , $nvl = false , $formatNumber = "N" ) { //$nvl = " "
        return $this->_select ( $querySelect , $alias , $align , $nowrap , $nvl , $formatNumber , 'A' ) ;
    }

    public function addButton ( $buttonList ) {

        $colCaption = "EMPTYCOL" . count ( $this->campos ) ;
        $this->addEmptyColumn ( $colCaption );

        if ( !is_array ( $buttonList ) ) {
            $buttonList = array ( $buttonList );
        }

        foreach ( $buttonList as $button ) {
            if ( get_class ( $button ) !== 'Xlib_XListaDados_XButton' ) throw new Exception ( "Botão deve ser uma instancia de Xlib_XListaDados_XButton" ) ;

            $this->buttons[$colCaption][] = $button ;

        }

        return $this;
    }

    /**
     *
     * @param type $filter
     */
    public function addFilter ( Xlib_XListaDados_FieldFilterAbstract $filter , Array $rules = array ( ) ) {
        if ( in_array ( 'MANDATORY', $rules ) ) {
            $filter->setMandatory ( true ) ;
            $this->flagHasMandatoryFilter = true ;
        }
        $filter->setTableID($this->id);
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * retorna os filtros adicionados
     */
    public function getFiltersForm ( $formType = "inline" ) {

        $filtersFormType    = array ( 'inline' , 'horizontal' );
        $formType               = strtolower ( $formType );
        if ( !in_array ( $formType , $filtersFormType ) ) throw new Exception ( "Tipo de visualização indisponível" );

        $output = "<form class=\"form-" . $formType . " xlistadados-filter-form\" role=\"form\" method=\"post\" onsubmit=\"for(var i=0;i<this.elements.length;i++){if(this.elements[i].getAttribute('name')===null)continue;createCookie(this.elements[i].getAttribute('name'),this.elements[i].value,1)};\" >" ;
        foreach ( $this->filters as $filter ) {
            $output .= $filter ;
        }

        $output .= "
            <div class=\"form-group\" style=\"float:right;\">
                <label >&nbsp;</label><br/>
                <button type=\"submit\" class=\"btn btn-default \"><span class=\"glyphicon glyphicon-filter\"></span> Filtrar</button>
            </div>";

        $output .= "</form>";

        return $output;
    }

    public function getFiltersForm2 ( $formType = "inline" ) {

        $filtersFormType    = array ( 'inline' , 'horizontal' );
        $formType               = strtolower ( $formType );
        if ( !in_array ( $formType , $filtersFormType ) ) throw new Exception ( "Tipo de visualização indisponível" );

        $output = "<form class=\"form-" . $formType . " xlistadados-filter-form\" role=\"form\" method=\"post\" onsubmit=\"for(var i=0;i<this.elements.length;i++){if(this.elements[i].getAttribute('name')===null)continue;createCookie(this.elements[i].getAttribute('name'),this.elements[i].value,1)};\" >" ;

        foreach ( $this->filters as $filter ) {
            $output .= str_replace("&nbsp;","",$filter);
        }

        $output .= "
            <div class='form-group'>
                <button type=\"submit\" class=\"btn btn-default \"><span class=\"glyphicon glyphicon-filter\"></span> Filtrar</button>
            </div>";

        $output .= "</form>";

        return $output;
    }

    /**
     * Coloca uma coluna vazia
     * @return \XListaDados
     */
    public function addEmptyColumn ( $colCaption = "" ) {

        if ( empty ( $colCaption ) ) {
            $colCaption = "EMPTYCOL" . count ( $this->campos ) ;
        }

        $this->listaDb->addQuerySelect( "''" , $colCaption ) ;

		$campo = new XCampo($colCaption, '' , 'T',false, "" , false , false );
        $campo->allowOrder ( false );
		array_push($this->campos,$campo);
        return $this ;

    }

    public function requireFilter ( $require = true ) {
    	$require = (bool) $require ;
    	return $this->setFilterRequired( $require );
    }

    public function setFilterRequired ( $newStatus ) {
    	$this->filterRequired = (boolean) $newStatus;
    	return $this ;
    }

    public function setCreatePageLink ( $link ) {
    	$this->createPageLink = $link ;
    	return $this;
    }

    /**
     * Objeto que irá formatar o campo
     * @param FieldFormatterAbstract $formatter
     */
    public function setFormatter ( Xlib_XListaDados_FieldFormatterAbstract $formatter ) {
        $offset = count ( $this->campos ) - 1 ;
        if ( $offset < 0 ) throw new Exception ( "Faça select de ao menos um campo" ) ;
        $key = $this->campos[$offset]->getNome();
        $formatter->setFieldAlias($key);
        $this->fieldFormatters[$key][] = $formatter;
        return $this;
    }

    /**
     * Adiciona o aliasLabel de uma coluna na lista de primary key,
     * a importancia da primary key é ser adicionada em um botão.
     * como o botão é o ultimo a ser adicionado, provavelmente você já incluiu a PK no ->selectID ( )
     * mas se por um acaso não usou o ->selectID ( ) ou deseja adicionar um checkbox no inicio da linha (implementação futura)
     * é necessário saber o pk da tabela. para isso, usa-se este método
     * @param string $aliasLabel o nome do campo, você deve saber. se não souber, dê um pr($this->data) pra saber
     */
    public function addPrimaryKey ( $querySelect , $alias = "" ) {
        if ( empty ( $alias ) ) $alias = $querySelect ;
        $aliasLabel = $this->makeLabelFromAlias ( $alias ) ;
        $this->selectParam ( $querySelect , $alias ) ;
        $this->__addPrimaryKey ( $aliasLabel ) ;
        return $this ;
    }

    /**
     * Adiciona o aliasLabel de uma coluna na lista de primary key,
     * a importancia da primary key é ser adicionada em um botão.
     * como o botão é o ultimo a ser adicionado, provavelmente você já incluiu a PK no ->selectID ( )
     * mas se por um acaso não usou o ->selectID ( ) ou deseja adicionar um checkbox no inicio da linha (implementação futura)
     * é necessário saber o pk da tabela. para isso, usa-se este método
     * @param string $aliasLabel o nome do campo, você deve saber. se não souber, dê um pr($this->data) pra saber
     */
    private function __addPrimaryKey ( $aliasLabel ) {
        $this->tablePrimaryKeyList[] = $aliasLabel;
        return $this ;
    }


    /**
     * Inclui uma coluna na tabela
     * @param type $querySelect
     * @param type $alias
     * @param type $align
     * @param type $nowrap
     * @param type $nvl
     * @param type $formatNumber
     * @return \XListaDados
     */
    private function _select ( $querySelect , $alias = false , $align = null , $nowrap = null , $nvl = false , $formatNumber = "N" , $tipoCampo ) { //$nvl = " "
        $align  = ( $align === null ) ? "left" : $align ;
        $nowrap = ( $nowrap === null ) ? true : true ;

        $aliasLabel = $this->makeLabelFromAlias ( $alias ) ;
        if ( $tipoCampo === "A" ) $this->__addPrimaryKey ( $aliasLabel );

        $this->listaDb->addQuerySelect( $querySelect , $aliasLabel , $nvl ) ;
        $this->addCampo ( $querySelect , $aliasLabel , $alias , $tipoCampo , $nowrap , $align , $formatNumber ) ;

        return $this ;
    }

    private function makeLabelFromAlias ( $alias ) {
        return substr ( "CAMPO_" . preg_replace ( '/[^\w]/' , '' , strtoupper ( $this->removeAcento ( $alias ) ) ) , 0 , 30 ) ;
    }

    public function from ( $queryFrom , $alias = "" ) {
        if ( $alias !== "" ) $queryFrom .= " " . $alias ;
        $this->listaDb->addQueryFrom ( $queryFrom ) ;
        return $this;
    }

    public function join ( $queryJoin ) {
        $queryFrom .= " " . $alias ;
        $this->listaDb->join ( $queryJoin ) ;
        return $this ;
    }
    public function innerJoin ( $queryJoin ) {
        $this->listaDb->innerJoin ( $queryJoin ) ;
        return $this ;
    }
    public function leftJoin ( $queryJoin ) {
        $this->listaDb->leftJoin ( $queryJoin ) ;
        return $this ;
    }
    public function rightJoin ( $queryJoin ) {
        $this->listaDb->rightJoin ( $queryJoin ) ;
        return $this ;
    }

    public function group ( $queryGroup ) {
        $this->listaDb->addQueryGroup ( $queryGroup ) ;
        return $this ;
    }

    /**
     * adiciona o campo do select ao group by
     * ->select ( "campo" , "label do campo" ) ->groupField ()
     * // a linha acima além de fazer o select em [campo] ainda o adiciona no group by
     * @return \XListaDados
     * @throws Exception
     */
    public function groupField ( ) {
        $offset = count ( $this->campos ) - 1 ;
        if ( $offset < 0 ) throw new Exception ( "Faça select de ao menos um campo" ) ;

        $dbField = $this->campos[$offset]->getDbField();
        $this->listaDb->addQueryGroup ( $dbField ) ;
        return $this ;
    }

    public function where ( $queryWhere , array $bindList = array ( ) ) {

    	if ( !empty ( $bindList ) ) $queryWhere = $this->listaDb->bind ( $queryWhere , $bindList );

        $this->listaDb->addQueryWhere ( $queryWhere ) ;
        return $this ;
    }

    public function order ( $fieldToOrder , $desc = '' ) {
        $this->listaDb->setQueryOrder ( $fieldToOrder , $desc = '' ) ;
        return $this;
    }

    public function getQuery ( ) {
        return $this->getListaDb()->getQuery();
    }

    public function removeAcento ( $texto ) {
        $array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
        , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
        $array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
        , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
        return str_replace( $array1, $array2, $texto);
    }

    public function setPermission ( $requestedPermission , $tipoAcao = null , $permissoes = null ) {
        $this->permission = array (
            'permission'        => $requestedPermission ,
            'tipoAcao'          => $tipoAcao ,
            'permissoes'        => $permissoes
        ) ;
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
	 * seta a classe de manipulação.
	 * esta classe será usada para manipular os dados antes de desenhar a tabela
	 * @param $class, instancia da classe que vamos utilizar ara manipular os dados
	*/
	public function setHelper ( &$helper ) {
		$this->Helper = $helper ;
        return $this;
	}

	public function setCheckBox($name,$valor,$campoComparacao,$condicao){

		$this->checkbox = array("nome"=> $name,"valor"=>$valor,"campoComparacao" =>$campoComparacao, "condicao" =>$condicao);
	}
	public function getCheckBox($name,$valor,$condicao){
		return $this->checkbox ;
	}

	public function setBackgroundIndex($style){
		$this->UsaBackgroundIndex=$style;
	}

    /**
     * Configura uma condição para alterar o estilo da linha ou esconder algumas ações
     *
     * @param string $condicao uma string contendo uma condição para ser avaliada
     * @param string $style um conjunto de regras CSS para aplicar na linha caso a condição seja verdadeira
     * @param Array $acoesToHide o nome das imagens das ações para esconder caso a condição seja verdadeira
     *
     * Exemplo:
     *
     * $Lista->setCondicao ('IDCOLETA == 105' , 'color:#00F;text-decoration:underline;' , array ( 'blah.jpg' , 'image.jpg' )  );
     * $Lista->setCondicao ('IDCOLETA > 100' , 'color:#F00;text-decoration:underline;' , array ( 'blah.jpg')  );
     */
	public function setCondicao ( $condicao , $style , $acoesToHide = array ( ) ) {
        $arr_campo['condicao'] = $condicao ;
        $arr_campo['style'] = $style ;
        $arr_campo['acao'] = $acoesToHide ;
        array_push ( $this->condicao , $arr_campo ) ;
    }

	public function setId($id){
		$this->id= $id;
	}

	public function getId(){
		return $this->id;
	}

	public function getPaginaAtual(){
		return $this->paginaAtual;
	}

	public function setPaginaAtual($pagina){
		$this->paginaAtual = $pagina;
	}

	public function getOrdenacao(){
		return $this->ordenacao;
	}

	public function setLimit($linhas){
        return $this->setLinhasPorTabela ( $linhas );
    }

	public function setLinhasPorTabela($linhas){
		$this->linhasPorTabela = $linhas;
        return $this;
	}

	public function setPaginacao($paginacao){
		$this->paginacao = $paginacao;
        return $this;
	}

	public function setLineCounter($showLineCounter){
		$this->showLineCounter = $showLineCounter;
        return $this;
	}

    public static function setDefaultTemplate ( $template ) {
        XListaDados::$defaultTemplate = $template ;
    }

    public function setTemplate ( $template ) {
        $this->template = $template ;
        return $this;
    }

	public function setOrdenacao($ordenacao){
		$this->ordenacao = $ordenacao;
        return $this;
	}

	public function addCampo( $querySelect , $nomeBanco, $nomePagina, $tipoSaida = 'T',$nowrap = true,$align = "center", $formatNumber="N"){

		foreach($this->getCampos() as $objCampo){
			if($objCampo->getNome()==$nomeBanco){
				return false;
			}
		}

		$campo = new XCampo($nomeBanco, $nomePagina, $tipoSaida, $nowrap, $align,'',$formatNumber);
        $campo->setDbField ( $querySelect );
		array_push($this->campos,$campo);
		return true;

	}

	public function setCampos($campos){
		$this->campos=$campos;
	}

	public function getCampos(){
		return $this->campos;
	}

	public function addAcao($imagem,$href = '',$alt = '',$label = '',$outros = ''){
			foreach($this->getAcoes() as $objAcao){
			if($objAcao->getImagem()==$imagem){
				return false;
			}
		}

		$acao= new XAcao($imagem,$href,$alt,$label,$outros);
		array_push($this->acoes,$acao);
	}

	public function setAcoes($acoes){
		$this->acoes=$acoes;
	}

	public function getAcoes(){
		return $this->acoes;
	}

	public function setComboPagina($comboPagina){
		$this->comboPagina= $comboPagina;
	}

	public function getComboPagina(){
		return $this->comboPagina;
	}

	public function getListaDb(){
		return $this->listaDb;
	}

	public function setDados () {
        if ( $this->dadosQuery ) return true ;

		$whereList = $this->listaDb->getWhereList ( );
		if ( empty ( $whereList ) && $this->filterRequired ) {
			$this->dadosQuery = array ( );
			return false ;
		}

        $queryCount         = $this->queryCount;
        $totalPaginas       = ceil ( ($queryCount / $this->linhasPorTabela ) ) ;
        $totalRegistros     = $queryCount;

		if ( isset ( $_GET['paginacao' . $this->id] ) && $this->paginacao ) {

			$this->paginaAtual  = ( $totalPaginas < (int) $_GET['paginacao'.$this->id] ) ? 1 : (int) $_GET['paginacao'.$this->id] ;
			$limitStart         = ( $this->paginaAtual -1 ) * $this->linhasPorTabela ;
			$this->dadosQuery   = $this->getListaDb()->fetchLimit ( $this->getListaDb()->getQuery() , $limitStart , $this->linhasPorTabela ) ;
        } else {

        	if ( $this->paginacao ) {
                if ( $totalPaginas < $this->paginaAtual ) {
                    $this->paginaAtual = $totalPaginas ;
                }
                $limitStart = ($this->paginaAtual - 1) * $this->linhasPorTabela ;

                $this->dadosQuery = $this->getListaDb ()->fetchLimit ( $this->getListaDb ()->getQuery () , $limitStart , $this->linhasPorTabela ) ;
            } else {
                $this->dadosQuery = $this->getListaDb ()->getResultadoQuery () ;
            }
        }

        // trata os dados antes de gerar o html
		try {
            $paramList = $this->getParamList() ;
            if ( !empty ( $this->dadosQuery ) ) {

                if ( !empty ( $this->buttons ) ) {
                    foreach ( $this->dadosQuery as &$dado ) {
                        foreach ( $this->buttons as $key => $buttonSet ) {
                            foreach ( $buttonSet as $button ) {
                                $button->setData ( $dado , $paramList );
                                $button->setRowID ( $this->extractRowID ( $dado ) ) ;
                                $dado[$key] .= $button;
                            }
                        }
                    }
                }

                $before = md5 ( implode ( '<br>' , array_keys ( $this->dadosQuery[0] ) ) ) ;

                if ( !empty ( $this->fieldFormatters ) ) {
                    foreach ( $this->dadosQuery as &$dado ) {
                        foreach ( $this->fieldFormatters as $key => $formatterSet ) {
                            foreach ( $formatterSet as $formatter ) {
                                $formatter->setData ( $dado );
                                $formatter->setRowID ( $this->extractRowID ( $dado ) ) ;
                                $dado[$key] = $formatter->format ( $dado[$key] ) ;
                            }
                        }
                    }
                }

                if ( is_object ( $this->Helper ) ) $this->dadosQuery = $this->Helper->trataDados ( $this->dadosQuery ) ;

                if ( DEBUG && ( $before !== md5 ( implode ( '<br>' , array_keys ( $this->dadosQuery[0] ) ) ) ) ) {
                    foreach ( $this->campos as $c ) $nomesCamposList[] = $c->getNome ( );
                    pr ( "Campo inválido na Helper <strong style=\"color:#FF0;\">" . get_class ( $this->Helper ) . "</strong><br><BR>Os campos disponíveis são:<br><strong style=\"color:#FF0;\">" . implode ( "<br>" , $nomesCamposList ) . "</strong><br>" );
                    pr ( "<br>Campos alterados:<span style=\"color:#F22;\">\n" . implode ( '<br>' , array_keys ( $this->dadosQuery[0] ) ) . "</span>" ) ;
                }
            }

		} catch ( Exception $err ) {
			throw new Exception ( "AjaxListaDados: erro ao tratar os dados com a classe helper. Verifique se a classe Helper possui um método publico trataDados ( \$dados ) " ) ;
		}

	}

    /**
     * Retorna um array com todos os campos que podem ser usados como parâmetro
     */
    private function getParamList () {
        $output = array ( );
        foreach ( $this->getCampos () as $campo ) {
            if ( $campo->getTipoSaida () == "P" ) {
                $output[] = $campo->getNome ( );
            }
        }
        return $output ;
    }

    /**
     * retorna o ID da linha com base em qual campo se usou o selectID
     */
    public function extractRowID ( $data ) {
        $outputIdList = array ( );

        if ( empty ( $this->tablePrimaryKeyList ) && DEBUG ) pr("\n Não tem uma primary key configurada\n use ->selectID ( ) ou ->addPrimaryKey ( )\n");

        foreach ( $this->tablePrimaryKeyList as $pkFieldName ) {
            $outputIdList[] = $data[$pkFieldName] ;
        }

        return implode ( ":" , $outputIdList );
    }

	public function getSum($campo){
		$this->setDados();
		foreach($this->dadosQuery as $linha){
			$total += $this->valor_us($linha[$campo]);
		}
		return number_format($total,2,',','.');
	}

	public function getSumOnly($campo){
		$this->setDados();
		$qtd=0;
		foreach($this->dadosQuery as $linha){
			$qtd++;
			$total += $linha[$campo];
		}
		return array($total,$qtd);
	}

	public function valor_us($valor){
	   $res_valor = str_replace(",","M",$valor);
	   $res_valor = str_replace(".","",$res_valor);
	   $res_valor = str_replace("M",".",$res_valor);

	   return $res_valor;

	}

	public function montaOptionComboPagina($numero){
        $options = '';
		for($i=1;$i<$numero+1;$i++){
			$options.='<option value="'.$i.'"';
			if($i==$this->paginaAtual){
				$options.='selected';
			}
			$options.='>Página '.$i.'</option>';
		}
		return $options;
	}

	public function numeroDePaginas($registros) {
		if ($registros > $this->linhasPorTabela){
			$numPagina = ceil($registros / $this->linhasPorTabela);
		}
		else{
			$numPagina = 1;
		}
		return $numPagina;
	} // retorna o numero de paginas que serão paginadas

	public function setFiltrosOrdem(){

		if ( isset ( $_GET['orderCampo' . $this->id] ) ) {

            $temp           = explode ( ':' , $_GET['orderCampo' . $this->id] ) ;
            $orderCampo     = isset ( $temp[0] ) ? $temp[0] : '' ;
            $orderTipo      = isset ( $temp[1] ) ? $temp[1] : '' ;
            $campoEncontrado = false ;
            foreach ( $this->getCampos () as $campo ) {
                if ( $campo->getNome () === $orderCampo ) {
                    $campoEncontrado = true ;
                    $campo->setOrder ( $orderTipo ) ;
                } else {
                    $campo->setOrder ( '' ) ;
                }
            }
            if ( $campoEncontrado ) $this->listaDb->setQueryOrder ( $orderCampo , $orderTipo ) ;
        } else {
            foreach ( $this->getCampos () as $campo ) {
                if ( $campo->getOrder () === '' ) continue ;
                $orderCampo = $campo->getNome () ;
                $orderTipo = $campo->getOrder () ;
            }
            if ( !empty($orderCampo) && !empty($orderTipo) ) {
            	$this->listaDb->setQueryOrder ( $orderCampo , $orderTipo ) ;
            } else {
            	$orderCampo = $orderTipo = "";
            }
        }

		return array("CAMPO"=>$orderCampo,"TIPO"=>$orderTipo);

	}

    /**
     * verifica se existe um campo mandatory e se ele foi preenchido
     * @return boolean
     */
    public function getMandatoryFilter ( ) {
        if ( !$this->flagHasMandatoryFilter ) return false ;

        foreach ( $this->filters as $filter ) {
            if ( $where = $filter->formatQueryFilter ( ) ) {
                if ( $filter->isMandatory ( ) && !empty ( $where ) ) {
                    return $where ;
                }
            }
        }

        return false ;
    }

    private function applyFilters ( ) {

        $whereList = array ( );

        if ( $where = $this->getMandatoryFilter ( ) ) {

            $this->where($where);
            $this->resetNoMandatoryFilters ( );

        } else {
            $this->_applyFilters ( );
        }

    }

    private function _applyFilters ( ) {
        foreach ( $this->filters as $filter ) {
            if ( $where = $filter->formatQueryFilter ( ) ) {
                $this->where ( $where );
            }
        }
    }

    private function resetNoMandatoryFilters ( ) {
        foreach ( $this->filters as $filter ) {
            if ( !$filter->isMandatory ( ) ) $filter->resetFilter ( );
        }
    }

    public function __toString ( ) {
        return $this->display();
    }

	public function display() {

        try {

            $smarty_ListaDados = new XSmarty();

            $this->applyFilters ( );

			$whereList = $this->listaDb->getWhereList ( ) ;
			if ( empty ( $whereList ) && $this->filterRequired ) throw new Xlib_XListaDados_Exception_EmptySearchFilter ( "Favor forne&ccedil;a ao menos um argumento de busca " );

            if ( !empty ( $this->permission ) && !$this->hasPermission ( ) ) {
                $message = "Desculpe, você não tem permissão suficiente para acessar o recurso solicitado." ;
                return '
                <div style="min-width:500px;background:#FFFAFA;padding:10px;min-height:100px;text-align:center;margin-left:auto;margin-right:auto;text-align:center;color:#F00;font-family:verdana;font-size:12px;margin:10px;">
					<div style="display:block;text-align:left;">
						<pre style="border:2px solid #D00;font-size:1.3em;color:#D00;padding:10px;"> <span class="glyphicon glyphicon-warning-sign"></span> ' . $message . '</pre>
					</div>
				</div>
                ' ;
            }


            $this->setFiltrosOrdem();

            if ( $this->ListaDadosDisplay !== null ) return $this->ListaDadosDisplay;

            $this->queryCount = $this->listaDb->queryCount();
            $this->setDados();
            $totalRegistros = $this->queryCount;
            $totalPaginas = ceil ( $this->queryCount / $this->linhasPorTabela ) ;

            $paginacao['inicio']=$paginacao['anterior']=$paginacao['proxima']=$paginacao['fim'] = '_off';

            if ( $this->paginaAtual > 1 ) {
                $paginacao['inicio'] = $paginacao['anterior'] = '_on' ;
            }

            if ( $this->paginaAtual < $totalRegistros / $this->linhasPorTabela ) {
                $paginacao['fim'] = $paginacao['proxima'] = '_on' ;
            }

            $linha=0;
            $checkboxValues=  array();

            foreach ( $this->dadosQuery as $registro ) {

            	$url = '' ;
                if ( sizeof ( $this->checkbox ) != 0 ) {
                    $checkboxValues[$linha]['valor'] = $registro[$this->checkbox['valor']] ;
                    $checkboxValues[$linha]['display'] = 'none' ;
                    if ( !empty ( $registro[$this->checkbox['campoComparacao']] ) ) {

                        eval ( "\$condicaoDisplay = '" . $registro[$this->checkbox['campoComparacao']] . "'" . $this->checkbox['condicao'] . ";" ) ;
                        if ( $condicaoDisplay ) {
                            $checkboxValues[$linha]['display'] = '' ;
                        }
                    }
                }

                /**
                 * CRIA OS PARAMETROS PARA ENVIAR PARA O BOTÃO ANTIGO
                 * @TODO: VERIFICAR A NECESSIDADE DO FOREACH ABAIXO E CODIGOS NA SEQUENCIA
                 *
                 */
                foreach ( $this->getCampos () as $campo ) {
                    if ( $campo->getTipoSaida () != "T" ) {
                        $url.=$campo->getNome () . "=" . str_replace ( "'" , " " , $registro[$campo->getNome ()] ) . "&" ;
                    }

                    if ( $campo->getTipoSaida () != "P" ) {
                        if ( $campo->getFormat () == "S" ) {
                            $dadosTabela[$linha][$campo->getNome ()] = number_format ( $registro[$campo->getNome ()] , 2 , '.' , '' ) ;
                        } else {
                            $dadosTabela[$linha][$campo->getNome ()] = $registro[$campo->getNome ()] ;
                        }
                    }

                    foreach ( $this->condicao as $key => $cond ) {
                        if ( !isset ( $condicao[$key] ) ) {
                            $condicao[$key]['condicao'] = trim ( $cond['condicao'] ) ;
                            $condicao[$key]['STATUS'] = FALSE ;
                        }
                        if ( eregi ( $campo->getNome () . " " , $condicao[$key]['condicao'] ) ) {
                            $condicao[$key]['condicao'] = str_replace ( $campo->getNome () , "'" . $registro[$campo->getNome ()] . "'" , $condicao[$key]['condicao'] ) ;
                            $condicao[$key]['STATUS'] = TRUE ;
                        }
                    }
                }
                $dadosTabela[$linha]["Style"] = "" ;
                if ( !empty ( $condicao ) ) {
                    foreach ( $condicao as $key => $cond ) {
                        if ( $cond['STATUS'] ) {
                            eval ( "\$resultcon = " . $cond['condicao'] . "; " ) ;
                            if ( $resultcon ) {
                                $dadosTabela[$linha]["Style"] = $this->condicao[$key]['style'] ;
                                if ( is_array ( $this->condicao[$key]['acao'] ) ) {
                                    $hiddenimg = $this->condicao[$key]['acao'] ;
                                }
                            }
                        }
                    }
                }

                unset ( $condicao ) ;

                foreach ( $this->getAcoes () as $acao ) {
                    $viewacao = true ;
                    if ( is_array ( $hiddenimg ) && in_array ( $acao->getImagem () , $hiddenimg ) ) $viewacao = false ;
                    $acao->setParametros ( substr ( $url , 0 , strlen ( $url ) - 1 ) ) ;
                    $acaoComLink = new XAcao ( $acao->getImagem () , $acao->getHref () , $acao->getAlt () , $acao->getLabel () , $acao->getOutros () , $acao->getParametros () , $viewacao ) ;
                    $dadosTabela[$linha]['acoes'][] = $acaoComLink ;
                    unset ( $acaoComLink ) ;
                }
                unset ( $hiddenimg ) ;
                $linha++ ;
            }

            foreach ( $this->getCampos () as $campo ) {
                if ( $campo->getTipoSaida () != "P" ) {
                    $temp = $campo ;
                    $camposTabela[] = $temp ;
                    $this->incrementTotColum () ;
                    unset ( $temp ) ;
                }
            }

            $optionsComboPagina = $this->montaOptionComboPagina ( $totalPaginas ) ;

            $smarty_ListaDados->assign("UsaPaginacao",$this->paginacao);
            $smarty_ListaDados->assign("ordenacao",$this->ordenacao);
            $smarty_ListaDados->assign("paginacao",$paginacao); // seta o status das imagens
            $smarty_ListaDados->assign("listaId",$this->id);
            $smarty_ListaDados->assign("arr_campos_exibicao",$camposTabela);
            $smarty_ListaDados->assign("order",$this->setFiltrosOrdem());
            $smarty_ListaDados->assign("optionsComboPagina",$optionsComboPagina);
            $smarty_ListaDados->assign('comboPagina',$this->comboPagina);
            $smarty_ListaDados->assign('UsaBackgroundIndex',$this->UsaBackgroundIndex);
            $smarty_ListaDados->assign ( 'query' , ModelAbstract::queryBeautifier ( $this->getQuery ( ) ) ) ;


            //$smarty_ListaDados->assign("corTrue",COR_TRUE);
            //$smarty_ListaDados->assign("corFalse",COR_FALSE);
            //$smarty_ListaDados->assign("corOnMouseOver",COR_OVER);

			$smarty_ListaDados->assign("tableClassList",$this->tableClassList);
			$smarty_ListaDados->assign("tableTitle",$this->tableTitle);

			if ( !isset ( $dadosTabela ) ) $dadosTabela = array ();
            $smarty_ListaDados->assign("arr_campos_exibicao_values",$dadosTabela);
            $smarty_ListaDados->assign("qry_pag_inicio",$this->paginaAtual);
            $smarty_ListaDados->assign("qry_pag_paginas",ceil($totalPaginas));
            $smarty_ListaDados->assign("qtd_linha",$linha);
            $smarty_ListaDados->assign("qry_pag_registros",$totalRegistros);
            $smarty_ListaDados->assign("checkboxValues",$checkboxValues);
            $smarty_ListaDados->assign("showLineCounter",$this->showLineCounter);

            $checkcol = (sizeof ( $this->checkbox ) != 0) ? 1 : 0 ;
            if ( !$this->showLineCounter ) $checkcol-- ;

            $smarty_ListaDados->assign("total_colunas", $this->totColum + $checkcol);
            $smarty_ListaDados->assign("AutoDetalhes", $this->AutoDetalhes);
            $smarty_ListaDados->assign("AutoDetalhesStr", $this->AutoDetalhesStr);
            $smarty_ListaDados->assign("checkbox", $this->checkbox);

            $smarty_ListaDados->assign("createPageLink", $this->createPageLink);	// link da página de inserir registro caso não tenha sido encontrado nenhum

            $db = ModelAbstract::getDB () ;

            $pagina = $_SERVER['REQUEST_URI'] ;

            if ( !isset ( $_GET['paginacao'.$this->id] ) )     $_GET['paginacao'.$this->id]   = '1';
            if ( !isset ( $_GET['orderCampo'.$this->id] ) )    $_GET['orderCampo'.$this->id]  = '1';
            $hidden =
                "<input type=\"hidden\" name=\"paginacao".$this->id."\" value=\"" . $_GET['paginacao'.$this->id]  . "\">" .
                "<input type=\"hidden\" name=\"orderCampo".$this->id."\" value=\"" . $_GET['orderCampo'.$this->id] . "\">" ;

            foreach ( $_GET as $key => $value ) {
                if ( $key === 'orderCampo' . $this->id || $key === 'paginacao' . $this->id ) continue ;
                $hidden .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' ;
            }

$javas =
'<form action="'.$pagina.'" method="get" name="'.$this->id.'" id="'.$this->id.'">' .
    $hidden .
'</form>' .
'<form action="'.$pagina.'" method="get" name="paginacaoform'.$this->id.'" id="paginacao'.$this->id.'">' .
    $hidden .
'</form>
<script language="javascript">

    function ordenacao'.$this->id.'(campo,ordem){
        document.'.$this->id.'.orderCampo'.$this->id.'.value = campo+":"+ordem;
        document.'.$this->id.'.submit();
    }

    function goToPage'.$this->id.'() {
        var paginaAtual = document.paginacaoform'.$this->id.'.paginacao'.$this->getId().'.value
        var proximaPagina = window.prompt("Digite o número da página" , paginaAtual ) ;

        proximaPagina = proximaPagina.replace(/[^\d]+/,\'\');
        if ( proximaPagina === "" ) return false ;
        proximaPagina = parseInt ( proximaPagina , 10 );

        if ( paginaAtual === proximaPagina ) return false ;
        if ( proximaPagina <= 0 ) return false ;
        if ( proximaPagina > '.$totalPaginas.' ) return false ;

        document.paginacaoform'.$this->id.'.paginacao'.$this->getId().'.value = proximaPagina;
        document.paginacaoform'.$this->id.'.submit();
    }

    function pagina'.$this->id.' (variavel){

        var paginaAtual = '.$this->paginaAtual.' ;

        if ( variavel === "inicio" ) {
            paginaAtual = 1;
        } else if ( variavel === "anterior" ) {
            paginaAtual--;
            if ( paginaAtual < 1 ) paginaAtual = 1 ;
        } else if ( variavel === "proximo" ) {
            paginaAtual++;
            if ( paginaAtual > '.$totalPaginas.' ) paginaAtual = '.$totalPaginas.' ;
        } else if ( variavel === "fim" ) {
            paginaAtual = '.$totalPaginas.';
        }

        document.paginacaoform'.$this->id.'.paginacao'.$this->getId().'.value = paginaAtual;
        document.paginacaoform'.$this->id.'.submit();
    }
</script>';

            $smarty_ListaDados->assign("javascriptListaDados",$javas);
            $template = empty ( $this->template ) ? XListaDados::$defaultTemplate : $this->template ;
            $ListaDadosDisplay = $smarty_ListaDados->getDisplay(dirname(__file__)."/view/".$template.".phtml");

            $this->ListaDadosDisplay = $ListaDadosDisplay;
            return $this->ListaDadosDisplay;

        } catch ( Xlib_XListaDados_Exception_EmptySearchFilter $err ) {

        	$smarty_ListaDados->assign ( 'mensagem' , $err->getMessage ( ) ) ;
            return $smarty_ListaDados->getDisplay(dirname(__file__)."/view/XListaDadosEmpty.phtml");

        } catch ( Exception $err ) {

            $backtrace = $errorMessage = $query = $errorDetail = $dbLastQuery = $dbAccess = $connectorReport = '' ;

            // $errorMessage = translateError ( $err->getMessage ( ) );
            $errorMessage = $err->getMessage ( ) ;

            if ( DEBUG ) {
                $backtrace          = print_r ( debug_backtrace ( ) , true ) ;
                $errorDetail        = $err->getTraceAsString ( ) ;
                $connectorReport    = ModelAbstract::dumpQueries();
                $dbLastQuery        = $this->listaDb->getDB ( )->last_query;
            }

            $smarty_ListaDados->assign ( 'connectorReport' , $connectorReport ) ;
            $smarty_ListaDados->assign ( 'dbLastQuery' , $dbLastQuery ) ;
            $smarty_ListaDados->assign ( 'debug' , DEBUG ) ;
            $smarty_ListaDados->assign ( 'errorMessage' , $errorMessage ) ;
//            $smarty_ListaDados->assign ( 'errorMessage' , translateError ( $errorMessage ) ) ;
            $smarty_ListaDados->assign ( 'errorDetail' , $errorDetail ) ;
            $smarty_ListaDados->assign ( 'backtrace' , $backtrace ) ;

            return $smarty_ListaDados->getDisplay(dirname(__file__)."/view/XListaDadosException.phtml");

        }

	}


	/**
	 * Incrementa o total de colunas em this->totColum
	 *
	 */
	public function incrementTotColum() {
		$this->totColum++;
	}
	/**
	 * Incrementa o total de colunas em this->totColum
	 * @params integer number Numero a ser colocado em totColum
	 */
	public function setValueTotColum($number = 0) {
		if(!is_numeric($number))
			$number = 0;
		$this->totColum = $number;
	}
	/**
	 * Incrementa o total de colunas em this->totColum
	 * @return integer Numero a ser colocado em totColum
	 */
	public function getTotColum() {
		return $this->totColum;
	}

	/**
	 * Funcao responsavel para uso do detalhes por Ajax
	 * @param parametro de definicao
	 */
	public function setAutoDetalhes($param = FALSE){
		if(!is_bool($param)){
			$param = FALSE;
		}

		$this->AutoDetalhes = $param;
	}

	/**
	 * Retorno de variavel $this->AutoDetalhes
	 * @return booleano
	 */
	public function getAutoDetalhes(){
		return $this->AutoDetalhes;
	}

	public function setAutoDetalhesArray($param = array()){
		$this->AutoDetalhes = TRUE;
		if(!isset($param) || !is_array($param) || !count($param)) {
			$this->AutoDetalhesStr = "<div id=\"edresults#index#\" style=\"display:none;width:735;position:relative;border:1px solid #CC0000;\">
										<iframe align=\"center\" frame name=\"meio#index#\" src=\"\" width=\"100%\" height=\"165\" scrolling=\"auto\" frameborder=\"0\">
										</iframe>
									  </div>";
		} else {
			$this->AutoDetalhesStr = $this->readArrayDetalhes($param);
		}
	}

    /**
     *
     * @return type
     */
    public function getQueryCount ( ) {
        return $this->queryCount;
    }

	/**
	 * Interpreta um array com os parametros para formar o html
	 * @param array params Array com os parametros para preencher o html
	 */
	public function readArrayDetalhes($params) {
		if ( !isset ( $params ) || !is_array ( $params ) || !count ( $params ) ) return false ;

		$parametros = $subitem = $retorno = "";
		foreach($params as $key=>$val) {
			if($key == "tipo") {
				$retorno .= "<".$val ." %param%>%subitem%</".$val.">";
			} else if($key == "parametros") {
				foreach($val as $key1=>$val1)
					$parametros .= $key1.'="'.$val1.'" ';
			} else if($key == "subitem") {
				$subitem = $this->readArrayDetalhes($val);
			}
		}
		$retorno = str_replace("%param%", $parametros, $retorno);
		$retorno = str_replace("%subitem%", $subitem, $retorno);
		return $retorno;
	}

	public function montaStrAutoDetalhes($param){
		foreach ($param as $key=>$val){
			if($key == 'tipo'){
				$tipo = $val;
				$strLinha = "<".$tipo;
			}

			if($key == 'parametros') {
				foreach($val as $key1=>$val1){
					$strLinha .= " $key1=\"$val1\" ";
				}
			}
		}

		$strLinha .= "></$tipo>";

		return $strLinha;
	}


    public function setClass ( Array $newTableClassList ) {
        $this->tableClassList = $newTableClassList ;
		return $this;
    }

    public function removeClass ( $class ) {
//        @TODO
		//return $this;
    }

    public function addClass ( $class ) {
        $this->tableClassList[] = $class ;
		return $this;
    }

    public function setTableTitle ( $title ) {
        $this->tableTitle = $title ;
        return $this;
    }



}// fim classe
