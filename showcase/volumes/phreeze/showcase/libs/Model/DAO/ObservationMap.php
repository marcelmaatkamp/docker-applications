<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * ObservationMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the ObservationDAO to the observation datastore.
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
class ObservationMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","observation","id",true,FM_TYPE_BIGINT,20,null,true);
			self::$FM["Version"] = new FieldMap("Version","observation","version",false,FM_TYPE_BIGINT,20,null,false);
			self::$FM["DateCreated"] = new FieldMap("DateCreated","observation","date_created",false,FM_TYPE_DATETIME,null,null,false);
			self::$FM["NodeId"] = new FieldMap("NodeId","observation","node_id",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["SensorId"] = new FieldMap("SensorId","observation","sensor_id",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["Value"] = new FieldMap("Value","observation","value",false,FM_TYPE_VARCHAR,255,null,false);
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
			self::$KM["FK_27v5pji13cutepjuv9ox0glwp"] = new KeyMap("FK_27v5pji13cutepjuv9ox0glwp", "Id", "Alarm", "ObservationId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["FK_3vtmlnui6re2o9jq4vqpa2t06"] = new KeyMap("FK_3vtmlnui6re2o9jq4vqpa2t06", "SensorId", "Sensor", "SensorId", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			self::$KM["FK_smi270lm0koqq55tj5bfisawt"] = new KeyMap("FK_smi270lm0koqq55tj5bfisawt", "NodeId", "Node", "DevEui", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>