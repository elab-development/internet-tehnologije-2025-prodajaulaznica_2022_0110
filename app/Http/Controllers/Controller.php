<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "EPA Prodaja Karata API",
    version: "1.0.0",
    description: "API dokumentacija za EPA sistem prodaje karata"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Lokalni server"
)]
#[OA\PathItem(
    path: "/"
)]
abstract class Controller
{
    //
}
