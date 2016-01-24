[{include file="email/html/header.tpl" title=$shop->oxshops__oxname->value}]

[{oxmultilang ident="ORDER_CHANGE_STATUS_MAIL_MESSAGE" }] [{oxmultilang ident=$sOldFolder noerror=true }]
[{oxmultilang ident="ORDER_CHANGE_STATUS_TO" }] [{oxmultilang ident=$sNewFolder noerror=true }]

<hr>
[{if $bIfSendComment == 'include'}]
    [{$sMailText}]
    [{/if}]
[{include file="email/html/footer.tpl"}]
