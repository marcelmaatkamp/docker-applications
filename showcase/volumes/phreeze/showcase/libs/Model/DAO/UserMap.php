<?php
/** @package    Showcase::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * UserMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the UserDAO to the user datastore.
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
class UserMap implements IDaoMap, IDaoMap2
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
			self::$FM["Id"] = new FieldMap("Id","user","a_id",true,FM_TYPE_INT,10,null,true);
			self::$FM["RoleId"] = new FieldMap("RoleId","user","a_role_id",false,FM_TYPE_INT,10,null,false);
			self::$FM["Username"] = new FieldMap("Username","user","a_username",false,FM_TYPE_VARCHAR,150,null,false);
			self::$FM["Password"] = new FieldMap("Password","user","a_password",false,FM_TYPE_VARCHAR,150,null,false);
			self::$FM["FirstName"] = new FieldMap("FirstName","user","a_first_name",false,FM_TYPE_VARCHAR,45,null,false);
			self::$FM["LastName"] = new FieldMap("LastName","user","a_last_name",false,FM_TYPE_VARCHAR,45,null,false);
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
			self::$KM["u_role"] = new KeyMap("u_role", "RoleId", "Role", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return self::$KM;
	}

}

?>