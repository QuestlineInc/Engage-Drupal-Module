<?php

namespace Drupal\Engage_Drupal_Module\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

class QuestlineEngageCommands extends DrushCommands {

    function questline_engage_drush_command() {

        $file = dirname(__FILE__);
        $real_path = realpath($file . '/./');
        $drupal_path = explode('modules', $real_path);

        define('DRUPAL_DIR', $drupal_path[0]);

        require_once DRUPAL_DIR . '/core/includes/database.inc';
        require_once DRUPAL_DIR . '/core/includes/schema.inc';

        $autoloader = require_once DRUPAL_DIR . '/autoload.php';

        $request = Request::createFromGlobals();
        $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
        $kernel->boot();
        $kernel->prepareLegacyRequest($request);

    }
}