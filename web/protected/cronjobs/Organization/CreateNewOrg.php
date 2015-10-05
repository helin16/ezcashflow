<?php
require_once dirname(__FILE__) . '/../../../bootstrap.php';

class CreateNewOrg
{
    const NEW_LINE = "\n";

    public static function run($params, $debug = false) {
        Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
        $args = self::_arguments($params);
        $paramKeys = array(
            'newOrg' => 'Organization Name',
            'firstName' => 'Firstname of the user',
            'lastName' => 'Lastname of the user',
            'email' => 'Email of the user',
            'initPass' => 'Initial Password of the user',
        );
        foreach($paramKeys as $key => $name) {
            if(self::_checkArgv($args, $key, $name) === false)
                die();
        }
        try{
            Dao::beginTransaction();
            $org = self::_createOrg($args['newOrg'], $debug);
            $userAccount = self::_createUser($args['email'], $args['firstName'], $args['lastName'], $args['initPass'], $org, $debug);
            self::_loadSampleAccounts($org, $debug);
            Dao::commitTransaction();
        } catch (Exception $ex) {
            Dao::rollbackTransaction();
            die($ex->getMessage() . self::NEW_LINE . $ex->getTraceAsString());
        }
    }

    private static function _checkArgv($params, $key, $name) {
        if(!isset($params[$key]) || ($argvValue = trim($params[$key])) === '') {
            self::_debug('No ' . $name . ' given to create a new org.', self::NEW_LINE, "", true);
            self::_debug('Usage: php ' . dirname(__FILE__) . ' --newOrg=new_org_name --firstName=user_first_name --lastName=user_last_name --email=user_email --initPass=user_init_pass', self::NEW_LINE, "", true);
            return false;
        }
        return true;
    }

    private static function _createOrg($orgName, $debug = false) {
        $org = Organization::create($orgName);
        return $org;
    }

    private static function _createUser($email, $firstName, $lastName, $password, Organization $org, $debug = false) {
        $person = Person::create($firstName, $lastName, $email)
            ->addRole($org, Role::get(Role::ID_ADMIN));
        self::_debug('Successfully created person with firstName="' . $firstName . '", lastName="' . $lastName . '", email="' . $email . '".', self::NEW_LINE, "\t", $debug);
        $userAccount = UserAccount::create($email, $password, $person, false)
            ->confirm();
        self::_debug('Successfully created useraccount with email="' . $email . '" and password="' . $password . '" and personId=' . $person->getId(), self::NEW_LINE, "\t", $debug);
        return $userAccount;
    }

    private static function _loadSampleAccounts(Organization $org, $debug = false) {
        self::_debug('Start loading Sample accounts: ', self::NEW_LINE, "\t", $debug);
        foreach(AccountType::getAll() as $accType) {
            self::_loadSampleAccountsByType($accType, $org, $debug);
        }
    }

    private static function _loadSampleAccountsByType(AccountType $type, Organization $org, $debug = false) {
         $rootAcc = AccountEntry::createRootAccount($org, $type->getName(), $type, true, 0, $type->getName(), $type->getId());
         self::_debug('Successfully create a root account: ' . $type->getName(), self::NEW_LINE, "\t\t", $debug);
         $accName = trim($type->getName() . ' - test account');
         $account = AccountEntry::create($org, $rootAcc, $accName, false, $accName, $type->getId() . '0001');
         self::_debug('Successfully create a child account: ' . $accName, self::NEW_LINE, "\t\t", $debug);
         return $account;
    }

    private static function _arguments($argv) {
        $_ARG = array ();
        foreach ( $argv as $arg ) {
            if (preg_match ( '/--([^=]+)=(.*)/', $arg, $reg )) {
                $_ARG [$reg [1]] = $reg [2];
            } elseif (preg_match ( '/-([a-zA-Z0-9])/', $arg, $reg )) {
                $_ARG [$reg [1]] = 'true';
            }
        }
        return $_ARG;
    }

    /**
     * Output the debug message
     *
     * @param string $msg
     *
     */
    private static function _debug($msg = "", $newLine = self::NEW_LINE, $prefix = "", $debug = false)
    {
        if($debug === true)
            echo $prefix . $msg . $newLine;
     }
}

CreateNewOrg::run($argv, true);