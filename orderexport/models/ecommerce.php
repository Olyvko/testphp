<?php

/**
 * Class ecommerce - base class for ECommerce
 */
class ecommerce extends oxBase
{

    /**
     * Field name list
     *
     * @var array
     */
    protected $_aCommFieldNames = array();

    /**
     * Query for request to DB
     *
     * @var string
     */
    protected $_sQueryString = '';

    /**
     * Error message
     *
     * @var string
     */
    protected $_sErroMsg = '';

    /**
     * Inserting key
     *
     * @var null
     */
    protected $_iId = null;

    /**
     * Get value
     *
     * @return String - Class name
     */
    public function getClassNameECommerce()
    {
        return $this->_sClassName;
    }

    /**
     * Get value
     *
     * @return String - Error message
     */
    public function getErroMsqECommerce()
    {
        return $this->_sErroMsg;
    }

    /**
     * Get value
     *
     * @return String - Key field name
     */
    public function getKeyNameECommerce()
    {
        return $this->_sKeyName;
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getRowECommerce()
    {
        return zsDb::getDb()->getRow($this->_sQueryString);
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getArrayECommerce()
    {
        return zsDb::getDb()->getAll($this->_sQueryString);

    }

    /**
     * Save to database.
     * @return bool
     */
    public function saveECommerce()
    {
        $blRet = false;

        if ($this->_sQueryString != '') {
            try {
                $blRet = zsDb::getDb()->execute($this->_sQueryString);
            } catch (Exception $e) {
                $this->_sErroMsg = zsDb::getDb(true)->errorMsg() . " IN QUERY: " . $this->_sQueryString . PHP_EOL;
                return $blRet;
            }
        }

        if (mysql_insert_id() != 0)
            $this->_iId = mysql_insert_id();

        return $blRet;

    }

    /**
     * Delete  from the database, returns true on success.
     *
     * @param $sTable
     * @param null $sField
     * @param null $sId
     *
     * @return bool
     */
    public function deleteByFieldECommerce($sTable,$sField = null,$sId = null)
    {
        if (!$sId || !$sField) {
            return false;
        }

        zsDb::getDb()-$this->execute("delete from {$sTable} where {$sField} = '{$sId}'");

        return true;
    }

    /**
     * Delete  from the database, returns true on success.
     *
     * @param null $sId
     */
    public function deleteECommerce($sId = null)
    {
        if (!$sId) {
            return false;
        }

        zsDb::getDb()->execute("delete from {$this->_sClassName} where {$this->_sKeyName} = '{$sId}'");

        return true;
    }

    /**
     * Write error message to file
     * @param $msg
     */
    public function errorWriteMsg($msg)
    {

        $fStream = fopen(getShopBasePath() . DIR_SEP . 'log/exportcomm.log', 'a');
        fprintf($fStream, $msg);
    }

    /**
     * Return last inserting key
     *
     * @return null
     */
    public function getIdECommerce()
    {
        return $this->_iId;
    }

    /**
     * Builds and returns SQL query string for Replace.
     *
     * @return string
     */
    public function buildReplaceStringECommerce()
    {
        $sColumns = "";
        $sValues = "";

        foreach ($this->_aCommFieldNames as $key => $value) {
            $sColumns = $sColumns . $key . ",";
            $sValues = $sValues . "'" . mysql_real_escape_string($value) . "',";
        }

        $sColumns = trim($sColumns, ",");
        $sValues = trim($sValues, ",");

        $this->_sQueryString = "REPLACE INTO $this->_sClassName ($sColumns) VALUES  ($sValues)";

        return $this->_sQueryString;
    }

    /**
     * Builds and returns SQL query string for Insert.
     *
     * @return string
     */
    public function buildInsertStringECommerce()
    {
        $sColumns = "";
        $sValues = "";

        foreach ($this->_aCommFieldNames as $key => $value) {
            $sColumns = $sColumns . $key . ",";
            $sValues = $sValues . "'" . mysql_real_escape_string($value) . "',";
        }

        $sColumns = trim($sColumns, ",");
        $sValues = trim($sValues, ",");

        $this->_sQueryString = "INSERT INTO $this->_sClassName ($sColumns) VALUES  ($sValues)";

        return $this->_sQueryString;
    }

    /**
     * Builds and returns SQL query string for Select.
     *
     * @return string
     */
    public function buildSelectStringECommerce($aWhere = null, $sAndOr = " AND ")
    {
        $sSelect = "SELECT * FROM $this->_sClassName where 1";

        if ($aWhere) {
            $sWhereAdd = "";
            reset($aWhere);
            while (list($name, $value) = each($aWhere)) {
                $sWhereAdd .= "$this->_sClassName.$name = '$value' $sAndOr";
            }

            $sWhereAdd = trim($sWhereAdd, $sAndOr);
            $sWhereAdd = " AND ( " . $sWhereAdd . " )";
        }

        $sSelect .= $sWhereAdd;

        $this->_sQueryString = $sSelect;
        return $this->_sQueryString;
    }

    /**
     * Assigns DB field values to fields. Returns true on success.
     *
     * @param array $dbRecord Associative data values array
     *
     * @return null
     */
    public function assignECommerce($dbRecord)
    {
        if (!is_array($dbRecord)) {
            return;
        }

        reset($dbRecord);
        while (list($sName, $sValue) = each($dbRecord)) {
            $this->_setECommerceFieldData($sName, $sValue);
        }
        return true;

    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name of a data field to set
     * @param string $sValue value of data field
     *
     */
    protected function _setECommerceFieldData($sFieldName, $sValue)
    {
        $this->_aCommFieldNames[$sFieldName] = $sValue;
    }
}
