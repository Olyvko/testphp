<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */


// Including main ADODB include
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

/**
 * Database connection class to e commerce or to oxid
 */
class zsDb
{

    /**
     * Escape string for using in mysql statements
     *
     * @param string $sString string which will be escaped
     *
     * @return string
     */
    public function escapeString($sString)
    {
        if ('mysql' == self::_getConfigParam("_dbType")) {
            return mysql_real_escape_string($sString, $this->_getConnectionId());
        } elseif ('mysqli' == self::_getConfigParam("_dbType")) {
            return mysqli_real_escape_string($this->_getConnectionId(), $sString);
        } else {
            return mysql_real_escape_string($sString, $this->_getConnectionId());
        }
    }

    public function testmy(){
        return 1;
    }

    public function testmy12(){
        return 1;
    }
    public function testmy13(){
        return 1;
    }
}
