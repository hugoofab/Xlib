<?php

class XCreateData extends ModelAbstract {
	
    protected $fieldList = array ( );
    protected $tableName = "";
    protected $sequence = "";
    protected $primaryKey = "";
    protected $template = "";
    
    public function setTemplate ( $template ) {
        $this->template = $template ;
        return $this;
    }

    public function setTableName ( $tableName ) {
        $this->tableName = $tableName ;
        return $this;
    }

    public function setSequence ( $sequence ) {
        $this->sequence = $sequence ;
        return $this;
    }

    public function setPrimaryKey ( $primaryKey ) {
        $this->primaryKey = $primaryKey ;
        return $this;
    }

    public function __toString ( ) {
        
        $view = new XSmarty ( );
        
//        $output = "";
//        foreach ( $this->fieldList as $field ) {
//            $output .= $field ;
//        }
//        $view->assign ( 'output' , $output ) ;        
        
        $view->assign ( 'fieldList' , $this->fieldList ) ;
        
        return $view->getDisplay();
        
    }
    
    public function __construct ( $tableName = "" , $primaryKey = "" , $sequence = "" ) {
        
        $this->setTableName  ( $tableName ) ;
        $this->setPrimaryKey ( $primaryKey ) ;
        $this->setSequence  ( $sequence ) ;
        
        // pegar informações sobre a tabela e seus campos
        
    }
      
    public static function getInstance ( $tableName = "" , $primaryKey = "" , $sequence = "" ) {
        return new XCreateData ( $tableName = "" , $primaryKey = "" , $sequence = "" );
    }
    
    public function addField ( $fieldKey , Xlib_XListaDados_InsertFieldsAbstract $fieldObject ) {
        $this->fieldList[] = $field ;
        return $this;
    }
        
}
