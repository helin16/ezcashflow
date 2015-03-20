<?php
/**
 * This is the User::Details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends DetailsPageAbstract
{
	private $_entity;
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		if($this->_entity instanceof BaseEntityAbstract)
			$js .= 'pageJs.init();';
		return $js;
	}
	/**
	 * (non-PHPdoc)
	 * @see DetailsPageAbstract::_getEntity()
	 */
	protected function _getEntity()
	{
		$entity = $parent = $type = null;

		if(!isset($this->Request['id']) || ($id = trim($this->Request['id'])) === '')
			return $entity;

		if($id === 'new') {
			$entity = new UserAccount();
			$entity->setPerson(new Person());
		} else if ($id === 'me')
			$entity = Core::getUser();
		else
			$entity = UserAccount::get($id);
		$this->_entity = $entity;
		return $entity;
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $params)
	{
		$results = $errors = array();
		try {
			Dao::beginTransaction();
			if(!isset($params->CallbackParameter->firstName) || ($firstName = trim($params->CallbackParameter->firstName)) === '')
				throw new Exception('FirstName is required.');
			if(!isset($params->CallbackParameter->lastName) || ($lastName = trim($params->CallbackParameter->lastName)) === '')
				throw new Exception('LastName is required.');
			if(!isset($params->CallbackParameter->email) || ($email = trim($params->CallbackParameter->email)) === '')
				throw new Exception('Email is required.');

			if(isset($params->CallbackParameter->userId)) {
				if(!($userAccount = UserAccount::get($params->CallbackParameter->userId)) instanceof UserAccount)
					throw new Exception('System Error: invalid user id');
				if($userAccount->getPerson() instanceof Person)
					$userAccount->getPerson()
						->setFirstName($firstName)
						->setLastName($lastName)
						->setEmail($email)
						->save();
				if(isset($params->CallbackParameter->password) && ($password = trim($params->CallbackParameter->password)) !== '') {
					if(!isset($params->CallbackParameter->confirmPassword) || $password !== trim($params->CallbackParameter->confirmPassword))
						throw new Exception('Confirm password is NOT matched with password!');
					$userAccount = UserAccount::updateUser($userAccount, $email, $password, false);
				}
			} else {
				if(!isset($params->CallbackParameter->password) || ($password = trim($params->CallbackParameter->password)) === '')
					throw new Exception('Password Required.');
				if(!isset($params->CallbackParameter->confirmPassword) || $password !== trim($params->CallbackParameter->confirmPassword))
					throw new Exception('Confirm password is NOT matched with password!');
				$userAccount = UserAccount::create($email, $password, Person::create($firstName, $lastName, $email), false);
			}
			$results['item'] = $userAccount->getJson();
			Dao::commitTransaction();

		} catch(Exception $ex) {
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>