<?php

class orderexport_ecommerce extends oxAdminView
{
    /**
     *  object transactions
     * @var null
     */
    protected $_oTransactions = null;

    /**
     * object oxorder
     * @var null
     */
    protected $_oOrderCheck = null;

    /**
     * admin mail template
     * @var string
     */
    protected $_sEmailTemplate = "email2admin.tpl";

    /**
     * @return string
     */
    public function render()
    {
        parent::render();
    }

    /**
     * Export customer to e commerce
     *
     * @param $oOrder
     * @param $oUser
     * @return bool|null
     */
    protected function _exportCustomers($oOrder,$oUser)
    {
        $sCustomerId = null;

        // Search User in e commerce by email
        $oCommersCustomers = oxNew('eccustomer');
        $aResultCustomer = $oCommersCustomers->getRowECommerce($oCommersCustomers->buildSelectStringECommerce(array("customers_email_address" => $oOrder->oxorder__oxbillemail->value)));

        //If user adsent in e commerce bd
        if ($aResultCustomer == null) {

            $oCommersCustomers->assignECommerce(
                array(
                    "customers_gender" => ($oUser->oxuser__oxsal->value == "MR") ? "m" : "f",
                    "customers_firstname" => $oUser->oxuser__oxfname->value,
                    "customers_lastname" => $oUser->oxuser__oxlname->value,
                    "customers_email_address" => $oUser->oxuser__oxusername->value,
                    "customers_default_address_id" => 0,
                    "customers_shipping_adress_id" => 0,
                    "customers_telephone" => 0,
                    "date_registration" => date('Y.m.d H:i:s'),
                    "date_last_update" => date('Y.m.d H:i:s'),
                    "customers_activated" => "NO",
                )
            );

            if ($oCommersCustomers->saveECommerce()) {

                $sCustomerId = $oCommersCustomers->getIdECommerce();
                $this->_oTransactions->TransactionAdd($oCommersCustomers->getClassNameECommerce(), $oCommersCustomers->getKeyNameECommerce(), $sCustomerId);

            } else {
                $this->_errorWriteMsg($oCommersCustomers);
                $this->_oTransactions->TransactionRollBack();
                return false;
            }

        } else {
            $sCustomerId = $aResultCustomer[0];
        }

        return $sCustomerId;
    }

    /**
     * Export customer info to e commerce
     *
     * @param $sCustomerId
     * @return bool
     */
    protected function _exportCustomerinfo($sCustomerId)
    {
        $oCustomersInfo = oxNew('eccustomersinfo');
        $oCustomersInfo->assignECommerce(
            array(
                "customers_id" => $sCustomerId,
                "customers_info_date_account_created" => date('Y.m.d H:i:s'),
            )
        );

        $oCustomersInfo->buildReplaceStringECommerce();
        if($oCustomersInfo->saveECommerce())
            return true;

    }

    /**
     * Get country id
     *
     * @param $oOrder
     * @return bool|null
     */
    protected function _getCounty($oOrder)
    {
        $country_id = null;

        $oCommersCountries = oxNew('eccountry');
        if($oOrder->oxorder__oxdelcountryid->value!=null)
            $sCountry = $oOrder->oxorder__oxdelcountryid->value;
        else
            $sCountry = $oOrder->oxorder__oxbillcountryid->value;

        $aCommerceCountry = $oCommersCountries->getRowECommerce($oCommersCountries->buildSelectStringECommerce(array("countries_id" => $sCountry)));
        if ($aCommerceCountry != null) {
            $country_id = $aCommerceCountry[0];
        } else {
            $this->_errorWriteMsg(null,"Error. No such country in e commerce shop");
            $this->_oTransactions->TransactionRollBack();
            return false;
        }

        return $country_id;
    }

