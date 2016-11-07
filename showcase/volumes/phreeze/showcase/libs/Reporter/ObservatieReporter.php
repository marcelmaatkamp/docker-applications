<?php
/** @package    Showcase::Reporter */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");


/**
 * This is an example Reporter based on the Observatie object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Showcase::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ObservatieReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `observatie` table
	
	public $Id;
	public $Node;
	public $NodeAlias;
	public $Sensor;
	public $SensorOmschrijving;
	public $DatumTijdAangemaakt;
	public $Waarde;
	public $SensorEenheid;

	/*
	* GetCustomQuery returns a fully formed SQL statement.  The result columns
	* must match with the properties of this reporter object.
	*
	* @see Reporter::GetCustomQuery
	* @param Criteria $criteria
	* @return string SQL statement
	*/
	static function GetCustomQuery($criteria)
	{
		$sql = "select
			`observatie`.`id` as Id
			,`observatie`.`node` as Node
			,`node`.`alias` as NodeAlias
			,`observatie`.`sensor` as Sensor
			,`sensor`.`omschrijving` as SensorOmschrijving
			,`sensor`.`eenheid` as SensorEenheid
			,`observatie`.`datum_tijd_aangemaakt` as DatumTijdAangemaakt
			,`observatie`.`waarde` as Waarde
		from `observatie`
		inner join node on node.dev_eui = observatie.node
		inner join sensor on sensor.sensor_id = observatie.sensor";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();
		$sql .= $criteria->GetOrder();

		return $sql;
	}
	
	/*
	* GetCustomCountQuery returns a fully formed SQL statement that will count
	* the results.  This query must return the correct number of results that
	* GetCustomQuery would, given the same criteria
	*
	* @see Reporter::GetCustomCountQuery
	* @param Criteria $criteria
	* @return string SQL statement
	*/
	static function GetCustomCountQuery($criteria)
	{
		$sql = "select count(1) as counter from `observatie`
		inner join node on node.dev_eui = observatie.node
		inner join sensor on sensor.sensor_id = observatie.sensor";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();

		return $sql;
	}
}

?>