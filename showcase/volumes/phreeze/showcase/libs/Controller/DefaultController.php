<?php
/** @package Showcase::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/User.php");

/**
 * DefaultController is the entry point to the application
 *
 * @package Showcase::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class DefaultController extends AppBaseController
{

	/**
	 * Override here for any controller-specific functionality
	 */
	protected function Init()
	{
		parent::Init();

			$this->RequirePermission(User::$PERMISSION_READ,
				'SecureExample.LoginForm',
				'Please login to access this page',
				'Admin permission is required to configure roles');
				
				
			
	}

	/**
	 * Display the home page for the application
	 */
	public function Home()
	{
		$this->Render();
	}

	/**
	 * Displayed when an invalid route is specified
	 */
	public function Error404()
	{
		$this->Render();
	}

	/**
	 * Display a fatal error message
	 */
	public function ErrorFatal()
	{
		$this->Render();
	}

	public function ErrorApi404()
	{
		$this->RenderErrorJSON('An unknown API endpoint was requested.');
	}

}
?>