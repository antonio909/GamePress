<?php

/**
 * @file
 * A página PHP que lida com a atualização da instalação do GamePress.
 *
 * Todo o código GamePress é lançado sob a Licença Pública Geral GNU.
 * Consulte os arquivos COPYRIGHT.txt e LICENSE.txt no diretório "core".
 */

use GamePress\Core\Update\UpdateKernel;
use Symfony\Component\HttpFoundation\Request;

$autoloader = require_once 'autoload.php';

// Desabilita a coleta de lixo durante a execução do teste. Sob certas circunstâncias,
// caminho de atualização criará tantos objetos que a coleta de lixo causa
// falhas de segmentação.
if (gamepress_valid_test_ua()) {
  gc_collect_cycles();
  gc_disable();
}

$kernel = new UpdateKernel('prod', $autoloader, FALSE);
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
