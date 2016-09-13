<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/AlarmNotificatie.php");

/**
 * AlarmNotificatieController is the controller class for the AlarmNotificatie object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class AlarmNotificatieController extends AppBaseController
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
	 * Displays a list view of AlarmNotificatie objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for AlarmNotificatie records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new AlarmNotificatieCriteria();
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,AlarmRegel,Kanaal,P1,P2,P3,P4,Meldingtekst'
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

				$alarmnotificaties = $this->Phreezer->Query('AlarmNotificatie',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $alarmnotificaties->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $alarmnotificaties->TotalResults;
				$output->totalPages = $alarmnotificaties->TotalPages;
				$output->pageSize = $alarmnotificaties->PageSize;
				$output->currentPage = $alarmnotificaties->CurrentPage;
			}
			else
			{
				// return all results
				$alarmnotificaties = $this->Phreezer->Query('AlarmNotificatie',$criteria);
				$output->rows = $alarmnotificaties->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single AlarmNotificatie record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarmnotificatie = $this->Phreezer->Get('AlarmNotificatie',$pk);
			$this->RenderJSON($alarmnotificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new AlarmNotificatie record and render response as JSON
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

			$alarmnotificatie = new AlarmNotificatie($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $alarmnotificatie->Id = $this->SafeGetVal($json, 'id');

			$alarmnotificatie->AlarmRegel = $this->SafeGetVal($json, 'alarmRegel');
			$alarmnotificatie->Kanaal = $this->SafeGetVal($json, 'kanaal');
			$alarmnotificatie->P1 = $this->SafeGetVal($json, 'p1');
			$alarmnotificatie->P2 = $this->SafeGetVal($json, 'p2');
			$alarmnotificatie->P3 = $this->SafeGetVal($json, 'p3');
			$alarmnotificatie->P4 = $this->SafeGetVal($json, 'p4');
			$alarmnotificatie->Meldingtekst = $this->SafeGetVal($json, 'meldingtekst');

			$alarmnotificatie->Validate();
			$errors = $alarmnotificatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarmnotificatie->Save();
				$this->RenderJSON($alarmnotificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing AlarmNotificatie record and render response as JSON
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
			$alarmnotificatie = $this->Phreezer->Get('AlarmNotificatie',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $alarmnotificatie->Id = $this->SafeGetVal($json, 'id', $alarmnotificatie->Id);

			$alarmnotificatie->AlarmRegel = $this->SafeGetVal($json, 'alarmRegel', $alarmnotificatie->AlarmRegel);
			$alarmnotificatie->Kanaal = $this->SafeGetVal($json, 'kanaal', $alarmnotificatie->Kanaal);
			$alarmnotificatie->P1 = $this->SafeGetVal($json, 'p1', $alarmnotificatie->P1);
			$alarmnotificatie->P2 = $this->SafeGetVal($json, 'p2', $alarmnotificatie->P2);
			$alarmnotificatie->P3 = $this->SafeGetVal($json, 'p3', $alarmnotificatie->P3);
			$alarmnotificatie->P4 = $this->SafeGetVal($json, 'p4', $alarmnotificatie->P4);
			$alarmnotificatie->Meldingtekst = $this->SafeGetVal($json, 'meldingtekst', $alarmnotificatie->Meldingtekst);

			$alarmnotificatie->Validate();
			$errors = $alarmnotificatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarmnotificatie->Save();
				$this->RenderJSON($alarmnotificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing AlarmNotificatie record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarmnotificatie = $this->Phreezer->Get('AlarmNotificatie',$pk);

			$alarmnotificatie->Delete();

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
