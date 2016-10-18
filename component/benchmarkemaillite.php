<?php

/******************************************************
 This file is the main admin controller used by Joomla.
*******************************************************/

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load Classes
JLoader::registerPrefix( 'BenchmarkEmailLite', JPATH_COMPONENT );

// Get Application
$app = JFactory::getApplication();

// Require Specific Controller If Requested
$controller = $app->input->get( 'controller', 'dashboard' );

// Create The Controller
$classname  = 'BenchmarkEmailLiteControllers' . ucwords( $controller );
$controller = new $classname();

// Perform The Request Task
$controller->execute();
