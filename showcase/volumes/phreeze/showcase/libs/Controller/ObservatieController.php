<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Observatie.php");

/**
 * ObservatieController is the controller class for the Observatie object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class ObservatieController extends AppBaseController
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
				'Geen toegang tot deze pagina.');
	}

	/**
	 * Displays a list view of Observatie objects
	 */
	public function ListView()
	{
		$this->Phreezer->SetLoadType("Observatie","FK_observatie_node",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
		$this->Phreezer->SetLoadType("Observatie","FK_observatie_sensor",KM_LOAD_EAGER); // KM_LOAD_INNER | KM_LOAD_EAGER | KM_LOAD_LAZY
		
		
		$this->Render();
	}

	/**
	 * API Method queries for Observatie records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new ObservatieCriteria();
			$criteria->SetOrder('Id',true);
			$filternode = RequestUtil::Get('FilterNode');
			$filtersensor = RequestUtil::Get('FilterSensor');
			
			if ($filternode) $criteria->AddFilter(
				new CriteriaFilter('nodeAlias', '%'.$filternode.'%')
			);
			
			if ($filtersensor) $criteria->AddFilter(
				new CriteriaFilter('sensorOmschrijving', '%'.$filtersensor.'%')
			);
			
			
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Node,Sensor,DatumTijdAangemaakt,Waarde'
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

				$observaties = $this->Phreezer->Query('ObservatieReporter',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $observaties->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $observaties->TotalResults;
				$output->totalPages = $observaties->TotalPages;
				$output->pageSize = $observaties->PageSize;
				$output->currentPage = $observaties->CurrentPage;
			}
			else
			{
				// return all results
				$observaties = $this->Phreezer->Query('ObservatieReporter',$criteria);
				$output->rows = $observaties->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Observatie record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$observatie = $this->Phreezer->Get('Observatie',$pk);
			$this->RenderJSON($observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Observatie record and render response as JSON
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

			$observatie = new Observatie($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $observatie->Id = $this->SafeGetVal($json, 'id');

			$observatie->Node = $this->SafeGetVal($json, 'node');
			$observatie->Sensor = $this->SafeGetVal($json, 'sensor');
			$observatie->DatumTijdAangemaakt = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'datumTijdAangemaakt')));
			$observatie->Waarde = $this->SafeGetVal($json, 'waarde');

			$observatie->Validate();
			$errors = $observatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$observatie->Save();
				$this->RenderJSON($observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Observatie record and render response as JSON
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
			$observatie = $this->Phreezer->Get('Observatie',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $observatie->Id = $this->SafeGetVal($json, 'id', $observatie->Id);

			$observatie->Node = $this->SafeGetVal($json, 'node', $observatie->Node);
			$observatie->Sensor = $this->SafeGetVal($json, 'sensor', $observatie->Sensor);
			$observatie->DatumTijdAangemaakt = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'datumTijdAangemaakt', $observatie->DatumTijdAangemaakt)));
			$observatie->Waarde = $this->SafeGetVal($json, 'waarde', $observatie->Waarde);

			$observatie->Validate();
			$errors = $observatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$observatie->Save();
				$this->RenderJSON($observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Observatie record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$observatie = $this->Phreezer->Get('Observatie',$pk);

			$observatie->Delete();

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
