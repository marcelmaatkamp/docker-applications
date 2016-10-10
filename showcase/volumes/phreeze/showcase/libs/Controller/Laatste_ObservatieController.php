<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Laatste_Observatie.php");

/**
 * Laatste_ObservatieController is the controller class for the Laatste_Observatie object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class Laatste_ObservatieController extends AppBaseController
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
		$this->RequirePermission(User::$PERMISSION_READ,
				'SecureExample.LoginForm',
				'Please login to access this page',
				'');
	}

	/**
	 * Displays a list view of Laatste_Observatie objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Laatste_Observatie records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new Laatste_ObservatieCriteria();
			$criteria->SetOrder('Node',true);
			
			//observer not working
			//$observer = new ObserveToBrowser();
			//$this->Phreezer->DataAdapter->AttachObserver($observer);
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Observatieid,Node,Sensor,Observatiewaarde,Observatiedatum'
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

				$laatste_observaties = $this->Phreezer->Query('Laatste_Observatie',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $laatste_observaties->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $laatste_observaties->TotalResults;
				$output->totalPages = $laatste_observaties->TotalPages;
				$output->pageSize = $laatste_observaties->PageSize;
				$output->currentPage = $laatste_observaties->CurrentPage;
			}
			else
			{
				// return all results
				$laatste_observaties = $this->Phreezer->Query('Laatste_Observatie',$criteria);
				$output->rows = $laatste_observaties->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Laatste_Observatie record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('observatieid');
			$laatste_observatie = $this->Phreezer->Get('Laatste_Observatie',$pk);
			$this->RenderJSON($laatste_observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Laatste_Observatie record and render response as JSON
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

			$laatste_observatie = new Laatste_Observatie($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			$laatste_observatie->Observatieid = $this->SafeGetVal($json, 'observatieid');
			$laatste_observatie->Node = $this->SafeGetVal($json, 'node');
			$laatste_observatie->Sensor = $this->SafeGetVal($json, 'sensor');
			$laatste_observatie->Observatiewaarde = $this->SafeGetVal($json, 'observatiewaarde');
			$laatste_observatie->Observatiedatum = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'observatiedatum')));

			$laatste_observatie->Validate();
			$errors = $laatste_observatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				// since the primary key is not auto-increment we must force the insert here
				$laatste_observatie->Save(true);
				$this->RenderJSON($laatste_observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Laatste_Observatie record and render response as JSON
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

			$pk = $this->GetRouter()->GetUrlParam('observatieid');
			$laatste_observatie = $this->Phreezer->Get('Laatste_Observatie',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $laatste_observatie->Observatieid = $this->SafeGetVal($json, 'observatieid', $laatste_observatie->Observatieid);

			$laatste_observatie->Node = $this->SafeGetVal($json, 'node', $laatste_observatie->Node);
			$laatste_observatie->Sensor = $this->SafeGetVal($json, 'sensor', $laatste_observatie->Sensor);
			$laatste_observatie->Observatiewaarde = $this->SafeGetVal($json, 'observatiewaarde', $laatste_observatie->Observatiewaarde);
			$laatste_observatie->Observatiedatum = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'observatiedatum', $laatste_observatie->Observatiedatum)));

			$laatste_observatie->Validate();
			$errors = $laatste_observatie->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$laatste_observatie->Save();
				$this->RenderJSON($laatste_observatie, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
	 * API Method deletes an existing Laatste_Observatie record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
			// TODO: views are read-only by default.  uncomment at your own discretion
			throw new Exception('Database views are read-only and cannot be updated');
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('observatieid');
			$laatste_observatie = $this->Phreezer->Get('Laatste_Observatie',$pk);

			$laatste_observatie->Delete();

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
