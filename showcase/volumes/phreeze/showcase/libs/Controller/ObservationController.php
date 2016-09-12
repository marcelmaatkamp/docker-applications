<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Observation.php");

/**
 * ObservationController is the controller class for the Observation object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class ObservationController extends AppBaseController
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
	 * Displays a list view of Observation objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Observation records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new ObservationCriteria();
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Version,DateCreated,NodeId,SensorId,Value'
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

				$observations = $this->Phreezer->Query('Observation',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $observations->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $observations->TotalResults;
				$output->totalPages = $observations->TotalPages;
				$output->pageSize = $observations->PageSize;
				$output->currentPage = $observations->CurrentPage;
			}
			else
			{
				// return all results
				$observations = $this->Phreezer->Query('Observation',$criteria);
				$output->rows = $observations->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Observation record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$observation = $this->Phreezer->Get('Observation',$pk);
			$this->RenderJSON($observation, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Observation record and render response as JSON
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

			$observation = new Observation($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $observation->Id = $this->SafeGetVal($json, 'id');

			$observation->Version = $this->SafeGetVal($json, 'version');
			$observation->DateCreated = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'dateCreated')));
			$observation->NodeId = $this->SafeGetVal($json, 'nodeId');
			$observation->SensorId = $this->SafeGetVal($json, 'sensorId');
			$observation->Value = $this->SafeGetVal($json, 'value');

			$observation->Validate();
			$errors = $observation->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$observation->Save();
				$this->RenderJSON($observation, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Observation record and render response as JSON
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
			$observation = $this->Phreezer->Get('Observation',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $observation->Id = $this->SafeGetVal($json, 'id', $observation->Id);

			$observation->Version = $this->SafeGetVal($json, 'version', $observation->Version);
			$observation->DateCreated = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'dateCreated', $observation->DateCreated)));
			$observation->NodeId = $this->SafeGetVal($json, 'nodeId', $observation->NodeId);
			$observation->SensorId = $this->SafeGetVal($json, 'sensorId', $observation->SensorId);
			$observation->Value = $this->SafeGetVal($json, 'value', $observation->Value);

			$observation->Validate();
			$errors = $observation->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$observation->Save();
				$this->RenderJSON($observation, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Observation record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$observation = $this->Phreezer->Get('Observation',$pk);

			$observation->Delete();

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
