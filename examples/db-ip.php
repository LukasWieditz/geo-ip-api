<?php

require __DIR__ . '/../vendor/autoload.php';

$geoIp = new \GeoIP\GeoIP([
    'services' => ['db-ip.com' => ['api_key' => true]]
]);

echo '<pre>';

var_dump($geoIp->getIpInfo('8.8.8.8'));

echo '</pre>';