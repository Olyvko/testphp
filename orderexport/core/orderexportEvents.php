<?php

class orderexportEvents {

    public static function onActivate() {

        self::addMissingFieldsOnActivateOrderExport();
    }


    public static function addMissingFieldsOnActivateOrderExport(){

        $if_exist_column_sql = "SHOW COLUMNS FROM `oxorder` LIKE 'ZSECOMMERCEID' ";
        $oRs = oxDb::getDb()->getArray($if_exist_column_sql);

        if(empty($oRs)) {
            $sSql = " ALTER TABLE `oxorder`
                    ADD `ZSECOMMERCEID` int(11)
                    COMMENT 'Id order from E Commerce, after export' ";
            oxDb::getDb()->execute($sSql);
        }

    }

}
