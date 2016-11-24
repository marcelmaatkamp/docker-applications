<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * RoleMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the RoleDAO to the role datastore.
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
class RoleMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","role","r_id",true,FM_TYPE_INT,10,null,true);
			self::$FM["Name"] = new FieldMap("Name","role","r_name",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["CanAdmin"] = new FieldMap("CanAdmin","role","r_can_admin",false,FM_TYPE_TINYINT,4,null,false);
			self::$FM["CanEdit"] = new FieldMap("CanEdit","role","r_can_edit",false,FM_TYPE_TINYINT,4,null,false);
			self::$FM["CanWrite"] = new FieldMap("CanWrite","role","r_can_write",false,FM_TYPE_TINYINT,4,null,false);
			self::$FM["CanRead"] = new FieldMap("CanRead","role","r_can_read",false,FM_TYPE_TINYINT,4,null,false);
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
			self::$KM["u_role"] = new KeyMap("u_role", "Id", "User", "RoleId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
		}
		return self::$KM;
	}

}

?>