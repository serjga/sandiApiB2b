# sandi/api
Description: Composer package by API b2b Sandi+ </br> </br>
<b>Installation</b> </br>
This library is installable via Composer: </br>
composer require sandi/api @dev </br></br>

<b>Examples</b></br>
use \sandi\api\ApiSandiB2b;</br></br>
$api = new ApiSandiB2b( [KEY_API] );</br>
$list_product = $api->getProducts();</br>
