<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Alarm_Regel.php");

/**
 * Alarm_RegelController is the controller class for the Alarm_Regel object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class Alarm_RegelController extends AppBaseController
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
	 * Displays a list view of Alarm_Regel objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Alarm_Regel records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new Alarm_RegelCriteria();
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Node,Sensor,AlarmTrigger'
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

				$alarm_regels = $this->Phreezer->Query('Alarm_Regel',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $alarm_regels->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $alarm_regels->TotalResults;
				$output->totalPages = $alarm_regels->TotalPages;
				$output->pageSize = $alarm_regels->PageSize;
				$output->currentPage = $alarm_regels->CurrentPage;
			}
			else
			{
				// return all results
				$alarm_regels = $this->Phreezer->Query('Alarm_Regel',$criteria);
				$output->rows = $alarm_regels->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Alarm_Regel record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm_regel = $this->Phreezer->Get('Alarm_Regel',$pk);
			$this->RenderJSON($alarm_regel, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Alarm_Regel record and render response as JSON
	 */
	public function Create()
	{
		try
		{
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$alarm_regel = new Alarm_Regel($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $alarm_regel->Id = $this->SafeGetVal($json, 'id');

			$alarm_regel->Node = $this->SafeGetVal($json, 'node');
			$alarm_regel->Sensor = $this->SafeGetVal($json, 'sensor');
			$alarm_regel->AlarmTrigger = $this->SafeGetVal($json, 'alarmTrigger');

			$alarm_regel->Validate();
			$errors = $alarm_regel->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarm_regel->Save();
				$this->RenderJSON($alarm_regel, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Alarm_Regel record and render response as JSON
	 */
	public function Update()
	{
		try
		{
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm_regel = $this->Phreezer->Get('Alarm_Regel',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $alarm_regel->Id = $this->SafeGetVal($json, 'id', $alarm_regel->Id);

			$alarm_regel->Node = $this->SafeGetVal($json, 'node', $alarm_regel->Node);
			$alarm_regel->Sensor = $this->SafeGetVal($json, 'sensor', $alarm_regel->Sensor);
			$alarm_regel->AlarmTrigger = $this->SafeGetVal($json, 'alarmTrigger', $alarm_regel->AlarmTrigger);

			$alarm_regel->Validate();
			$errors = $alarm_regel->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarm_regel->Save();
				$this->RenderJSON($alarm_regel, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Alarm_Regel record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm_regel = $this->Phreezer->Get('Alarm_Regel',$pk);

			$alarm_regel->Delete();

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
