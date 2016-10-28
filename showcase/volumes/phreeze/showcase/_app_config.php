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
		'phar://' . GlobalConfig::$APP_ROOT . '/libs/phreeze-3.3.8.phar' . PATH_SEPARATOR .
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
require_once("Model/User.php");

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
	
	
	
	'GET:export' => array('route' => 'Node.Export'),
		
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
		
	// Alarm_Notificatie
	'GET:alarm_notificaties' => array('route' => 'Alarm_Notificatie.ListView'),
	'GET:alarm_notificatie/(:num)' => array('route' => 'Alarm_Notificatie.SingleView', 'params' => array('id' => 1)),
	'GET:api/alarm_notificaties' => array('route' => 'Alarm_Notificatie.Query'),
	'POST:api/alarm_notificatie' => array('route' => 'Alarm_Notificatie.Create'),
	'GET:api/alarm_notificatie/(:num)' => array('route' => 'Alarm_Notificatie.Read', 'params' => array('id' => 2)),
	'PUT:api/alarm_notificatie/(:num)' => array('route' => 'Alarm_Notificatie.Update', 'params' => array('id' => 2)),
	'DELETE:api/alarm_notificatie/(:num)' => array('route' => 'Alarm_Notificatie.Delete', 'params' => array('id' => 2)),
		
	// Alarm_Regel
	'GET:alarm_regels' => array('route' => 'Alarm_Regel.ListView'),
	'GET:alarm_regel/(:num)' => array('route' => 'Alarm_Regel.SingleView', 'params' => array('id' => 1)),
	'GET:api/alarm_regels' => array('route' => 'Alarm_Regel.Query'),
	'POST:api/alarm_regel' => array('route' => 'Alarm_Regel.Create'),
	'GET:api/alarm_regel/(:num)' => array('route' => 'Alarm_Regel.Read', 'params' => array('id' => 2)),
	'PUT:api/alarm_regel/(:num)' => array('route' => 'Alarm_Regel.Update', 'params' => array('id' => 2)),
	'DELETE:api/alarm_regel/(:num)' => array('route' => 'Alarm_Regel.Delete', 'params' => array('id' => 2)),
		
	// Alarm
	'GET:alarmen' => array('route' => 'Alarm.ListView'),
	'GET:alarm/(:any)' => array('route' => 'Alarm.SingleView', 'params' => array('id' => 1)),
	'GET:api/alarmen' => array('route' => 'Alarm.Query'),
	'POST:api/alarm' => array('route' => 'Alarm.Create'),
	'GET:api/alarm/(:any)' => array('route' => 'Alarm.Read', 'params' => array('id' => 2)),
	'PUT:api/alarm/(:any)' => array('route' => 'Alarm.Update', 'params' => array('id' => 2)),
	'DELETE:api/alarm/(:any)' => array('route' => 'Alarm.Delete', 'params' => array('id' => 2)),
		
	// Laatste_Observatie
	'GET:laatste_observaties' => array('route' => 'Laatste_Observatie.ListView'),
	'GET:laatste_observatie/(:any)' => array('route' => 'Laatste_Observatie.SingleView', 'params' => array('observatieid' => 1)),
	'GET:api/laatste_observaties' => array('route' => 'Laatste_Observatie.Query'),
	'POST:api/laatste_observatie' => array('route' => 'Laatste_Observatie.Create'),
	'GET:api/laatste_observatie/(:any)' => array('route' => 'Laatste_Observatie.Read', 'params' => array('observatieid' => 2)),
	'PUT:api/laatste_observatie/(:any)' => array('route' => 'Laatste_Observatie.Update', 'params' => array('observatieid' => 2)),
	'DELETE:api/laatste_observatie/(:any)' => array('route' => 'Laatste_Observatie.Delete', 'params' => array('observatieid' => 2)),
		
	// Node
	'GET:nodes' => array('route' => 'Node.ListView'),
	'GET:node/(:any)' => array('route' => 'Node.SingleView', 'params' => array('devEui' => 1)),
	'GET:api/nodes' => array('route' => 'Node.Query'),
	'POST:api/node' => array('route' => 'Node.Create'),
	'GET:api/node/(:any)' => array('route' => 'Node.Read', 'params' => array('devEui' => 2)),
	'PUT:api/node/(:any)' => array('route' => 'Node.Update', 'params' => array('devEui' => 2)),
	'DELETE:api/node/(:any)' => array('route' => 'Node.Delete', 'params' => array('devEui' => 2)),
		
	// Observatie
	'GET:observaties' => array('route' => 'Observatie.ListView'),
	'GET:observatie/(:num)' => array('route' => 'Observatie.SingleView', 'params' => array('id' => 1)),
	'GET:api/observaties' => array('route' => 'Observatie.Query'),
	'POST:api/observatie' => array('route' => 'Observatie.Create'),
	'GET:api/observatie/(:num)' => array('route' => 'Observatie.Read', 'params' => array('id' => 2)),
	'PUT:api/observatie/(:num)' => array('route' => 'Observatie.Update', 'params' => array('id' => 2)),
	'DELETE:api/observatie/(:num)' => array('route' => 'Observatie.Delete', 'params' => array('id' => 2)),
		
	// Sensor
	'GET:sensoren' => array('route' => 'Sensor.ListView'),
	'GET:sensor/(:any)' => array('route' => 'Sensor.SingleView', 'params' => array('sensorId' => 1)),
	'GET:api/sensoren' => array('route' => 'Sensor.Query'),
	'POST:api/sensor' => array('route' => 'Sensor.Create'),
	'GET:api/sensor/(:any)' => array('route' => 'Sensor.Read', 'params' => array('sensorId' => 2)),
	'PUT:api/sensor/(:any)' => array('route' => 'Sensor.Update', 'params' => array('sensorId' => 2)),
	'DELETE:api/sensor/(:any)' => array('route' => 'Sensor.Delete', 'params' => array('sensorId' => 2)),
		
	// SensorNodeObservation
	'GET:sensornodeobservations' => array('route' => 'SensorNodeObservation.ListView'),
	'GET:sensornodeobservation/(:any)' => array('route' => 'SensorNodeObservation.SingleView', 'params' => array('node' => 1)),
	'GET:api/sensornodeobservations' => array('route' => 'SensorNodeObservation.Query'),
	'POST:api/sensornodeobservation' => array('route' => 'SensorNodeObservation.Create'),
	'GET:api/sensornodeobservation/(:any)' => array('route' => 'SensorNodeObservation.Read', 'params' => array('node' => 2)),
	'PUT:api/sensornodeobservation/(:any)' => array('route' => 'SensorNodeObservation.Update', 'params' => array('node' => 2)),
	'DELETE:api/sensornodeobservation/(:any)' => array('route' => 'SensorNodeObservation.Delete', 'params' => array('node' => 2)),

	// Role
	'GET:roles' => array('route' => 'Role.ListView'),
	'GET:role/(:num)' => array('route' => 'Role.SingleView', 'params' => array('id' => 1)),
	'GET:api/roles' => array('route' => 'Role.Query'),
	'POST:api/role' => array('route' => 'Role.Create'),
	'GET:api/role/(:num)' => array('route' => 'Role.Read', 'params' => array('id' => 2)),
	'PUT:api/role/(:num)' => array('route' => 'Role.Update', 'params' => array('id' => 2)),
	'DELETE:api/role/(:num)' => array('route' => 'Role.Delete', 'params' => array('id' => 2)),
		
	// User
	'GET:users' => array('route' => 'User.ListView'),
	'GET:user/(:num)' => array('route' => 'User.SingleView', 'params' => array('id' => 1)),
	'GET:api/users' => array('route' => 'User.Query'),
	'POST:api/user' => array('route' => 'User.Create'),
	'GET:api/user/(:num)' => array('route' => 'User.Read', 'params' => array('id' => 2)),
	'PUT:api/user/(:num)' => array('route' => 'User.Update', 'params' => array('id' => 2)),
	'DELETE:api/user/(:num)' => array('route' => 'User.Delete', 'params' => array('id' => 2)),
	
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
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("AlarmNotificatie","fk_alarm_notificatie_alarm_regel",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("AlarmRegel","FK_4stgr2ch3nidujfk8pial5sdv",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("AlarmRegel","FK_afhhe5d4s0if67l0h6fxmdj08",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Observatie","FK_3vtmlnui6re2o9jq4vqpa2t06",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("Observatie","FK_smi270lm0koqq55tj5bfisawt",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
?>