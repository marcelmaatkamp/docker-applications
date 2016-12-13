<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * SensorMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the SensorDAO to the sensor datastore.
 *
 * WARNING: THIS IS AN AUTO-GENERATED FILE
 *
 * This file should generally not be edited by hand except in special circumstances.
 * You can override the default fetching strategies for KeyMaps in _config.php.
 * Leaving this file alone will allow easy re-generation of all DAOs in the event of schema changes
 *
 * @package Showcase::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class SensorMap implements IDaoMap, IDaoMap2
{

	private static $KM;
	private static $FM;
	
	/**
	 * {@inheritdoc}
	 */
	public static function AddMap($property,FieldMap $map)
	{
		self::GetFieldMaps();
		self::$FM[$property] = $map;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function SetFetchingStrategy($property,$loadType)
	{
		self::GetKeyMaps();
		self::$KM[$property]->LoadType = $loadType;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function GetFieldMaps()
	{
		if (self::$FM == null)
		{
			self::$FM = Array();
			self::$FM["SensorId"] = new FieldMap("SensorId","sensor","sensor_id",true,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Omschrijving"] = new FieldMap("Omschrijving","sensor","omschrijving",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Eenheid"] = new FieldMap("Eenheid","sensor","eenheid",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Omrekenfactor"] = new FieldMap("Omrekenfactor","sensor","omrekenfactor",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Presentatie"] = new FieldMap("Presentatie","sensor","presentatie",false,FM_TYPE_VARCHAR,255,null,false);
		}
		return self::$FM;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function GetKeyMaps()
	{
		if (self::$KM == null)
		{
			self::$KM = Array();
			self::$KM["FK_alarm_regel_sensor"] = new KeyMap("FK_alarm_regel_sensor", "SensorId", "AlarmRegel", "Sensor", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["FK_observatie_sensor"] = new KeyMap("FK_observatie_sensor", "SensorId", "Observatie", "Sensor", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
		}
		return self::$KM;
	}

}

?>