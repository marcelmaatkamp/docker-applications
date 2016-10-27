<?php
/** @package    Showcase::Reporter */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the Alarm_Regel object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Showcase::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class Alarm_RegelReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `alarm_regel` table
	public $CustomFieldExample;

	public $Id;
	public $Node;
	public $NodeAlias;
	public $Sensor;
	public $SensorOmschrijving;
	public $AlarmTrigger;

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
			`alarm_regel`.`id` as Id
			,`alarm_regel`.`node` as Node
			,`node`.`alias` as NodeAlias
			,`alarm_regel`.`sensor` as Sensor
			,`sensor`.`omschrijving` as SensorOmschrijving
			,`alarm_regel`.`alarm_trigger` as AlarmTrigger
		from `alarm_regel`
		inner join node on node.dev_eui = alarm_regel.node
		inner join sensor on sensor.sensor_id = alarm_regel.sensor";
	

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
		$sql = "select count(1) as counter from `alarm_regel`
		inner join node on node.dev_eui = alarm_regel.node
		inner join sensor on sensor.sensor_id = alarm_regel.sensor";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();

		return $sql;
	}
}

?>