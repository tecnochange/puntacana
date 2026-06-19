<?php

// Zona horaria y localización
setlocale(LC_TIME, "es_ES");
date_default_timezone_set('America/Bogota');

// URL base
$baseUrl = 'https://' . $_SERVER['SERVER_NAME'] . '';

// Rutas de recursos
return [
    'base_url' => $baseUrl,
    'recursos_clima' => $baseUrl . 'recursos_clima',
    'recursos_virtual' => $baseUrl . 'recursos_virtual',
    'recursos_desarrollo' => $baseUrl . 'recursos_desarrollo'
];