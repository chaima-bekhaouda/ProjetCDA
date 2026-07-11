<?php

namespace App\Config;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="BookNest API",
 *     version="1.0.0",
 *     description="API de gestion de bibliothèque personnelle"
 *   ),
 *   @OA\Server(url="http://localhost:8000"),
 *   @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 *   )
 * )
 */
class OpenApi
{
}
