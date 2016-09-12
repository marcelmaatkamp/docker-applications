<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * AlarmNotificatieMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the AlarmNotificatieDAO to the alarm_notificatie datastore.
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
class AlarmNotificatieMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","alarm_notificatie","id",true,FM_TYPE_INT,11,null,true);
			self::$FM["AlarmRegel"] = new FieldMap("AlarmRegel","alarm_notificatie","alarm_regel",false,FM_TYPE_BIGINT,20,null,false);
			self::$FM["Kanaal"] = new FieldMap("Kanaal","alarm_notificatie","kanaal",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["P1"] = new FieldMap("P1","alarm_notificatie","p1",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["P2"] = new FieldMap("P2","alarm_notificatie","p2",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["P3"] = new FieldMap("P3","alarm_notificatie","p3",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["P4"] = new FieldMap("P4","alarm_notificatie","p4",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["Meldingtekst"] = new FieldMap("Meldingtekst","alarm_notificatie","meldingtekst",false,FM_TYPE_VARCHAR,45,null,false);
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
			self::$KM["fk_alarm_notificatie_alarm_regel"] = new KeyMap("fk_alarm_notificatie_alarm_regel", "AlarmRegel", "AlarmRegel", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>