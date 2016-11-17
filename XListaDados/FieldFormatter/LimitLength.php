<?php

class Xlib_XListaDados_FieldFormatter_LimitLength extends Xlib_XListaDados_FieldFormatterAbstract {

    private $limit ;
    protected static $callId = 0 ;

    public function __construct ( $limit ) {
        $this->limit = $limit ;
    }

    public function format ( $dataIn ) {

        if ( gettype ( $this->limit ) === "integer" ) {
            if ( strlen ( $dataIn ) > $this->limit ) {
                return $this->limitStringByCharLen ( $dataIn , $this->limit ) ;
            }
        } else if ( gettype ( $this->limit ) === "string" ) {
            return $this->limitStringByCssWidth ( $dataIn , $this->limit ) ;
        }

        return  $dataIn ;
    }

    private function limitStringByCharLen ( $string , $charLen ) {
        $limitedData = substr ( $string , 0 , $charLen ) ;
        Xlib_XListaDados_FieldFormatter_LimitLength::$callId++;

        return "<span style=\"cursor:help;\" title=\"" . $string . "\" >" . $limitedData . "...</span>" ;
//        "<script>\n" .
//        "$(function(){\n" .
//        "   $(\"#Xlib_XListaDados_FieldFormatter_LimitLengthTooltip".Xlib_XListaDados_FieldFormatter_LimitLength::$callId."\").tooltip({placement:'bottom'});\n" .
//        "   $(\"#Xlib_XListaDados_FieldFormatter_LimitLengthTooltip".Xlib_XListaDados_FieldFormatter_LimitLength::$callId."\").tooltip({placement:'bottom',trigger:'click'});\n" .
//        "});\n" .
//        "</script>\n" ;
    }

    private function limitStringByCssWidth ( $string , $charLen ) {
        return "<a style=\"width:$charLen;overflow:hidden;\" title=\"" . htmlentities ( $dataIn ) . "\" href=\"javascript:;\">" . $limitedData . "...</a>";
    }


}