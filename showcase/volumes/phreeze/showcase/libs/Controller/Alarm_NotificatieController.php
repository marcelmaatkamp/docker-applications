<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Alarm_Notificatie.php");

/**
 * Alarm_NotificatieController is the controller class for the Alarm_Notificatie object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class Alarm_NotificatieController extends AppBaseController
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
		
		// DO SOME CUSTOM AUTHENTICATION FOR THIS PAGE
		$this->RequirePermission(User::$PERMISSION_EDIT,
				'SecureExample.LoginForm',
				'Please login to access this page',
				'');
	}

	/**
	 * Displays a list view of Alarm_Notificatie objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Alarm_Notificatie records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new Alarm_NotificatieCriteria();
			
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

				$alarm_notificaties = $this->Phreezer->Query('Alarm_Notificatie',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $alarm_notificaties->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $alarm_notificaties->TotalResults;
				$output->totalPages = $alarm_notificaties->TotalPages;
				$output->pageSize = $alarm_notificaties->PageSize;
				$output->currentPage = $alarm_notificaties->CurrentPage;
			}
			else
			{
				// return all results
				$alarm_notificaties = $this->Phreezer->Query('Alarm_Notificatie',$criteria);
				$output->rows = $alarm_notificaties->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Alarm_Notificatie record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm_notificatie = $this->Phreezer->Get('Alarm_Notificatie',$pk);
			$this->RenderJSON($alarm_notificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Alarm_Notificatie record and render response as JSON
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

			$alarm_notificatie = new Alarm_Notificatie($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $alarm_notificatie->Id = $this->SafeGetVal($json, 'id');

			$alarm_notificatie->AlarmRegel = $this->SafeGetVal($json, 'alarmRegel');
			$alarm_notificatie->Kanaal = $this->SafeGetVal($json, 'kanaal');
			$alarm_notificatie->P1 = $this->SafeGetVal($json, 'p1');
			$alarm_notificatie->P2 = $this->SafeGetVal($json, 'p2');
			$alarm_notificatie->P3 = $this->SafeGetVal($json, 'p3');
			$alarm_notificatie->P4 = $this->SafeGetVal($json, 'p4');
			$alarm_notificatie->Meldingtekst = $this->SafeGetVal($json, 'meldingtekst');

			$alarm_notificatie->Validate();
			$errors = $alarm_notificatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarm_notificatie->Save();
				$this->RenderJSON($alarm_notificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Alarm_Notificatie record and render response as JSON
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
			$alarm_notificatie = $this->Phreezer->Get('Alarm_Notificatie',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $alarm_notificatie->Id = $this->SafeGetVal($json, 'id', $alarm_notificatie->Id);

			$alarm_notificatie->AlarmRegel = $this->SafeGetVal($json, 'alarmRegel', $alarm_notificatie->AlarmRegel);
			$alarm_notificatie->Kanaal = $this->SafeGetVal($json, 'kanaal', $alarm_notificatie->Kanaal);
			$alarm_notificatie->P1 = $this->SafeGetVal($json, 'p1', $alarm_notificatie->P1);
			$alarm_notificatie->P2 = $this->SafeGetVal($json, 'p2', $alarm_notificatie->P2);
			$alarm_notificatie->P3 = $this->SafeGetVal($json, 'p3', $alarm_notificatie->P3);
			$alarm_notificatie->P4 = $this->SafeGetVal($json, 'p4', $alarm_notificatie->P4);
			$alarm_notificatie->Meldingtekst = $this->SafeGetVal($json, 'meldingtekst', $alarm_notificatie->Meldingtekst);

			$alarm_notificatie->Validate();
			$errors = $alarm_notificatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$alarm_notificatie->Save();
				$this->RenderJSON($alarm_notificatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Alarm_Notificatie record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$alarm_notificatie = $this->Phreezer->Get('Alarm_Notificatie',$pk);

			$alarm_notificatie->Delete();

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