    /**
     * Export address book to e commerce
     *
     * @param $sCustomerId
     * @param $aUserInfo
     * @param $country_id
     * @param $oOrder
     * @return bool
     */
    protected function _exportAddressBook($sCustomerId, $aUserInfo, $country_id, $oOrder)
    {

        $oCommersAddressBook = oxNew('ecaddressbook');
        $oCommersAddressBook->assignECommerce(
            array(
                "customers_id" => $sCustomerId,
                "entry_company" => $aUserInfo["delivery"]["company"],
                "entry_firstname" => $aUserInfo["delivery"]["fname"],
                "entry_lastname" => $aUserInfo["delivery"]["lname"],
                "entry_email" => $oOrder->oxorder__oxbillemail->value,
                "entry_street_address" => $aUserInfo["delivery"]["street_address"],
                "entry_postcode" => $aUserInfo["delivery"]["postcode"],
                "entry_city" => $aUserInfo["delivery"]["city"],
                "entry_country_id" => $country_id,
                "entry_zone_id" => 0
            )
        );
        $oCommersAddressBook->buildInsertStringECommerce();

        if ($oCommersAddressBook->saveECommerce()){
            $this->_oTransactions->TransactionAdd($oCommersAddressBook->getClassNameECommerce(), $oCommersAddressBook->getKeyNameECommerce(), $oCommersAddressBook->getIdECommerce());
        } else{
            $this->_errorWriteMsg($oCommersAddressBook);
            $this->_oTransactions->TransactionRollBack();
            return false;
        }

        return true;
    }

    /**
     * Export order to e commerce
     *
     * @param $sCustomerId
     * @param $aUserInfo
     * @param $oPayment
     * @param $oOrder
     * @return bool|null
     */
    protected function _exportOrder($sCustomerId, $aUserInfo, $oPayment, $oOrder)
    {
        $sCommersOrderId = null;

        $oCommersOrders = oxNew('ecorder');
        $oCommersOrders->assignECommerce(
            array(
                "customers_id" => $sCustomerId,
                "customers_name" => $aUserInfo["delivery"]["fname"] . " " . $aUserInfo["delivery"]["lname"],
                "customers_company" => $aUserInfo["delivery"]["company"],
                "customers_street_address" => $aUserInfo["delivery"]["street_address"],
                "customers_suburb" => "",
                "customers_city" => $aUserInfo["delivery"]["city"],
                "customers_postcode" => $aUserInfo["delivery"]["postcode"],
                "customers_state" => "",//TTTTT
                "customers_country" => $aUserInfo["delivery"]["country"],
                "customers_telephone" => $aUserInfo["delivery"]["telephone"],
                "customers_email_address" => $oOrder->oxorder__oxbillemail->value,
                "customers_address_format_id" => 5,
                "delivery_name" => $aUserInfo["delivery"]["fname"] . " " . $aUserInfo["delivery"]["lname"],
                "delivery_company" => $aUserInfo["delivery"]["company"],
                "delivery_street_address" => $aUserInfo["delivery"]["street_address"],
                "delivery_suburb" => "",
                "delivery_city" => $aUserInfo["delivery"]["city"],
                "delivery_postcode" => $aUserInfo["delivery"]["postcode"],
                "delivery_state" => "",
                "delivery_country" => $aUserInfo["delivery"]["country"],
                "delivery_address_format_id" => 5,
                "billing_name" => $aUserInfo["billing"]["fname"] . " " . $aUserInfo["billing"]["lname"],
                "billing_company" => $aUserInfo["billing"]["company"],
                "billing_street_address" => $aUserInfo["billing"]["street_address"],
                "billing_suburb" => "",
                "billing_city" => $aUserInfo["billing"]["city"],
                "billing_postcode" => $aUserInfo["billing"]["postcode"],
                "billing_state" => "",
                "billing_country" => $aUserInfo["billing"]["country"],
                "billing_address_format_id" => 5,
                "payment_method" => $oPayment->oxpayments__oxdesc->value,
                "last_modified" => date('Y.m.d H:i:s'),
                "date_purchased" => date('Y-m-d H:i:s', strtotime($oOrder->oxorder__oxorderdate->value)),
                "orders_status" => 1,
                "currency" => $oOrder->oxorder__oxcurrency->value,
                "currency_value" => 1
            )
        );

        $oCommersOrders->buildInsertStringECommerce();
        if ($oCommersOrders->saveECommerce()){
            $sCommersOrderId = $oCommersOrders->getIdECommerce();
            $this->_oTransactions->TransactionAdd($oCommersOrders->getClassNameECommerce(), $oCommersOrders->getKeyNameECommerce(), $sCommersOrderId);
        }
        else{
            $this->_errorWriteMsg($oCommersOrders);
            $this->_oTransactions->TransactionRollBack();
            return false;
        }

        return $sCommersOrderId;

    }

