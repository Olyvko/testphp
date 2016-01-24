<?php

class ecproduct extends ecommerce {

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'products';

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


    /**
     * Builds and returns SQL query string.
     *
     * @return string
     */
    public function UpdateECommerce($aProducts)
    {
        foreach ($aProducts as $aProduct) {

            $products_id = $aProduct['id'];
            $product_quant = $aProduct['quantity'];
            $sUpdate = "UPDATE $this->_sClassName SET products_quantity = products_quantity - '$product_quant' where products_id  = '$products_id'";
            $this->_sQueryString  = $sUpdate;
            $this->saveECommerce();
        }
        return $this->_sQueryString;
    }

}
