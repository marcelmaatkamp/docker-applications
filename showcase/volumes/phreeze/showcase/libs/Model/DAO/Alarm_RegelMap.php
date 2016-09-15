<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * Alarm_RegelMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the Alarm_RegelDAO to the alarm_regel datastore.
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
class Alarm_RegelMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","alarm_regel","id",true,FM_TYPE_BIGINT,20,null,true);
			self::$FM["Node"] = new FieldMap("Node","alarm_regel","node",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Sensor"] = new FieldMap("Sensor","alarm_regel","sensor",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["AlarmTrigger"] = new FieldMap("AlarmTrigger","alarm_regel","alarm_trigger",false,FM_TYPE_VARCHAR,255,null,false);
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
			self::$KM["FK_qqgttvcq7u148nkqjhx2hsbdi"] = new KeyMap("FK_qqgttvcq7u148nkqjhx2hsbdi", "Id", "Alarm", "AlarmRegel", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["fk_alarm_notificatie_alarm_regel"] = new KeyMap("fk_alarm_notificatie_alarm_regel", "Id", "AlarmNotificatie", "AlarmRegel", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["FK_4stgr2ch3nidujfk8pial5sdv"] = new KeyMap("FK_4stgr2ch3nidujfk8pial5sdv", "Node", "Node", "DevEui", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			self::$KM["FK_afhhe5d4s0if67l0h6fxmdj08"] = new KeyMap("FK_afhhe5d4s0if67l0h6fxmdj08", "Sensor", "Sensor", "SensorId", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>