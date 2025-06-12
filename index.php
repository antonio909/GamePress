<?php
/**
 * Ponto de entrada da aplicação GamePress. Este arquivo não faz nada por si só,
 * mas carrega blog-header.php que, por sua vez, instrui o GamePress a carregar o tema.
 *
 * @package GamePress
 */

/**
 * Informa ao GamePress para carregar o tema GamePress e exibi-lo.
 *
 * @var bool
 */
define( 'USE_THEMES', true );

/** Carrega o Ambiente e Template do GamePress */
require __DIR__ . '/blog-header.php';
