<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * AlarmMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the AlarmDAO to the alarm_report datastore.
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
class AlarmMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","alarm_report","Id",true,FM_TYPE_BIGINT,20,null,false);
			self::$FM["Node"] = new FieldMap("Node","alarm_report","Node",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Sensor"] = new FieldMap("Sensor","alarm_report","Sensor",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Alarmtrigger"] = new FieldMap("Alarmtrigger","alarm_report","AlarmTrigger",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Observatiewaarde"] = new FieldMap("Observatiewaarde","alarm_report","ObservatieWaarde",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Observatietijdstip"] = new FieldMap("Observatietijdstip","alarm_report","ObservatieTijdstip",false,FM_TYPE_DATETIME,null,null,false);
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
		}
		return self::$KM;
	}

}

?>