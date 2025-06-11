<?php
/**
 * GamePress
 *
 * @link
 * @copyright
 * @license
 *
 */

include_once "vendor/autoload.php";
include_once "gamepress/core/System/Preloader.php";

(new Gamepress\Core\System\Preloader([],false))
    ->load('vendor/gamepress/container/')
    ->load('vendor/gamepress/core')
    ->load('vendor/gamepress/core/framework/')
    ->load('vendor/gamepress/core/controllers/');
