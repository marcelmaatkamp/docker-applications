<?php
/** @package    Showcase::Reporter */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the Alarm_Notificatie object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Showcase::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class Alarm_NotificatieReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `alarm_notificatie` table
	public $CustomFieldExample;

	public $Id;
	public $AlarmRegel;
	public $Kanaal;
	public $P1;
	public $P2;
	public $P3;
	public $P4;
	public $Meldingtekst;

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
			,`alarm_notificatie`.`id` as Id
			,`alarm_notificatie`.`alarm_regel` as AlarmRegel
			,`alarm_notificatie`.`kanaal` as Kanaal
			,`alarm_notificatie`.`p1` as P1
			,`alarm_notificatie`.`p2` as P2
			,`alarm_notificatie`.`p3` as P3
			,`alarm_notificatie`.`p4` as P4
			,`alarm_notificatie`.`meldingtekst` as Meldingtekst
		from `alarm_notificatie`";

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
		$sql = "select count(1) as counter from `alarm_notificatie`";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();

		return $sql;
	}
}

?>