<?php
/** @package    AUTHEXAMPLE::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Role.php");
require_once("Model/User.php");

/**
 * RoleController is the controller class for the Role object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package AUTHEXAMPLE::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class RoleController extends AppBaseController
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
		$this->RequirePermission(User::$PERMISSION_ADMIN,
				'SecureExample.LoginForm',
				'Please login to access this page',
				'Admin permission is required to configure roles');
	}

	/**
	 * Displays a list view of Role objects
	 */
	public function ListView()
	{
		$this->Render();
	}

	/**
	 * API Method queries for Role records and render as JSON
	 */
	public function Query()
	{
		try
		{
			$criteria = new RoleCriteria();
			
			// TODO: this will limit results based on all properties included in the filter list 
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Name,CanAdmin,CanEdit,CanWrite,CanRead'
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

				$roles = $this->Phreezer->Query('Role',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $roles->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $roles->TotalResults;
				$output->totalPages = $roles->TotalPages;
				$output->pageSize = $roles->PageSize;
				$output->currentPage = $roles->CurrentPage;
			}
			else
			{
				// return all results
				$roles = $this->Phreezer->Query('Role',$criteria);
				$output->rows = $roles->ToObjectArray(true, $this->SimpleObjectParams());
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
	 * API Method retrieves a single Role record and render as JSON
	 */
	public function Read()
	{
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$role = $this->Phreezer->Get('Role',$pk);
			$this->RenderJSON($role, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new Role record and render response as JSON
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

			$role = new Role($this->Phreezer);

			// TODO: any fields that should not be inserted by the user should be commented out

			// this is an auto-increment.  uncomment if updating is allowed
			// $role->Id = $this->SafeGetVal($json, 'id');

			$role->Name = $this->SafeGetVal($json, 'name');
			$role->CanAdmin = $this->SafeGetVal($json, 'canAdmin');
			$role->CanEdit = $this->SafeGetVal($json, 'canEdit');
			$role->CanWrite = $this->SafeGetVal($json, 'canWrite');
			$role->CanRead = $this->SafeGetVal($json, 'canRead');

			$role->Validate();
			$errors = $role->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$role->Save();
				$this->RenderJSON($role, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing Role record and render response as JSON
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
			$role = $this->Phreezer->Get('Role',$pk);

			// TODO: any fields that should not be updated by the user should be commented out

			// this is a primary key.  uncomment if updating is allowed
			// $role->Id = $this->SafeGetVal($json, 'id', $role->Id);

			$role->Name = $this->SafeGetVal($json, 'name', $role->Name);
			$role->CanAdmin = $this->SafeGetVal($json, 'canAdmin', $role->CanAdmin);
			$role->CanEdit = $this->SafeGetVal($json, 'canEdit', $role->CanEdit);
			$role->CanWrite = $this->SafeGetVal($json, 'canWrite', $role->CanWrite);
			$role->CanRead = $this->SafeGetVal($json, 'canRead', $role->CanRead);

			$role->Validate();
			$errors = $role->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$role->Save();
				$this->RenderJSON($role, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing Role record and render response as JSON
	 */
	public function Delete()
	{
		try
		{
						
			// TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

			$pk = $this->GetRouter()->GetUrlParam('id');
			$role = $this->Phreezer->Get('Role',$pk);

			$role->Delete();

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
