<?php
/** @package    SHOWCASE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/NodeThreshold.php");

/**
 * NodeThresholdController is the controller class for the NodeThreshold object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package SHOWCASE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class NodeThresholdController extends AppBaseController
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
	 * Displays a list view of NodeThreshold objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for NodeThreshold records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new NodeThresholdCriteria();
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Version,KeepaliveTimeout,NodeId,RmqChannel,SensorId,State'
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

				$nodethresholds = $this->Phreezer->Query('NodeThreshold',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $nodethresholds->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $nodethresholds->TotalResults;
				$output->totalPages = $nodethresholds->TotalPages;
				$output->pageSize = $nodethresholds->PageSize;
				$output->currentPage = $nodethresholds->CurrentPage;
			}
			else
			{
				// return all results
				$nodethresholds = $this->Phreezer->Query('NodeThreshold',$criteria);
				$output->rows = $nodethresholds->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single NodeThreshold record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$nodethreshold = $this->Phreezer->Get('NodeThreshold',$pk);
			$this->RenderJSON($nodethreshold, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new NodeThreshold record and render response as JSON
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

			$nodethreshold = new NodeThreshold($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $nodethreshold->Id = $this->SafeGetVal($json, 'id');

			$nodethreshold->Version = $this->SafeGetVal($json, 'version');
			$nodethreshold->KeepaliveTimeout = $this->SafeGetVal($json, 'keepaliveTimeout');
			$nodethreshold->NodeId = $this->SafeGetVal($json, 'nodeId');
			$nodethreshold->RmqChannel = $this->SafeGetVal($json, 'rmqChannel');
			$nodethreshold->SensorId = $this->SafeGetVal($json, 'sensorId');
			$nodethreshold->State = $this->SafeGetVal($json, 'state');

			$nodethreshold->Validate();
			$errors = $nodethreshold->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$nodethreshold->Save();
				$this->RenderJSON($nodethreshold, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing NodeThreshold record and render response as JSON
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
			$nodethreshold = $this->Phreezer->Get('NodeThreshold',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $nodethreshold->Id = $this->SafeGetVal($json, 'id', $nodethreshold->Id);

			$nodethreshold->Version = $this->SafeGetVal($json, 'version', $nodethreshold->Version);
			$nodethreshold->KeepaliveTimeout = $this->SafeGetVal($json, 'keepaliveTimeout', $nodethreshold->KeepaliveTimeout);
			$nodethreshold->NodeId = $this->SafeGetVal($json, 'nodeId', $nodethreshold->NodeId);
			$nodethreshold->RmqChannel = $this->SafeGetVal($json, 'rmqChannel', $nodethreshold->RmqChannel);
			$nodethreshold->SensorId = $this->SafeGetVal($json, 'sensorId', $nodethreshold->SensorId);
			$nodethreshold->State = $this->SafeGetVal($json, 'state', $nodethreshold->State);

			$nodethreshold->Validate();
			$errors = $nodethreshold->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$nodethreshold->Save();
				$this->RenderJSON($nodethreshold, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing NodeThreshold record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$nodethreshold = $this->Phreezer->Get('NodeThreshold',$pk);

			$nodethreshold->Delete();

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
