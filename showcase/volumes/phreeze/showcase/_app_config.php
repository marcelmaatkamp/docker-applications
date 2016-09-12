<?php
/**
 * @package SHOWCASE
 *
 * APPLICATION-WIDE CONFIGURATION SETTINGS
 *
 * This file contains application-wide configuration settings.  The settings
 * here will be the same regardless of the machine on which the app is running.
 *
 * This configuration should be added to version control.
 *
 * No settings should be added to this file that would need to be changed
 * on a per-machine basic (ie local, staging or production).  Any
 * machine-specific settings should be added to _machine_config.php
 */

/**
 * APPLICATION ROOT DIRECTORY
 * If the application doesn't detect this correctly then it can be set explicitly
 */
if (!GlobalConfig::$APP_ROOT) GlobalConfig::$APP_ROOT = realpath("./");

/**
 * check is needed to ensure asp_tags is not enabled
 */
if (ini_get('asp_tags')) 
	die('<h3>Server Configuration Problem: asp_tags is enabled, but is not compatible with Savant.</h3>'
	. '<p>You can disable asp_tags in .htaccess, php.ini or generate your app with another template engine such as Smarty.</p>');

/**
 * INCLUDE PATH
 * Adjust the include path as necessary so PHP can locate required libraries
 */
set_include_path(
		GlobalConfig::$APP_ROOT . '/libs/' . PATH_SEPARATOR .
		GlobalConfig::$APP_ROOT . '/../phreeze/libs' . PATH_SEPARATOR .
		GlobalConfig::$APP_ROOT . '/vendor/phreeze/phreeze/libs/' . PATH_SEPARATOR .
		get_include_path()
);

/**
 * COMPOSER AUTOLOADER
 * Uncomment if Composer is being used to manage dependencies
 */
// $loader = require 'vendor/autoload.php';
// $loader->setUseIncludePath(true);

/**
 * SESSION CLASSES
 * Any classes that will be stored in the session can be added here
 * and will be pre-loaded on every page
 */
require_once "App/ExampleUser.php";

/**
 * RENDER ENGINE
 * You can use any template system that implements
 * IRenderEngine for the view layer.  Phreeze provides pre-built
 * implementations for Smarty, Savant and plain PHP.
 */
require_once 'verysimple/Phreeze/SavantRenderEngine.php';
GlobalConfig::$TEMPLATE_ENGINE = 'SavantRenderEngine';
GlobalConfig::$TEMPLATE_PATH = GlobalConfig::$APP_ROOT . '/templates/';

/**
 * ROUTE MAP
 * The route map connects URLs to Controller+Method and additionally maps the
 * wildcards to a named parameter so that they are accessible inside the
 * Controller without having to parse the URL for parameters such as IDs
 */
