<?php

/**
 * @file
 * A página PHP que atende a todas as requisições de página em uma instalação GamePress.
 *
 * Todo o código GamePress é lançado sob a Licença Pública Geral GNU.
 * Veja os arquivos COPYRIGHT.txt e LICENSE.txt no diretório 'core'.
 */

use GamePress\Core\GamePressKernel;
use Symfony\Component\HttpFoundation\Request;

$autoloader = require_once 'autoload.php';

$kernel = new GamePressKernel('prod', $autoloader);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
