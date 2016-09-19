<?php
/** @package    Showcase::Reporter */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the Laatste_Observatie object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Showcase::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class Laatste_ObservatieReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `laatste_observatie_per_node_sensor` table
	public $CustomFieldExample;

	public $Observatieid;
	public $Node;
	public $Sensor;
	public $Observatiewaarde;
	public $Observatiedatum;

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
			'custom value here...' as CustomFieldExample
			,`laatste_observatie_per_node_sensor`.`ObservatieId` as Observatieid
			,`laatste_observatie_per_node_sensor`.`Node` as Node
			,`laatste_observatie_per_node_sensor`.`Sensor` as Sensor
			,`laatste_observatie_per_node_sensor`.`ObservatieWaarde` as Observatiewaarde
			,`laatste_observatie_per_node_sensor`.`ObservatieDatum` as Observatiedatum
		from `laatste_observatie_per_node_sensor`";

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
		$sql = "select count(1) as counter from `laatste_observatie_per_node_sensor`";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();

		return $sql;
	}
}

?>