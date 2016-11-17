<?php

class Xlib_XListaDados_FieldFilter_CPF extends Xlib_XListaDados_FieldFilterAbstract {

	private $punct = true;

	public function getInstance ( $field , $label = "" , $punct = false ) {
		return new Xlib_XListaDados_FieldFilter_CPF ( $field , $label , $punct );
	}

    public function formatQueryFilter ( ) {
        $value = Request::get($this->filterContainer."[".$this->field."]");
        if ( $value === "" ) return null ;
        if ( !$this->punct ) $value = preg_replace ( '/[^\d]+/' , '' , $value );
        return eval ( "return \"" . $this->template . "\" ;") ;
    }

	public function getPunct ( ) {
		return $this->punct;
	}

	/**
	 * Remove a pontuação para buscar no banco
	 * @param [type] $punct [description]
	 */
	public function setPunct ( $punct ) {
		$this->punct = (bool) $punct ;
		return $this;
	}

	/**
	 *
	 * @param [type]  $field [description]
	 * @param string  $label [description]
	 * @param boolean $punct false se o CPF é salvo no banco sem pontuação. true se é salvo com pontuação
	 */
    public function __construct ( $field , $label = "" , $punct = false ) {
        $this->setField ( $field );
        $this->setLabel ( $label );
        $this->setPunct ( $punct );
    }

    public function getFieldFormatter ( ) {

        $script =
            'var tmp=this.value.replace(/[^\d]+/g,\'\');console.log(tmp);switch(tmp.length){case 0:case 1:case 2:case 3:return;case 4:case 5:case 6:this.value=tmp.replace(/^(\d{3})(\d+)/,\'$1.$2\');return;case 7:case 8:case 9:this.value=tmp.replace(/^(\d{3})(\d{3})(\d+)/,\'$1.$2.$3\');return;default:this.value=tmp.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})(.*)/,\'$1.$2.$3-$4\')}'
        ;
        return $script ;
    }

    /**
     * Muitas vezes você vai querer sobrescrever este método
     * @return type
     */
    public function __toString ( ) {

        $output         = $this->getSufix() ;
        $attributeSet   = $this->getAttributeSet ( );
        $attributeSet['style'] = empty ( $attributeSet['style'] ) ? "width:125px" : $attributeSet['style'] . ";width:125px" ;
        $attributeSet['placeholder']    = "___.___.___-__";
        $attributeSet['onKeyPress']     = $this->getFieldFormatter();
        $attributeSet['onKeyUp']        = $this->getFieldFormatter();
        $attributeSet['onKeyDown']      = $this->getFieldFormatter();
        $attributeSet['maxlength']      = '14';


        $labelAttributeSet = array ( 'for' => $attributeSet['id'] , "class" => "control-label" );
        if ( empty ( $this->label ) ) $labelAttributeSet['class'] = 'sr-only';

        $output .= "<div class=\"form-group\">";
        $output .= "<label " . $this->getAttributeSetAsString ( $labelAttributeSet ) . " >" . $this->label . "</label><br/>" ;
        $output .= "<input " . $this->getAttributeSetAsString ( $attributeSet ) . " >";
        $output .= "</div>";

        // $output .= "
        // <script>
        //     $(function(){
        //         $(\"#".$attributeSet['id']."\").datepicker();
        //     });
        // </script>
        // " ;

        $output .= $this->getPrefix ();
        return $output;

    }

}