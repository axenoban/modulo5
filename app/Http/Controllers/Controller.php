<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        title: "API Módulo de Mantenimiento",
        description: "Documentación oficial de los microservicios de mantenimiento."
    )
)]
#[OA\Server(
    url: "https://modulo5-production.up.railway.app/",
    description: "Servidor de Producción Railway"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
abstract class Controller
{
    //
}
