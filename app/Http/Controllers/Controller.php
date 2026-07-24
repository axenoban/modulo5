<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *      @OA\Info(
 *          version="1.0.0",
 *          title="API de Mantenimiento TI",
 *          description="Documentación oficial de la API REST para el sistema de gestión de mantenimiento TI.",
 *          @OA\Contact(
 *              email="soporte@tudominio.com"
 *          ),
 *          @OA\License(
 *              name="MIT",
 *              url="https://opensource.org/licenses/MIT"
 *          )
 *      ),
 *      @OA\Server(
 *          url=L5_SWAGGER_CONST_HOST,
 *          description="Servidor de Producción (Railway)"
 *      ),
 *      @OA\SecurityScheme(
 *          securityScheme="bearerAuth",
 *          type="http",
 *          scheme="bearer",
 *          bearerFormat="JWT",
 *          description="Introduce el token JWT obtenido al iniciar sesión en el formato: Bearer <tu_token>"
 *      ),
 *      @OA\Tag(
 *          name="Trabajos de Mantenimiento",
 *          description="Endpoints relacionados con la gestión y el borrado lógico de trabajos de mantenimiento"
 *      ),
 *      @OA\Tag(
 *          name="Autenticación",
 *          description="Endpoints de inicio de sesión y control de usuarios"
 *      )
 * )
 */
abstract class Controller
{
    //
}
