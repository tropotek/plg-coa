<?php
$config = \App\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Coa\\', dirname(__FILE__));

$routes = $config->getRouteCollection();
if (!$routes) return;


$params = array();

// Staff Only
$routes->add('staff-coa-manager', new \Tk\Routing\Route('/staff/{subjectCode}/coaManager.html', 'Coa\Controller\Manager::doDefault', $params));
$routes->add('staff-coa-edit', new \Tk\Routing\Route('/staff/{subjectCode}/coaEdit.html', 'Coa\Controller\Edit::doDefault', $params));



