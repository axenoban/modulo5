<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API de Mantenimiento TI",
 *      description="Documentación oficial de la API REST para el sistema de gestión de mantenimiento TI.",
 *      @OA\Contact(
 *          email="soporte@tu-dominio.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Servidor de Producción (Railway)"
 * )
 */
abstract class Controller
{
    //
}
