<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * NodeThresholdMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the NodeThresholdDAO to the node_threshold datastore.
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
class NodeThresholdMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","node_threshold","id",true,FM_TYPE_BIGINT,20,null,true);
			self::$FM["Version"] = new FieldMap("Version","node_threshold","version",false,FM_TYPE_BIGINT,20,null,false);
			self::$FM["KeepaliveTimeout"] = new FieldMap("KeepaliveTimeout","node_threshold","keepalive_timeout",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["NodeId"] = new FieldMap("NodeId","node_threshold","node_id",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["RmqChannel"] = new FieldMap("RmqChannel","node_threshold","rmq_channel",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["SensorId"] = new FieldMap("SensorId","node_threshold","sensor_id",false,FM_TYPE_VARCHAR,255,null,false);
			self::$FM["State"] = new FieldMap("State","node_threshold","state",false,FM_TYPE_VARCHAR,255,null,false);
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
			self::$KM["FK_qqgttvcq7u148nkqjhx2hsbdi"] = new KeyMap("FK_qqgttvcq7u148nkqjhx2hsbdi", "Id", "Alarm", "NodeThresholdId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			self::$KM["FK_4stgr2ch3nidujfk8pial5sdv"] = new KeyMap("FK_4stgr2ch3nidujfk8pial5sdv", "NodeId", "Node", "DevEui", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			self::$KM["FK_afhhe5d4s0if67l0h6fxmdj08"] = new KeyMap("FK_afhhe5d4s0if67l0h6fxmdj08", "SensorId", "Sensor", "SensorId", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>