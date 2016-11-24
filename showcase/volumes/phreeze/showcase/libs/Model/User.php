<?php
/** @package    AuthExample::Model */

/** import supporting libraries */
require_once("DAO/UserDAO.php");
require_once("UserCriteria.php");

// #### ALL AUTHENTICATION-RELATED CHANGES ARE COMMENTED IN ALL-CAPS ####

// INCLUDE FILES FOR AUTHENTICATION
require_once("verysimple/Authentication/IAuthenticatable.php");

// BACKWARDS COMPATIBILITY FILE ADDS "password_hash" AND "password_verify" FUNCTIONS
require_once("util/password.php");

/**
 * NOTICE THAT THIS CLASS IMPLEMENTS THE "IAuthenticatable" INTERFACE
 * SO THAT IT CAN BE USED BY PHREEZE AS A "CURRENT USER"
 * 
 * The User class extends UserDAO which provides the access
 * to the datastore.
 *
 * @package AuthExample::Model
 * @author ClassBuilder
 * @version 1.0
 */
class User extends UserDAO implements IAuthenticatable
{

	static $PERMISSION_READ = 1;
	static $PERMISSION_WRITE = 2;
	static $PERMISSION_EDIT = 4;
	static $PERMISSION_ADMIN = 8;
	
	/**
	 * {@inheritdoc}
	 */
	public function IsAnonymous()
	{
		// ANY ACCOUNT THAT WAS LOADED FROM THE DB IS NOT CONSIDERED TO BE AN ANONYMOUS USER
		return $this->IsLoaded();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function IsAuthorized($permission)
	{
		// THIS COULD BE MADE MORE EFFICIENT BY CACHING THE ROLE VARIABLE
		// OR JUST HARD-CODING ROLE NAMES AND PERMISSIONS SO YOU DON'T
		// HAVE TO DO A DATABASE LOOKUP ON THE ROLE TABLE EVERY TIME
		
		// GET THE ROLE FOR THIS USER
		$role = $this->GetRole();
		
		// IF THE PERMISSION BEING REQUESTED IS SOMETHING THAT THIS USER'S ROLE HAS, THEN THEY ARE AUTHORIZED
		if ($permission == self::$PERMISSION_READ && $role->CanRead) return true;
		if ($permission == self::$PERMISSION_WRITE && $role->CanWrite) return true;
		if ($permission == self::$PERMISSION_EDIT && $role->CanEdit) return true;
		if ($permission == self::$PERMISSION_ADMIN && $role->CanAdmin) return true;
		
		// IF THERE WERE NO MATCHES THEN THAT MEANS THIS USER DOESNT' HAVE THE REQUESTED PERMISSION
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function Login($username,$password)
	{
		// IF THERE IS NO USERNAME THEN DON'T BOTHER CHECKING THE DATABASE
		if (!$username) return false;
		
		$result = false;
		
		$criteria = new UserCriteria();
		$criteria->Username_Equals = $username;
		
		try {
			$user = $this->_phreezer->GetByCriteria("User", $criteria);
			
			// WE NEED TO STRIP OFF THE "!!!" PREFIX THAT WAS ADDED IN "OnSave" BELOW:
			$hash = substr($user->Password, 3);
			
			if (password_verify($password, $hash))
			{
				// THE USERNAME/PASSWORD COMBO IS CORRECT!
				
				// WHAT THIS IS DOING IS BASICALLY CLONING THE USER RESULT
				// FROM THE DATABASE INTO THE CURRENT RECORD.
				$this->LoadFromObject($user);
				
				$result = true;
			}
			else
			{
				// THE USERNAME WAS FOUND BUT THE PASSWORD DIDN'T MATCH
				$result = false;
			}
			
		}
		catch (NotFoundException $nfex) {
			
			// NO ACCOUNT WAS FOUND WITH THE GIVEN USERNAME
			$result = false;
		}
		
		return $result;
	}
	
	/**
	 * Override default validation
	 * @see Phreezable::Validate()
	 */
	public function Validate()
	{
		// EXAMPLE OF CUSTOM VALIDATION LOGIC
		$this->ResetValidationErrors();
		$errors = $this->GetValidationErrors();

		// THESE ARE CUSTOM VALIDATORS
		if (!$this->Username) $this->AddValidationError('Username','Username is required');
		if (!$this->Password) $this->AddValidationError('Password','Password is required');
		
		return !$this->HasValidationErrors();
	}

	/**
	 * @see Phreezable::OnSave()
	 */
	public function OnSave($insert)
	{
		// the controller create/update methods validate before saving.  this will be a
		// redundant validation check, however it will ensure data integrity at the model
		// level based on validation rules.  comment this line out if this is not desired
		if (!$this->Validate()) throw new Exception('Unable to Save Role: ' .  implode(', ', $this->GetValidationErrors()));

		
		// WE NEVER WANT TO SAVE THE PASSWORD FIELD AS PLAIN TEXT.  SO, WE'LL DO A CHECK TO MAKE
		// SURE IT IS ENCRYPTED AND, IF NOT, THEN WE WILL ENCRYPT IT.  HOWEVER IT IS IMPORTANT
		// THAT WE DON'T DOUBLE-ENCRYPT THE PASSWORD SO WE NEED SOME WAY TO INDICATE WHETHER THE 
		// PASSWORD IS ALREADY HASHED OR NOT.  JUST AS AN EXAMPLE, WE'LL PREFIX IT WITH "!!!"
		// WHEN WE HASH THE PASSWORD.  WE'LL JUST IGNORE THAT PREFIX IN THE LOGIN FUNCTION.
		// FEEL FREE TO CHANGE THAT MECHANISM TO WHATEVER WORKS FOR YOUR OWN SYSTEM
		if (substr($this->Password, 0,3) != '!!!')
		{
			// the password is in plain-text, so we need to hash it before saving
			$this->Password = '!!!' . password_hash($this->Password, PASSWORD_DEFAULT);
		}

		// OnSave must return true or eles Phreeze will cancel the save operation
		return true;
	}

}

?>