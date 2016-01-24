<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id' => 'orderexport',
    'title' => array(
        'de' => 'Order export',
        'en' => 'Order export',
    ),
    'description' => array(
        'de' => 'Order export to e Commerce.',
        'en' => 'Order export to e Commerce.',
    ),
    'thumbnail' => 'logo.jpg',
    'version' => '1.0',
    'author' => 'ZinitSolution',
    'url' => 'http://zinitsolutions.com',
    'email' => 'info@zinitsolutions.com',

    'files' => array(
        'orderexport_ecommerce'                 => 'zs/orderexport/controllers/orderexport_ecommerce.php',
        'ecommerce'                             => 'zs/orderexport/models/ecommerce.php',
        'ecorderstatushistory'                  => 'zs/orderexport/models/ecorderstatushistory.php',
        'eccustomer'                            => 'zs/orderexport/models/eccustomer.php',
        'eccustomersinfo'                       => 'zs/orderexport/models/eccustomersinfo.php',
        'eccountry'                             => 'zs/orderexport/models/eccountry.php',
        'ecaddressbook'                         => 'zs/orderexport/models/ecaddressbook.php',
        'ecorder'                               => 'zs/orderexport/models/ecorder.php',
        'ecproduct'                             => 'zs/orderexport/models/ecproduct.php',
        'ecordersproducts'                      => 'zs/orderexport/models/ecordersproducts.php',
        'ecorderstotal'                         => 'zs/orderexport/models/ecorderstotal.php',
        'ectransaction'                         => 'zs/orderexport/models/ectransaction.php',
        'zsdb'                                  => 'zs/orderexport/core/zsdb.php',
        'orderexportEvents'                     => 'zs/orderexport/core/orderexportEvents.php'
    ),
    'events' => array(
        'onActivate' => 'orderexportEvents::onActivate'
    ),
    'blocks' => array(
        array('template' => 'order_preview.tpl', 'block'=>'order_preview_import_order_test', 'file'=>'views/admin/order_preview_import_order.tpl'),
    ),
    'templates' => array(
        'email2admin.tpl'  => 'zs/orderexport/views/letter/email2admin.tpl',
    ),


);