GlobalConfig::$ROUTE_MAP = array(

	// default controller when no route specified
	'GET:' => array('route' => 'Default.Home'),
		
	// example authentication routes
	'GET:loginform' => array('route' => 'SecureExample.LoginForm'),
	'POST:login' => array('route' => 'SecureExample.Login'),
	'GET:secureuser' => array('route' => 'SecureExample.UserPage'),
	'GET:secureadmin' => array('route' => 'SecureExample.AdminPage'),
	'GET:logout' => array('route' => 'SecureExample.Logout'),
		
	// Alarm
	'GET:alarms' => array('route' => 'Alarm.ListView'),
	'GET:alarm/(:num)' => array('route' => 'Alarm.SingleView', 'params' => array('id' => 1)),
	'GET:api/alarms' => array('route' => 'Alarm.Query'),
	'POST:api/alarm' => array('route' => 'Alarm.Create'),
	'GET:api/alarm/(:num)' => array('route' => 'Alarm.Read', 'params' => array('id' => 2)),
	'PUT:api/alarm/(:num)' => array('route' => 'Alarm.Update', 'params' => array('id' => 2)),
	'DELETE:api/alarm/(:num)' => array('route' => 'Alarm.Delete', 'params' => array('id' => 2)),
		
	// Node
	'GET:nodes' => array('route' => 'Node.ListView'),
	'GET:node/(:any)' => array('route' => 'Node.SingleView', 'params' => array('devEui' => 1)),
	'GET:api/nodes' => array('route' => 'Node.Query'),
	'POST:api/node' => array('route' => 'Node.Create'),
	'GET:api/node/(:any)' => array('route' => 'Node.Read', 'params' => array('devEui' => 2)),
	'PUT:api/node/(:any)' => array('route' => 'Node.Update', 'params' => array('devEui' => 2)),
	'DELETE:api/node/(:any)' => array('route' => 'Node.Delete', 'params' => array('devEui' => 2)),
		
	// NodeThreshold
	'GET:nodethresholds' => array('route' => 'NodeThreshold.ListView'),
	'GET:nodethreshold/(:num)' => array('route' => 'NodeThreshold.SingleView', 'params' => array('id' => 1)),
	'GET:api/nodethresholds' => array('route' => 'NodeThreshold.Query'),
	'POST:api/nodethreshold' => array('route' => 'NodeThreshold.Create'),
	'GET:api/nodethreshold/(:num)' => array('route' => 'NodeThreshold.Read', 'params' => array('id' => 2)),
	'PUT:api/nodethreshold/(:num)' => array('route' => 'NodeThreshold.Update', 'params' => array('id' => 2)),
	'DELETE:api/nodethreshold/(:num)' => array('route' => 'NodeThreshold.Delete', 'params' => array('id' => 2)),
		
	// Observation
	'GET:observations' => array('route' => 'Observation.ListView'),
	'GET:observation/(:num)' => array('route' => 'Observation.SingleView', 'params' => array('id' => 1)),
	'GET:api/observations' => array('route' => 'Observation.Query'),
	'POST:api/observation' => array('route' => 'Observation.Create'),
	'GET:api/observation/(:num)' => array('route' => 'Observation.Read', 'params' => array('id' => 2)),
	'PUT:api/observation/(:num)' => array('route' => 'Observation.Update', 'params' => array('id' => 2)),
	'DELETE:api/observation/(:num)' => array('route' => 'Observation.Delete', 'params' => array('id' => 2)),
		
	// Sensor
	'GET:sensors' => array('route' => 'Sensor.ListView'),
	'GET:sensor/(:any)' => array('route' => 'Sensor.SingleView', 'params' => array('sensorId' => 1)),
	'GET:api/sensors' => array('route' => 'Sensor.Query'),
	'POST:api/sensor' => array('route' => 'Sensor.Create'),
	'GET:api/sensor/(:any)' => array('route' => 'Sensor.Read', 'params' => array('sensorId' => 2)),
	'PUT:api/sensor/(:any)' => array('route' => 'Sensor.Update', 'params' => array('sensorId' => 2)),
	'DELETE:api/sensor/(:any)' => array('route' => 'Sensor.Delete', 'params' => array('sensorId' => 2)),

	// catch any broken API urls
	'GET:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'PUT:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'POST:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'DELETE:api/(:any)' => array('route' => 'Default.ErrorApi404')
);

/**
 * FETCHING STRATEGY
 * You may uncomment any of the lines below to specify always eager fetching.
 * Alternatively, you can copy/paste to a specific page for one-time eager fetching
 * If you paste into a controller method, replace $G_PHREEZER with $this->Phreezer
 */
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Alarm","FK_27v5pji13cutepjuv9ox0glwp",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Alarm","FK_qqgttvcq7u148nkqjhx2hsbdi",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("NodeThreshold","FK_4stgr2ch3nidujfk8pial5sdv",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("NodeThreshold","FK_afhhe5d4s0if67l0h6fxmdj08",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Observation","FK_3vtmlnui6re2o9jq4vqpa2t06",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Observation","FK_smi270lm0koqq55tj5bfisawt",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
?>