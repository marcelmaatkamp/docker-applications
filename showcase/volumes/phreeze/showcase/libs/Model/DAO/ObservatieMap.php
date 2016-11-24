<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * ObservatieMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the ObservatieDAO to the observatie datastore.
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
class ObservatieMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","observatie","id",true,FM_TYPE_BIGINT,20,null,true);
			self::$FM["Node"] = new FieldMap("Node","observatie","node",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Sensor"] = new FieldMap("Sensor","observatie","sensor",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["DatumTijdAangemaakt"] = new FieldMap("DatumTijdAangemaakt","observatie","datum_tijd_aangemaakt",false,FM_TYPE_DATETIME,null,null,false);
			self::$FM["Waarde"] = new FieldMap("Waarde","observatie","waarde",false,FM_TYPE_VARCHAR,255,null,false);
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
			self::$KM["FK_alarm_observatie"] = new KeyMap("FK_alarm_observatie", "Id", "Alarm", "Observatie", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["FK_observatie_node"] = new KeyMap("FK_observatie_node", "Node", "Node", "DevEui", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			self::$KM["FK_observatie_sensor"] = new KeyMap("FK_observatie_sensor", "Sensor", "Sensor", "SensorId", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>