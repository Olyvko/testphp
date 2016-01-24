<?php

class ectransaction extends ecommerce
{

    /**
     * Transactions Id
     *
     * @var string
     */
    public $_sTransactionsId = null;
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'transactions';

    /**
     * Current key name
     *
     * @var string
     */
    protected $_sKeyName = 'id';

    /**
     *  sets Transactions id
     *
     * @return null
     */
    public function TransactionBegin()
    {
        $this->_sTransactionsId = substr(md5(time()), 0, 32);
    }


    public function TransactionAdd($sTable, $sField, $sValue)
    {/*
        if (!$this->_sTransactionsId) return false;

        $this->assignECommerce(
            array(
                "transactions_id" => $this->_sTransactionsId,
                "table_name" => $sTable,
                "key_name" => $sField,
                "key_value" => $sValue
            )
        );
        $this->buildInsertStringECommerce();
        $this->saveECommerce();
*/
    }

    public function TransactionRollBack()
    {/*
        if (!$this->_sTransactionsId) return false;

        $aTransactions = $this->getArrayECommerce($this->buildSelectStringECommerce(array("transactions_id" => $this->_sTransactionsId)));
        foreach($aTransactions as $aTransaction){
            $this->deleteByFieldECommerce($aTransaction[2],$aTransaction[3],$aTransaction[4]);
            $this->TransactionCommit();
        }
*/
    }

    function DeleteTransaction()
    {
        if (!$this->_sTransactionsId) return false;

        $this->deleteByFieldECommerce($this->_sClassName,"transactions_id",$this->_sTransactionsId);

        $this->_sTransactionsId = null;
    }

    public function TransactionCommit()
    {
        $this->DeleteTransaction();

    }
}