    /**
     * Export order articles to e commerce
     *
     * @param $oOrder
     * @param $sCommersOrderId
     * @return array|bool
     */
    protected function _exportOrderArticles($oOrder,$sCommersOrderId)
    {
        // Connect to oxid db
        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC);
        $products_update = array();
        $oOrderArticles = $oOrder->getOrderArticles();

        // Connect to e commerce db
        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC, true);

        foreach ($oOrderArticles as $oOrderArticle) {

            $sArtId = $oOrderArticle->getProductId();


            //Search product in e commerce
            $oCommersProducts = oxNew('ecproduct');
            $oCommersProducts->buildSelectStringECommerce(array("products_id" => $sArtId));
            $aCommersProduct = $oCommersProducts->getRowECommerce();

            //if product exist
            if ($aCommersProduct) {
                $sCommerceArtId = $aCommersProduct[0];
                $products_update[$sCommerceArtId] = array(
                    'id' => $sCommerceArtId,
                    'quantity' => $oOrderArticle->oxorderarticles__oxamount->value
                );

            } else {
                $oCommersProducts->errorWriteMsg("Error. No such product in e commerce shop");
                $this->_oTransactions->TransactionRollBack();
                return false;
            }

            $oCommersOrdersProducts = oxNew('ecordersproducts');
            $oCommersOrdersProducts->assignECommerce(
                array(
                    "orders_id" => $sCommersOrderId,
                    "products_id" => $sCommerceArtId,
                    "products_model" => "",
                    "products_name" => $oOrderArticle->oxorderarticles__oxtitle->value,
                    "products_price" => $oOrderArticle->oxorderarticles__oxnetprice->value,
                    "final_price" => $oOrderArticle->oxorderarticles__oxnetprice->value,
                    "products_tax" => 19,
                    "products_quantity" => $oOrderArticle->oxorderarticles__oxamount->value
                )
            );

            $oCommersOrdersProducts->buildInsertStringECommerce();
            if ($oCommersOrdersProducts->saveECommerce()) {
                $this->_oTransactions->TransactionAdd($oCommersOrdersProducts->getClassNameECommerce(), $oCommersOrdersProducts->getKeyNameECommerce(), $oCommersOrdersProducts->getIdECommerce());
            } else {
                $this->_errorWriteMsg($oCommersOrdersProducts);
                $this->_oTransactions->TransactionRollBack();
                return false;
            }

            return $products_update;
        }
    }

    /**
     * Export order total to e commerce
     *
     * @param $sCommersOrderId
     * @param $title
     * @param $text
     * @param $value
     * @param $class
     * @param $sort_order
     * @return bool
     */
    protected function _exportOrderTotal($sCommersOrderId, $title, $text, $value, $class, $sort_order)
    {
        $oCommersOrdersTotal = oxNew('ecorderstotal');
        $oCommersOrdersTotal->assignECommerce(
            array(
                "orders_id" => $sCommersOrderId,
                "title" => $title,
                "text" => $text,
                "value" => $value,
                "class" => $class,
                "sort_order" => $sort_order
            )
        );

        $oCommersOrdersTotal->buildInsertStringECommerce();
        if ($oCommersOrdersTotal->saveECommerce()) {
            $this->_oTransactions->TransactionAdd($oCommersOrdersTotal->getClassNameECommerce(), $oCommersOrdersTotal->getKeyNameECommerce(), $oCommersOrdersTotal->getIdECommerce());
        } else {
            $this->_errorWriteMsg($oCommersOrdersTotal);
            $this->_oTransactions->TransactionRollBack();
            return false;
        }

       return true;

    }

    /**
     * Export order status history to e commerce
     *
     * @param $sCommersOrderId
     * @return bool
     */
    protected function _exportOrdersStatusHistory($sCommersOrderId)
    {
        $strComment = "Import from Oxid, Order No. " . $sCommersOrderId;

        $oCommersOrdersStatusHistory = oxNew('ecorderstatushistory');
        $oCommersOrdersStatusHistory->assignECommerce(
            array(
                "orders_id" => $sCommersOrderId,
                "orders_status_id" => 1,
                "date_added" => date('Y.m.d H:i:s'),
                "customer_notified" => 0,
                "comments" => $strComment,
                "modified_by" => 96
            )
        );

        $oCommersOrdersStatusHistory->buildInsertStringECommerce();
        if ($oCommersOrdersStatusHistory->saveECommerce()) {
            $this->_oTransactions->TransactionAdd($oCommersOrdersStatusHistory->getClassNameECommerce(), $oCommersOrdersStatusHistory->getKeyNameECommerce(), $oCommersOrdersStatusHistory->getIdECommerce());
        } else {
            $this->_errorWriteMsg($oCommersOrdersStatusHistory);
            $this->_oTransactions->TransactionRollBack();
            return false;
        }


        return true;

    }

    /**
     * Return User info from Order
     *
     * @param null $oOrder
     * @return array
     */
    protected function _getUserInfoOxid($oOrder = null)
    {
        $aUserInfo = array("delivery" => array(), "billing" => array());
        if ($oOrder != null) {
            if ($oOrder->oxorder__oxdelstreet->value != '' || $oOrder->oxorder__oxdelcity->value != '' || $oOrder->oxorder__oxdelcountryid->value != ''
                || $oOrder->oxorder__oxdelfname->value != '' || $oOrder->oxorder__oxdellname->value != ''
            ) {
                $aUserInfo["delivery"]["fname"] = $oOrder->oxorder__oxdelfname->value;
                $aUserInfo["delivery"]["lname"] = $oOrder->oxorder__oxdellname->value;
                $aUserInfo["delivery"]["company"] = $oOrder->oxorder__oxdelcompany->value;
                $aUserInfo["delivery"]["street_address"] = $oOrder->oxorder__oxdelstreet->value . " " . $oOrder->oxorder__oxdelstreetnr->value;
                $aUserInfo["delivery"]["city"] = $oOrder->oxorder__oxdelcity->value;
                $aUserInfo["delivery"]["postcode"] = $oOrder->oxorder__oxdelzip->value;
                $aUserInfo["delivery"]["country"] = "Österreich";//$oOrder->oxorder__oxdelcountry->value;
                $aUserInfo["delivery"]["telephone"] = $oOrder->oxorder__oxdelfon->value;

            } else {

                $aUserInfo["delivery"]["fname"] = $oOrder->oxorder__oxbillfname->value;
                $aUserInfo["delivery"]["lname"] = $oOrder->oxorder__oxbilllname->value;
                $aUserInfo["delivery"]["company"] = $oOrder->oxorder__oxbillcompany->value;
                $aUserInfo["delivery"]["street_address"] = $oOrder->oxorder__oxbillstreet->value . " " . $oOrder->oxorder__oxbillstreetnr->value;
                $aUserInfo["delivery"]["city"] = $oOrder->oxorder__oxbillcity->value;
                $aUserInfo["delivery"]["postcode"] = $oOrder->oxorder__oxbillzip->value;
                $aUserInfo["delivery"]["country"] = "Österreich";//$oOrder->oxorder__oxbillcountry->value;
                $aUserInfo["delivery"]["telephone"] = $oOrder->oxorder__oxbillfon->value;
            }

            $aUserInfo["billing"]["fname"] = $oOrder->oxorder__oxbillfname->value;
            $aUserInfo["billing"]["lname"] = $oOrder->oxorder__oxbilllname->value;
            $aUserInfo["billing"]["company"] = $oOrder->oxorder__oxbillcompany->value;
            $aUserInfo["billing"]["street_address"] = $oOrder->oxorder__oxbillstreet->value . " " . $oOrder->oxorder__oxbillstreetnr->value;
            $aUserInfo["billing"]["city"] = $oOrder->oxorder__oxbillcity->value;
            $aUserInfo["billing"]["postcode"] = $oOrder->oxorder__oxbillzip->value;
            $aUserInfo["billing"]["country"] = "Österreich";//$oOrder->oxorder__oxbillcountry->value;
            $aUserInfo["billing"]["telephone"] = $oOrder->oxorder__oxbillfon->value;

        }

        return $aUserInfo;

    }

    /**
     * Send mail about error to admin and write log file
     *
     * @param null $oECommers
     * @param null $sMsg
     */
    protected function _errorWriteMsg($oECommers = null,$sMsg = null)
    {
        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC);
        /*
        $this->_sendNotificationLetter(
            $this->_oOrderCheck,
            true
        );
*/
        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC, true);
        $fStream = fopen(getShopBasePath() . DIR_SEP . 'log/exportcomm.log', 'a');
        if($oECommers!=null){
            $sErrorMsg = date('Y.m.d H:i:s')." Problem with export - ".$oECommers->getClassNameECommerce()."\nText error: " .$oECommers->getErroMsqECommerce();
        }else{
            $sErrorMsg = $sMsg;
        }
        fprintf($fStream, $sErrorMsg."\n ");
    }


    /**
     * @param $sOrderId
     * @param $bIfInclude - if we include comment to email
     * @return bool result email send
     */
    protected function _sendNotificationLetter($oOrder,$bIfInclude) {


        $oSmartyRenderer = oxNew('oxSmartyRenderer');
        $oMailObject = oxNew('oxemail');
        $oShop = oxNew('oxshop');
        $oShop->load($this->getConfig()->getShopId());

        $oMailObject->setViewData('sMailText', "Bei dem Datenimport in Ordnung: ".$oOrder->oxorder__oxordernr->value);
        $oMailObject->setViewData('bIfSendComment', $bIfInclude);
        $oMailObject->setViewData('oTheme', oxNew('oxTheme'));
        $oMailObject->setViewData('shop', $oMailObject->getShop());
        $oMailObject->setViewData('oViewConf', $oMailObject->getViewConfig());
        $oMailObject->setViewData('userinfo', $oMailObject->getuser());

      //  $oMailObject->setRecipient($oShop->oxshops__oxowneremail->rawValue, "");
      //  $oMailObject->setReplyTo($oShop->oxshops__oxowneremail->rawValue, "");
          $oMailObject->setRecipient("oluvko07@gmail.com", "");
          $oMailObject->setReplyTo("oluvko07@gmail.com", "");

        $oMailObject->isHtml(true);

        $oMailObject->setSubject(
            oxRegistry::getLang()->translateString(
                'ORDER_CHANGE_STATUS_MAIL_TITLE'
            )
        );
        $oMailObject->setBody(
            $oSmartyRenderer->renderTemplate(
                $this->_sEmailTemplate, $oMailObject->getViewData()
            )
        );

        return $oMailObject->send();
    }

    /**
     * Export orders data to e commerce
     *
     * @param $sOrderKey
     */
    public function exportToECommerce($sOrderKey)
    {
       // $aOrderAdd = array(0 => "1f8de1bdcfc2d0b3f93f06b85c86654d");//462
        $aOrderAdd = array(0 => $sOrderKey);

        $this->_oTransactions = oxNew('ectransaction');
        $this->_oTransactions->TransactionBegin();

        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC, true);
      //  var_dump(zsDb::getDb()->getAll("SELECT * FROM test1 where 1"));

        foreach ($aOrderAdd as $sOrderKey) {

            zsDb::setNewConnection();
            zsDb::getDb(zsDb::FETCH_MODE_ASSOC);

            $oOrder = oxNew('oxOrder');
            $oOrder->load($sOrderKey);// 451 c43b3524555912c892e70ce39090c9f7    462 1f8de1bdcfc2d0b3f93f06b85c86654d
            $this->_oOrderCheck = $oOrder;
            if($oOrder->oxorder__zsecommerceid->value==null){
                $this->_errorWriteMsg(null,"Order # ".$oOrder->oxorder__oxordernr->value." already import to e commerce");
                continue;
            }

            // Get order data about user and order payment
            $oPayment = oxNew('oxpayment');
            $oPayment->load($oOrder->oxorder__oxpaymenttype->value);
            $oUser = $oOrder->getOrderUser();

            // Connect to e commerce bd
            zsDb::setNewConnection();
            zsDb::getDb(zsDb::FETCH_MODE_ASSOC, true);

            // If user have profile in oxid, create user in e commerce
            if ($oOrder->oxorder__oxuserid->value) {

                if(!$sCustomerId = $this->_exportCustomers($oOrder,$oUser))
                    continue;

             //   if(!$this->_exportCustomerinfo($sCustomerId))
               //     continue;

                if(!$country_id = $this->_getCounty($oOrder))
                    continue;

                if(!$aUserInfo = $this->_getUserInfoOxid($oOrder))
                    continue;

                $this->_exportAddressBook($sCustomerId,$aUserInfo,$country_id,$oOrder);

            }

            if(!$sCommersOrderId = $this->_exportOrder($sCustomerId,$aUserInfo,$oPayment,$oOrder))
                continue;
            if(!$products_update = $this->_exportOrderArticles($oOrder,$sCommersOrderId))
                continue;/*
            if(!$this->_exportOrderTotal($sCommersOrderId,
                                    "Zwischensumme:",
                                    str_replace('.', ',', $oOrder->oxorder__oxtotalbrutsum->value) . ' ' . $this->getConfig()->getActShopCurrencyObject()->sign,
                                    $oOrder->oxorder__oxtotalbrutsum->value,
                                    "ot_subtotal",
                                    1))
                continue;

            if(!$this->_exportOrderTotal($sCommersOrderId,
                                    "<b>Summe</b>:",
                                    str_replace('.', ',', $oOrder->oxorder__oxtotalordersum->value) . ' ' . $this->getConfig()->getActShopCurrencyObject()->sign,
                                    $oOrder->oxorder__oxtotalordersum->value,
                                    "ot_total",
                                    5))
                continue;

            if(!$this->_exportOrderTotal($sCommersOrderId,
                                    "Verpackung & Versand:",
                                    str_replace('.', ',', $oOrder->oxorder__oxdelcost->value) . ' ' . $this->getConfig()->getActShopCurrencyObject()->sign,
                                    $oOrder->oxorder__oxdelcost->value,
                                    "ot_shipping",
                                    3))
                continue;
*/
            if(!$this->_exportOrdersStatusHistory($sCommersOrderId))
                continue;

            $oCommersProducts = oxNew('ecproduct');
            $oCommersProducts->UpdateECommerce($products_update);//7570

            zsDb::setNewConnection();
            zsDb::getDb(zsDb::FETCH_MODE_ASSOC);
            $oOrder->assign(array("oxorder__zsecommerceid"=>$sCommersOrderId));
            $oOrder->save();

        }

        zsDb::setNewConnection();
        zsDb::getDb(zsDb::FETCH_MODE_ASSOC);
    }

}