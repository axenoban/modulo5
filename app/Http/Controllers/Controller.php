<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *      @OA\Info(
 *          version="1.0.0",
 *          title="API de Mantenimiento TI",
 *          description="Documentación de la API REST"
 *      ),
 *      @OA\Server(
 *          url=L5_SWAGGER_CONST_HOST,
 *          description="Servidor de Producción Railway"
 *      )
 * )
 */
abstract class Controller
{
    //
}
