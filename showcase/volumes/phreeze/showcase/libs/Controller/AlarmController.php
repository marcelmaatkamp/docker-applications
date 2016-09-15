<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Alarm.php");

/**
 * AlarmController is the controller class for the Alarm object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class AlarmController extends AppBaseController
{

	/**
	 * Override here for any controller-specific functionality
	 *
	 * @inheritdocs
	 */
	protected function Init()
	{
		parent::Init();

		// TODO: add controller-wide bootstrap code
		
		// TODO: if authentiation is required for this entire controller, for example:
		// $this->RequirePermission(ExampleUser::$PERMISSION_USER,'SecureExample.LoginForm');
	}

	/**
	 * Displays a list view of Alarm objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Alarm records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new AlarmCriteria();
			$criteria->SetOrder('Id',true);
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Node,Sensor,Alarmtrigger,Observatiewaarde,Observatietijdstip'
				, '%'.$filter.'%')
			);

			// TODO: this is generic query filtering based only on criteria properties
			foreach (array_keys($_REQUEST) as $prop)
			{
				$prop_normal = ucfirst($prop);
				$prop_equals = $prop_normal.'_Equals';

				if (property_exists($criteria, $prop_normal))
				{
					$criteria->$prop_normal = RequestUtil::Get($prop);
				}
				elseif (property_exists($criteria, $prop_equals))
				{
					// this is a convenience so that the _Equals suffix is not needed
					$criteria->$prop_equals = RequestUtil::Get($prop);
				}
			}

			$output = new stdClass();

			// if a sort order was specified then specify in the criteria
 			$output->orderBy = RequestUtil::Get('orderBy');
 			$output->orderDesc = RequestUtil::Get('orderDesc') != '';
 			if ($output->orderBy) $criteria->SetOrder($output->orderBy, $output->orderDesc);

			$page = RequestUtil::Get('page');

			if ($page != '')
			{
				// if page is specified, use this instead (at the expense of one extra count query)
				$pagesize = $this->GetDefaultPageSize();

				$alarmen = $this->Phreezer->Query('Alarm',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $alarmen->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $alarmen->TotalResults;
				$output->totalPages = $alarmen->TotalPages;
				$output->pageSize = $alarmen->PageSize;
				$output->currentPage = $alarmen->CurrentPage;
			}
			else
			{
				// return all results
				$alarmen = $this->Phreezer->Query('Alarm',$criteria);
				$output->rows = $alarmen->ToObjectArray(true, $this->SimpleObjectParams());
				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
			}


			$this->RenderJSON($output, $this->JSONPCallback());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method retrieves a single Alarm record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm = $this->Phreezer->Get('Alarm',$pk);
			$this->RenderJSON($alarm, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Alarm record and render response as JSON
	 */
	public function Create()
	{
		try
		{
			// TODO: views are read-only by default.  uncomment at your own discretion
			throw new Exception('Database views are read-only and cannot be updated');
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$alarm = new Alarm($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			$alarm->Id = $this->SafeGetVal($json, 'id');
			$alarm->Node = $this->SafeGetVal($json, 'node');
			$alarm->Sensor = $this->SafeGetVal($json, 'sensor');
			$alarm->Alarmtrigger = $this->SafeGetVal($json, 'alarmtrigger');
			$alarm->Observatiewaarde = $this->SafeGetVal($json, 'observatiewaarde');
			$alarm->Observatietijdstip = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'observatietijdstip')));

			$alarm->Validate();
			$errors = $alarm->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				// since the primary key is not auto-increment we must force the insert here
				$alarm->Save(true);
				$this->RenderJSON($alarm, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Alarm record and render response as JSON
	 */
	public function Update()
	{
		try
		{
			// TODO: views are read-only by default.  uncomment at your own discretion
			throw new Exception('Database views are read-only and cannot be updated');
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm = $this->Phreezer->Get('Alarm',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $alarm->Id = $this->SafeGetVal($json, 'id', $alarm->Id);

			$alarm->Node = $this->SafeGetVal($json, 'node', $alarm->Node);
			$alarm->Sensor = $this->SafeGetVal($json, 'sensor', $alarm->Sensor);
			$alarm->Alarmtrigger = $this->SafeGetVal($json, 'alarmtrigger', $alarm->Alarmtrigger);
			$alarm->Observatiewaarde = $this->SafeGetVal($json, 'observatiewaarde', $alarm->Observatiewaarde);
			$alarm->Observatietijdstip = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'observatietijdstip', $alarm->Observatietijdstip)));

			$alarm->Validate();
			$errors = $alarm->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarm->Save();
				$this->RenderJSON($alarm, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{

			// this table does not have an auto-increment primary key, so it is semantically correct to
			// issue a REST PUT request, however we have no way to know whether to insert or update.
			// if the record is not found, this exception will indicate that this is an insert request
			if (is_a($ex,'NotFoundException'))
			{
				return $this->Create();
			}

			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Alarm record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
			// TODO: views are read-only by default.  uncomment at your own discretion
			throw new Exception('Database views are read-only and cannot be updated');
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm = $this->Phreezer->Get('Alarm',$pk);

			$alarm->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
}

?>
