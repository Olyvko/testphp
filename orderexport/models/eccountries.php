<?php

class eccountry extends ecommerce {

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'countries';

    /**
     * Builds and returns SQL query string.
     *
     * @return string
     */
    public function buildSelectStringECommerce($aWhere = null, $sAndOr = " AND ")
    {
        $sSelect  = "SELECT * FROM $this->_sClassName where 1";

        if ($aWhere) {
            $sWhereAdd = "";
            reset($aWhere);
            while (list($name, $value) = each($aWhere)) {
                $sWhereAdd .="md5($this->_sClassName.$name) = '$value' $sAndOr";
            }

            $sWhereAdd = trim($sWhereAdd, $sAndOr);
            $sWhereAdd = " AND ( ".$sWhereAdd." )";
        }

        $sSelect .=$sWhereAdd;

        $this->_sQueryString  = $sSelect;
        return $this->_sQueryString;
    }
}
