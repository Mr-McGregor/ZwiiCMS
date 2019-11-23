<?php

/**
 * This file is part of Zwii.
 *
 * For full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 *
 * @author Rémi Jean <remi.jean@outlook.com>
 * @copyright Copyright (C) 2008-2018, Rémi Jean
 * @author Frédéric Tempez <frederic.tempez@outlook.com>
 * @copyright Copyright (C) 2018-2020, Frédéric Tempez
 * @license GNU General Public License, version 3
 * @link http://zwiicms.com/
 */

/**
 * Vérification de la version de PHP
 */
if(version_compare(PHP_VERSION, '5.6.0', '<')) {
	exit('PHP 5.6+ required.');
}

/* Set locale to French */
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fra_FRA', 'french');

/**
 * Initialisation de Zwii
 */
session_start();
require 'core/core.php';
require 'core/class/helper.class.php';
require 'core/class/template.class.php';
$core = new core;
spl_autoload_register('core::autoload');
echo $core->router();