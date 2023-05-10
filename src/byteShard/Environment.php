<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Database\Enum\ConnectionType;
use byteShard\Database\Struct\Parameters;
use byteShard\Enum\LogLevel;
use byteShard\Form\Enum\Label\Position;
use byteShard\Form\Settings;
use byteShard\Internal\Authentication\AuthenticationInterface;
use byteShard\Internal\Config;
use byteShard\Enum;
use byteShard\Authentication;
use byteShard\Internal\ByteShard\Css;
use byteShard\Internal\ByteShard\Javascript;
use byteShard\Internal\Database\ParametersInterface;
use byteShard\Internal\Schema\DB\UserTable;
use byteShard\Internal\Session;
use JsonSerializable;
use Monolog\Formatter\FormatterInterface;
use stdClass;
use byteShard\Internal\Login;
use byteShard\Authentication\Enum\Action;
use byteShard\Internal\ErrorHandler;
use byteShard\Internal\Server;
use byteShard\Internal\TabParentInterface;

/**
 * Class Environment
 * @exceptionId 00004
 * @package byteShard
 */
abstract class Environment implements ParametersInterface, JsonSerializable
{
    const DRIVER_MySQL_mysqli   = 'mysql_mysqli';
    const DRIVER_MYSQL_PDO      = 'mysql_pdo';
    const DRIVER_PGSQL_PDO      = 'pgsql_pdo';
    const DRIVER_MSSQL_ado      = 'mssql_ado';
    const DRIVER_SQLITE_sqlite3 = 'sqlite_sqlite3';

    /* Example:
    * User     accTarget     authentication_target
    * 1        db            db
    * 2        db            ldap
    * 3        ldap          ldap
    * 4        ldap          db
    * 1: if the user is allowed to log in depends on a field "grantLogin" on the DB. If true, authentication will be done against a hash stored on the DB
    * 2: if the user is allowed to log in depends on a field "grantLogin" on the DB. If true, authentication will be done against the defined ldap host
    * 3: if the user is allowed to log in depends on a successful authentication against the defined ldap host. Access can be restricted by using ldap groups
    * 4: this usually makes no sense, since you need to authenticate against the ldap to check group permissions and then authenticate against a DB stored password...
    * accTarget and authentication_target can be defined in application Environment or if AUTH_TARGET_DEFINED_ON_DB &&|| ACCESS_CONTROL_DEFINED_ON_DB are used, accTarget &&|| authentication_target can be stored on a per-user basis on the DB
    * ('db' or 'ldap' must be stored in the respective fields)
    */

    protected bool    $require_ssl                   = true;
    protected int     $sessionTimeoutInMinutes       = 240; //after that many minutes without any action, the user has to re-login to continue
    static public int $zlib_output_compression       = 4096;
    static public int $zlib_output_compression_level = 9;
    protected bool    $service_mode                  = false;       //changing service_mode to true will immediately log out all users on their next request and prevent future logins unless User has a serviceAccount
    protected string  $main                          = 'byteShard'; //changing this will impact certain functionality
    public string     $dbCharset                     = 'iso-8859-1';
    public string     $clientCharset                 = 'utf8';
    public string     $phpCharset                    = 'utf8';
    protected bool    $use_single_user_token         = true;

    /*public        $db_time_format;
    public        $client_date_format = '%d.%m.%Y';
    public        $client_time_format = '%H:%i:%s';
    public        $client_datetime_format = '%d.%m.%Y %H:%i:%s';*/

    /**
     * @var string
     */
    protected string $locale = 'en';

    /**
     * List all supported locales depending on the application
     * @var array
     */
    protected array $locales = ['en'];

    /**
     * environment which can be configured in config
     * @var string
     */
    protected string $environment = Config::PRODUCTION;

    // Date and Time related parameters
    //TODO: client formats by locale
    protected string $client_timezone = 'GMT+2';

    protected string $client_form_control_calendar_default_db_column_type = Enum\DB\ColumnType::DATETIME2;
    protected string $client_grid_column_calendar_default_db_column_type  = Enum\DB\ColumnType::DATETIME2;
    protected string $client_grid_column_date_default_db_column_type      = Enum\DB\ColumnType::DATETIME2;
    protected string $db_meta_data_column_created_on_column_type          = Enum\DB\ColumnType::DATETIME2;
    protected string $db_meta_data_column_modified_on_column_type         = Enum\DB\ColumnType::DATETIME2;
    protected string $db_meta_data_column_archived_on_column_type         = Enum\DB\ColumnType::DATETIME2;

    protected string $db_timezone                        = 'UTC';
    protected string $db_column_date_format              = 'Y-m-d';
    protected string $db_column_smalldatetime_format     = 'Y-m-d H:i:s';
    protected string $db_column_datetime_format          = 'Y-m-d H:i:s.u';
    protected int    $db_column_datetime_precision       = 3;
    protected string $db_column_datetime2_format         = 'Y-m-d H:i:s.u';
    protected int    $db_column_datetime2_precision      = 7;
    protected string $db_column_datetimeoffset_format    = 'Y-m-d H:i:s.u';
    protected int    $db_column_datetimeoffset_precision = 7;
    protected string $db_column_bigintdate_format        = 'YmdHis';
    protected string $db_column_time_format              = 'H:i:s.u';
    protected int    $db_column_time_precision           = 7;

    // Paths
    protected string  $appDir        = 'application';
    protected string  $cssPath       = 'css';
    protected string  $libPath       = 'lib';
    protected string  $jsPath        = 'js';
    protected string  $uploadDir     = 'upload';
    protected ?string $logDir        = null;   // if defined this must be a full qualified path starting with '/' or a drive letter and a trailing (back-)slash
    protected string  $defaultLogDir = 'logs'; // unless a logDir is specified, this directory will be used for all logging. This path will be prepended by the filerootDir

    // Files
    protected string $errorLog = 'error.log';
    protected string $debugLog = 'debug.log';

    // NEEDS TO BE SET IN ApplicationEnvironment
    // Database
    protected string $dbDriver = self::DRIVER_MySQL_mysqli;

    /**
     * @var string
     */
    protected string $database;

    /**
     * @var Parameters
     */
    protected Parameters $db_parameters_admin;

    /**
     * @var Parameters
     */
    protected Parameters $db_parameters_login;

    /**
     * @var Parameters
     */
    protected Parameters $db_parameters_read;

    /**
     * @var Parameters
     */
    protected Parameters $db_parameters_write;

    // Application
    protected string $favicon = '';
    /**
     * @var string
     */
    protected string $application_name = '';
    protected string $leftFooterText   = '';
    // Login related
    protected string  $ldap_host;
    protected string  $ldap_user;
    protected ?string $ldap_pass           = null;
    protected string  $ldap_port;
    protected string  $ldap_domain;
    protected bool    $lower_case_username = true;

    /**
     * ### DEBUGGING ###
     */
    /**
     * this will enable/disable all development related debug information
     * @var bool
     */
    protected bool $debug = false;

    /**
     * this will use the locale marked as debug
     * self::$debug overrides this setting
     *
     * @var bool $debug_locale
     */
    protected bool $debug_locale = false;

    /**
     * this will display the locale token of every single item
     * self::$debug overrides this setting
     *
     * @var bool $debug_locale_token
     */
    protected bool $debug_locale_token = false;

    /**
     * if true IDs won't be encrypted
     * self::$debug overrides this setting
     *
     * @var bool $debug_id
     */
    protected bool $debug_id = false;

    protected string $logoffButtonName = 'logoff';

    /**
     * this will control how the user credentials are challenged
     * @var Authentication\Enum\Target
     */
    protected Authentication\Enum\Target $authentication_target = Authentication\Enum\Target::AUTH_TARGET_DB;

    /**
     * setting this parameter will change the behavior how the application determines if a user is allowed to log in
     * this is necessary since there might be many records in the user table which are not actually users but user data used for a different purpose
     * @var Enum\AccessControlTarget
     */
    protected Enum\AccessControlTarget             $access_control_target = Enum\AccessControlTarget::ACCESS_CONTROLLED_BY_DB;
    protected bool                                 $showForgotPass        = false;
    protected \byteShard\Internal\Schema\LoginForm $loginFormSchema;

    // User related
    protected UserTable $user_table_schema;

    /**
     * if logout is true the destructor of this class will end the session
     * @var bool
     */
    private bool $logout = false;

    /**
     * optional method for application environment
     * @return bool
     */
    protected function initializeApplicationGlobals(): bool
    {
        return true;
    }

    /**
     * optional method for application environment
     * @return bool
     */
    protected function initializeApplicationBeforeAuthentication(): bool
    {
        return true;
    }

    /**
     * optional method for application environment
     * @return bool
     */
    protected function initializeApplicationAfterAuthentication(): bool
    {
        return true;
    }

    /**
     * optional method to specify user specific permissions to be used throughout the application
     * @return ?Permission
     */
    protected function initializePermissions(): ?Permission
    {
        return null;
    }

    /**
     * @param TabParentInterface $tab_parent
     * @return boolean
     */
    //abstract protected function initializeTabs(TabParentInterface $tab_parent);

    /**
     * optional method for application environment
     */
    protected function addCssTagsToHtmlHead(): array
    {
        $styleSheets = [];
        if (method_exists($this, 'getApplicationCss')) {
            trigger_error('getApplicationCss is deprecated. Please use addCssTagsToHtmlHead instead');
            $legacyApplicationCss = $this->getApplicationCss();
            if (is_string($legacyApplicationCss)) {
                $styleSheets[] = $legacyApplicationCss;
            } else {
                array_push($styleSheets, ...$legacyApplicationCss);
            }
        }
        return $styleSheets;
    }

    /**
     * returns array of <link> tags for the head
     * @return array
     */
    protected function addDhtmlxCSSTagsToHtmlHead(): array
    {
        return Css::includeCssFullPath(['dhx/css/dhtmlx5.css']);
    }

    /**
     * returns array of <link> tags for the head
     * @return array
     */
    protected function addApplicationCSSTagsToHtmlHead(): array
    {
        return Css::includeCssFullPath(['app/css/main.css']);
    }

    /**
     * optional method for application environment
     */
    protected function addJavascriptTagsToHtmlHead(): array
    {
        $scripts = [];
        if (method_exists($this, 'getApplicationJavascripts')) {
            trigger_error('getApplicationJavascripts is deprecated. Please use addJavascriptTagsToHtmlHead instead');
            $legacyApplicationJavaScript = $this->getApplicationJavascripts();
            if (is_string($legacyApplicationJavaScript)) {
                $scripts[] = $legacyApplicationJavaScript;
            } else {
                array_push($scripts, ...$legacyApplicationJavaScript);
            }
        }
        return $scripts;
    }

    /**
     * returns array of <script> tags for the head
     * @return array
     */
    protected function addDhtmlxJSTagsToHtmlHead(): array
    {
        return Javascript::includeJavascriptFullPath(['dhx/js/dhtmlx5.js']);
    }

    /**
     * returns array of <script> tags for the head
     * @return array
     */
    protected function addApplicationJSTagsToHtmlHead(): array
    {
        return Javascript::includeJavascriptFullPath(['app/js/main.js']);
    }

    /**
     * @return AuthenticationInterface
     */
    protected function getApplicationAuthenticationObject(): AuthenticationInterface
    {
        return new Authentication\DB(new UserTable());
    }

    /**
     *
     */
    protected function printLoginForm(): void
    {
        $template = new Login\Template(
            $this->loginFormSchema,
            $this->getJavascripts(['login.js'], '', 'bs'),
            $this->getCss(['login.css'], '', 'bs'),
            $this->favicon,
            $this->getAppName()
        );
        $template->printLoginForm();
    }

    protected function printLoginForm_failed($secondsToWait = 0): void
    {
        $this->printLoginForm();
    }

    protected function printLoginForm_error($fileaccess = true): void
    {
        $this->printLoginForm();
    }

    protected function printLoginForm_loggedout(): void
    {
        $this->printLoginForm();
    }

    protected function printLoginForm_passwordExpired(): void
    {
        $this->printLoginForm();
    }

    protected function printLoginForm_authTargetUnreachable(): void
    {
        $this->printLoginForm();
    }

    protected function printChangePasswordForm(): void
    {
        $this->printLoginForm();
    }

    protected function printChangePasswordForm_newPassDontMatch(): void
    {
        $this->printLoginForm();
    }

    protected function printChangePasswordForm_oldPassInvalid(): void
    {
        $this->printLoginForm();
    }

    protected function printChangePasswordForm_newPassUsed(): void
    {
        $this->printLoginForm();
    }

    protected function printChangePasswordForm_newPassInvalid(): void
    {
        $this->printLoginForm();
    }

    protected function printForgotPasswordForm(): void
    {
        $this->printLoginForm();
    }

    protected function setApplicationLoginFormSchema(): void
    {
        $this->loginFormSchema = new \byteShard\Internal\Schema\LoginForm();
    }

    protected function construct(Config $config)
    {
        $this->environment = $config->getEnvironment();
        $this->initializeFrameworkGlobals($config->getLogLevel(), $config->getUrlContext());
        $this->user_table_schema   = new UserTable();
        $this->db_parameters_admin = $config->getDbParameters(ConnectionType::ADMIN);
        $this->db_parameters_login = $config->getDbParameters(ConnectionType::LOGIN);
        $this->db_parameters_read  = $config->getDbParameters(ConnectionType::READ);
        $this->db_parameters_write = $config->getDbParameters(ConnectionType::WRITE);
        $this->database            = $this->db_parameters_read->database;
    }

    /**
     * @return FormatterInterface|null
     */
    public function getLogFormatter(): FormatterInterface|null
    {
        return null;
    }

    /**
     * @return UserTable
     */
    public function getUserTableSchema(): UserTable
    {
        return $this->user_table_schema;
    }

    public function getAuthenticationTarget(): Authentication\Enum\Target
    {
        return $this->authentication_target;
    }

    /**
     * @return bool
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return string|null
     */
    public function getDhtmlxCssImagePath(): ?string
    {
        return null;
    }

    /**
     * override in application environment in case you want to pass application specific settings
     * @param int|null $labelWidth
     * @param int|null $inputWidth
     * @param Position|null $position
     * @return Settings
     */
    public function getFormSettings(?int $labelWidth = null, ?int $inputWidth = null, ?Position $position = null): Settings
    {
        $formSettings = new Settings();
        if ($labelWidth !== null) {
            $formSettings->setLabelWidth($labelWidth);
        }
        if ($inputWidth !== null) {
            $formSettings->setInputWidth($inputWidth);
        }
        if ($position !== null) {
            $formSettings->setPosition($position);
        }

        return $formSettings;
    }

    /**
     * override in application environment in case a specific content needs to be displayed
     */
    public function getNoApplicationPermissionContent(): object
    {
        $result              = new stdClass();
        $result->label_width = 500;
        $result->tab_label   = Locale::get('byteShard.environment.tab.label.noPermission');
        $result->labels[]    = sprintf(Locale::get('byteShard.environment.cell.label.noPermission'), $this->application_name);
        return $result;
    }

    /**
     * @param \byteShard\ID\ID $lastTabId
     */
    public function setLastTab(\byteShard\ID\ID $lastTabId): void
    {
        $lastTab = $lastTabId->getTabId();
        $userId  = \byteShard\Session::getUserId();
        if ($userId !== null) {
            $model = $this->getByteShardDataModel();
            $model->setLastTab($userId, $lastTab, $this->user_table_schema);
        }
    }

    protected function getByteShardDataModel(): DataModelInterface
    {
        return new \byteShard\Database\Model();
    }

    /**
     * @param $tabName
     * @param $cellName
     * @param $type
     * @param $item
     * @param $value
     */
    public function storeUserSetting($tabName, $cellName, $type, $item, $value)
    {
        if ($_SESSION[MAIN] instanceof Session) {
            $model = $this->getByteShardDataModel();
            $model->storeUserSetting($tabName, $cellName, $type, $item, $_SESSION[MAIN]->getUserID(), $value);
        }
    }

    /**
     * @param string $tabName
     * @param string $cellName
     * @param string $type
     * @param string $item
     */
    public function deleteUserSetting(string $tabName, string $cellName, string $type, string $item)
    {
        if ($_SESSION[MAIN] instanceof Session) {
            $tabName = str_replace('\\', '\\\\', $tabName);
            $model   = $this->getByteShardDataModel();
            $model->deleteUserSetting($tabName, $cellName, $type, $item, $_SESSION[MAIN]->getUserID());
        }
    }

    private function checkIfUserIsStillLoggedIn(): bool
    {
        $action = true;
        if ($_SESSION[MAIN] instanceof Session) {
            if ($this->service_mode === true && $_SESSION[MAIN]->isServiceAccount() !== true) {
                // Check if User is a service account, put result in Session
                $model          = $this->getByteShardDataModel();
                $serviceAccount = $model->isServiceAccount($_SESSION[MAIN]->getUserID(), $this->user_table_schema);
                if ($serviceAccount) {
                    $_SESSION[MAIN]->setServiceAccount();
                } else {
                    $action = false; // Servicemode activated, user account is no serviceaccount -> logout
                }
            }
            if (($this->sessionTimeoutInMinutes * 60) < (time() - $_SESSION[MAIN]->getTimeOfLastUserRequest())) {
                $action = false; // Session timeout -> logout
            }
            if ($_SESSION[MAIN]->getServerAddress() !== BS_WEB_ROOT_DIR) {
                $action = false; // Session from different Server || Application -> logout
            }
        } else {
            $action = false;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === $this->logoffButtonName) {
            $action = false; // Logoff button clicked -> logout
        }
        return $action;
    }

    private function checkPrerequisites()
    {
        switch ($this->authentication_target) {
            case Authentication\Enum\Target::AUTH_TARGET_DB:
                if (empty($this->user_table_schema->getFieldNameLocalPassword())) {
                    ErrorHandler\Template::printGenericExceptionTemplate('no password column defined in db schema');
                    exit;
                }
                break;
            case Authentication\Enum\Target::AUTH_TARGET_DEFINED_ON_DB:
                break;
            case Authentication\Enum\Target::AUTH_TARGET_LDAP:
                break;
            default:
                break;
        }
    }

    private function getLoginStateAndStartSession(): bool
    {
        ini_set('session.use_only_cookies', 1);
        if ($this->require_ssl === true || Server::getProtocol() === 'https') {
            //Debug::debug('Evn: secure cookie');
            ini_set('session.cookie_secure', 1);
        }
        ini_set('session.cookie_httponly', 1);
        //Debug::debug('Env: start session');
        session_cache_limiter('nocache');
        session_start();
        //Debug::debug('Env: session started');
        if (!isset($_SESSION[MAIN]) || !($_SESSION[MAIN] instanceof Session)) {
            $_SESSION[MAIN] = new Session($this->locale);
            $_SESSION[MAIN]->setLocales($this->locales);
            $_SESSION[MAIN]->setTimezones($this->client_timezone, $this->db_timezone);
            $_SESSION[MAIN]->setDBFormats($this->db_column_date_format, $this->db_column_smalldatetime_format, $this->db_column_datetime_format, $this->db_column_datetime_precision, $this->db_column_datetime2_format, $this->db_column_datetime2_precision, $this->db_column_datetimeoffset_format, $this->db_column_datetimeoffset_precision, $this->db_column_bigintdate_format, $this->db_column_time_format, $this->db_column_time_precision);
            $_SESSION[MAIN]->setClientFormats($this->client_form_control_calendar_default_db_column_type, $this->client_grid_column_calendar_default_db_column_type, $this->client_grid_column_date_default_db_column_type);
            $_SESSION[MAIN]->setMetaColumnFormats($this->db_meta_data_column_created_on_column_type, $this->db_meta_data_column_modified_on_column_type, $this->db_meta_data_column_archived_on_column_type);
        }
        return $_SESSION[MAIN]->getLoginState();
    }

    protected function getIdentityProvider(): Authentication\IdentityProviderInterface
    {
        // todo: refactor these methods into the LocalForm authentication provider
        $byteShardLoginForm = new Authentication\LocalForm($this->getByteShardDataModel());
        $byteShardLoginForm->setEnvironment($this);
        $byteShardLoginForm->setLoginFormSchema($this->loginFormSchema);
        $byteShardLoginForm->setLowerCaseUserName($this->lower_case_username);
        $byteShardLoginForm->setAccessControlTarget($this->access_control_target);
        $byteShardLoginForm->setAuthenticationTarget($this->authentication_target);
        $byteShardLoginForm->setUserTableSchema($this->user_table_schema);
        $byteShardLoginForm->setApplicationAuthentication($this->getApplicationAuthenticationObject());
        $byteShardLoginForm->setServiceMode($this->service_mode);
        if (isset($this->ldap_host)) {
            $byteShardLoginForm->setLdapHost($this->ldap_host);
        }
        return $byteShardLoginForm;
    }

    private function initializeUser()
    {
        $_SESSION[MAIN]->setTimeOfLastUserRequest();

        if (isset($GLOBALS['errorHandler']) && ($GLOBALS['errorHandler'] instanceof ErrorHandler)) {
            $GLOBALS['errorHandler']->setResultObject(ErrorHandler::RESULT_OBJECT_CELL_CONTENT); //in case of error display a cell content error and don't redirect to log in
        }
        $this->initializeFrameworkAfterAuthentication();
        $this->initializeApplicationAfterAuthentication();
        if ($_SESSION[MAIN]->permissionsAreInitialized() === false) {
            $permissions = $this->initializePermissions();
            if ($permissions instanceof Permission) {
                $_SESSION[MAIN]->setPermissionObject($permissions);
                $_SESSION[MAIN]->permissionsAreInitialized(true);
            }
        }
        if ($_SESSION[MAIN]->cellSizesAreLoaded() === false) {
            if ($this->loadStoredCellSizes() === true) {
                $_SESSION[MAIN]->cellSizesAreLoaded(true);
            }
        }
        if ($_SESSION[MAIN]->tabsAreInitialized() === false && method_exists($this, 'initializeTabs')) {
            if ($this->initializeTabs($_SESSION[MAIN]) === true) {
                $_SESSION[MAIN]->tabsAreInitialized(true);
            }
        }
    }

    public function authenticate()
    { //TODO: rename this function, think of a more appropriate name
        $this->checkPrerequisites();
        $this->initializeApplicationGlobals();
        $this->initializeFrameworkBeforeAuthentication();
        $this->initializeApplicationBeforeAuthentication();

        $userIsLoggedIn = $this->getLoginStateAndStartSession();

        // todo: move to LocalForm auth provider
        $this->setApplicationLoginFormSchema();

        $identityProvider = $this->getIdentityProvider();

        // determine new login state
        if ($userIsLoggedIn === true) {
            $userIsLoggedIn = $this->checkIfUserIsStillLoggedIn();
        } else {
            $userIsLoggedIn = $identityProvider->authenticate();
            if ($userIsLoggedIn === true) {
                $this->processSuccessfulLogin($identityProvider->getUserData());
            }
        }

        if ($userIsLoggedIn === false) {
            $this->logout = true;
            $identityProvider->logout();
            exit;
        } else {
            $this->initializeUser();
        }
    }

    public function __destruct()
    {
        if ($this->logout === true && session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    public function printLoginCallback(string $type, ...$parameters)
    {
        switch ($type) {
            case Action::CHANGE_PASSWORD:
                $this->printChangePasswordForm();
                break;
            case Action::DISPLAY_TOO_MANY_FAILED_ATTEMPS:
                $this->printLoginForm_failed(...$parameters);
                exit;
            case Action::NEW_PASSWORD_REPEAT_FAILED:
                $this->printChangePasswordForm_newPassDontMatch();
                exit;
            case Action::OLD_PASSWORD_WRONG:
                $this->printChangePasswordForm_oldPassInvalid();
                exit;
            case Action::NEW_PASSWORD_USED_IN_PAST:
                $this->printChangePasswordForm_newPassUsed();
                exit;
            case Action::NEW_PASSWORD_DOESNT_MATCH_POLICY:
                $this->printChangePasswordForm_newPassInvalid();
                exit;
            case Action::PASSWORD_EXPIRED:
                $this->printLoginForm_passwordExpired();
                exit;
            case Action::AUTHENTICATION_TARGET_UNREACHABLE:
                $this->printLoginForm_authTargetUnreachable();
                exit;
            case Action::INVALID_CREDENTIALS:
                $this->printLoginForm_failed();
                exit;
            case 'login':
                $this->printLoginForm();
                exit;
            case 'logout':
                $this->printLoginForm_loggedout();
                break;
            case 'error':
                $this->printLoginForm_error($_SESSION['FILE_ACCESS'] ?? true);
                exit;
            case 'passwordReset':
                $this->printForgotPasswordForm();
                exit;
        }
    }

    /**
     * @return boolean
     */
    protected function loadStoredCellSizes(): bool
    {
        $model  = $this->getByteShardDataModel();
        $userId = \byteShard\Session::getUserId();
        if ($userId !== null) {
            $layouts = $model->getCellSize(\byteShard\Session::getUserId());
            foreach ($layouts as $layout) {
                $layout = array_change_key_case((array)$layout);
                switch ($layout['type']) {
                    case Cell::HEIGHT:
                    case Cell::WIDTH:
                        \byteShard\Session::setSavedCellSize($layout['tab'].'\\'.$layout['cell'], $layout['type'], (int)$layout['value']);
                        break;
                    case Cell::COLLAPSED:
                        \byteShard\Session::setSavedCellCollapse($layout['tab'].'\\'.$layout['cell']);
                        break;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $userID
     * @throws \Exception
     */
    private function storeLoginMetadata($userID)
    {
        $model = $this->getByteShardDataModel();
        $model->successfulLoginCallback($userID, $this->user_table_schema);
    }

    /**
     * @param $user_id
     * @return string
     */
    private function getLastTab($user_id): string
    {
        $model = $this->getByteShardDataModel();
        return $model->getLastTab($user_id, $this->user_table_schema);
    }

    private function processSuccessfulLogin(Authentication\UserDataInterface $userData)
    {
        // change of privilege, regenerate session id to prevent session fixation attacks
        session_regenerate_id();

        // last login and login count will be stored. But only if fieldname_lastLogin and/or fieldname_loginCount are defined in user_table_schema
        $this->storeLoginMetadata($userData->getUserId());
        \byteShard\Session::setUserData($userData, $this->getLastTab($userData->getUserId()));
        $this->successfulLoginCallback($userData->getUserId(), $userData->getUsername());
        header('Location: '.BS_WEB_ROOT_DIR.'/');
    }

    protected function successfulLoginCallback(int $userId, string $username)
    {
    }

    public function getDbDriver(): string
    {
        return $this->dbDriver;
    }

    public function getDatabase(string $name = null): string
    {
        return $this->database;
    }

    public function getDbParameters(ConnectionType $type, string $name = null): Parameters
    {
        return match ($type) {
            ConnectionType::LOGIN => $this->db_parameters_login,
            ConnectionType::READ  => $this->db_parameters_read,
            ConnectionType::WRITE => $this->db_parameters_write,
            ConnectionType::ADMIN => $this->db_parameters_admin
        };
    }

    public function getLdapHost(): string
    {
        return $this->ldap_host;
    }

    /**
     * print the site base container including all relevant java scripts and css
     * the dhtmlx content will be attached to the tabbar_init div
     */
    public function printSiteBaseContainer(): void
    {
        $result = [
            '<!DOCTYPE html>',
            '<html>',
            '<head>',
            '<meta http-equiv="X-UA-Compatible" content="IE=edge">',
            '<meta charset="utf-8">',
            '<title>'.$this->application_name.'</title>',
            '<link rel="SHORTCUT ICON" href="'.$this->favicon.'">'
        ];

        array_push($result, ...$this->addDhtmlxCSSTagsToHtmlHead());
        array_push($result, ...$this->addApplicationCSSTagsToHtmlHead());
        array_push($result, ...$this->addCssTagsToHtmlHead());

        array_push($result, ...$this->addDhtmlxJSTagsToHtmlHead());
        array_push($result, ...$this->addApplicationJSTagsToHtmlHead());
        array_push($result, ...$this->addJavascriptTagsToHtmlHead());
        $result[] = '</head>';
        $result[] = '<body>';
        array_push($result, ...$this->getApplicationHeader());
        $result[] = '<div id="tabbar_init"></div>';
        array_push($result, ...$this->getApplicationFooter());
        $result[] = '</body>';
        $result[] = '</html>';
        $result   = array_filter($result);
        $pretty   = false;
        if ($pretty === true) {
            $indent = 0;
            $spaces = 3;
            foreach ($result as &$tag) {
                switch ($tag) {
                    case '<html>':
                    case '<head>':
                    case '<body>':
                        $tag = str_repeat(' ', $indent * $spaces).$tag;
                        $indent++;
                        break;
                    case '</html>':
                    case '</head>':
                    case '</body>':
                        $indent--;
                        $tag = str_repeat(' ', $indent * $spaces).$tag;
                        break;
                    default:
                        $tag = str_repeat(' ', $indent * $spaces).$tag;
                        break;
                }
            }
            $GLOBALS['output_buffer'] = ob_get_clean();
            print implode("\n", $result);
        } else {
            $GLOBALS['output_buffer'] = ob_get_clean();
            print implode('', array_map('trim', $result));
        }
    }

    /**
     * the header which appears above the dhtmlx container
     * logout and locale selection are defined here as well
     * @return array
     */
    public function getApplicationHeader(): array
    {
        if (method_exists($this, 'getSiteBaseHeader')) {
            trigger_error('getSiteBaseHeader is deprecated. Use getApplicationHeader instead and return an array');
            $result = $this->getSiteBaseHeader();
            if (!is_array($result)) {
                return [$result];
            }
            return $result;
        }
        $class = match ($this->environment) {
            Config::DEVELOPMENT => 'header_dev',
            Config::TESTING     => 'header_test',
            default             => 'header',
        };

        $site_base_header[] = '<div id="header" class="'.$class.'">';
        $site_base_header[] = '   <div id="user_actions">';
        $site_base_header[] = '      <div id="locale"></div>';
        $site_base_header[] = '      <div id="logout"><a href="?action='.$this->logoffButtonName.'">'.Locale::get('byteShard.basecontainer.button.logout').'</a></div>';
        $site_base_header[] = '   </div>';
        $site_base_header[] = '</div>';
        return $site_base_header;
    }

    /**
     * the footer which appears below the dhtmlx container
     */
    public function getApplicationFooter(): array
    {
        if (method_exists($this, 'getSiteBaseFooter')) {
            trigger_error('getSiteBaseFooter is deprecated. Use getApplicationFooter instead and return an array');
            $result = $this->getSiteBaseFooter();
            if (!is_array($result)) {
                return [$result];
            }
            return $result;
        }
        $class = match ($this->environment) {
            Config::DEVELOPMENT => 'footer_dev',
            Config::TESTING     => 'footer_test',
            default             => 'footer',
        };
        return [
            '<div id="footer" class="'.$class.'"><span id="footer_left">'.$this->leftFooterText.'</span><span id="footer_right">powered by byteShard Framework &copy; Bespin Studios</span></div>'
        ];
    }

    public function includeJavascripts(array $arrayOfFilenames, string $scriptSubDirectory = '', string $target = 'app'): string
    {
        trigger_error(__METHOD__.' is deprecated. Please use getJavascripts() which returns an array');
        return implode('', $this->getJavascripts($arrayOfFilenames, $scriptSubDirectory, $target));
    }

    public function getJavascripts(array $files, string $scriptSubDirectory = '', string $target = 'app'): array
    {
        $js = new Javascript($this->jsPath);
        return $js->includeJavascripts($files, $scriptSubDirectory, $target);
    }

    public function includeCss(array $files, string $cssSubDirectory = '', string $target = 'app'): string
    {
        trigger_error(__METHOD__.' is deprecated. Please use getCss() which returns an array');
        return implode('', $this->getCss($files, $cssSubDirectory, $target));
    }

    public function getCss(array $files, string $cssSubDirectory = '', string $target = 'app'): array
    {
        $css = new Css($this->cssPath);
        return $css->includeCss($files, $cssSubDirectory, $target);
    }

    public function getAppName(): string
    {
        return $this->application_name;
    }

    private function initializeFrameworkGlobals($logLevel = Enum\LogLevel::CRITICAL, $context = ''): void
    {
        $this->sslRedirect();

        $this->initializeLogConstants($logLevel);

        //TODO: refactor to Server class
        define('PHPSELFDIR', Server::getProtocol().'://'.Server::getHost().((substr_count($_SERVER['PHP_SELF'], '/') > 1) ? dirname($_SERVER['PHP_SELF']).'/' : '/'));

        $this->initializeByteShardConstants();

        $this->initializeDebugConstants();

        $this->initializeDirectoryConstants($context);
    }

    /**
     * checks if the last request was ssl encrypted. Redirects to https otherwise
     */
    private function sslRedirect(): void
    {
        if ($this->require_ssl === true && Server::getProtocol() !== 'https' && php_sapi_name() !== 'cli') {
            if (!($_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' || $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0')) {
                $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
            }
            header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
            header('Location: https://'.Server::getHost().$_SERVER['REQUEST_URI']);
            exit();
        }
    }

    private function initializeByteShardConstants(): void
    {
        define('ID_SEPARATOR', '_');
        define('MAIN', $this->main);
    }

    /**
     * sets all log related constants
     */
    private function initializeLogConstants($logLevel = Enum\LogLevel::CRITICAL): void
    {
        define('LOGLEVEL', $logLevel);
        define('DISCLOSE_CREDENTIALS', false);
        if ($this->logDir !== null) {
            ini_set('error_log', $this->logDir.$this->errorLog);
        } else {
            ini_set('error_log', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.$this->defaultLogDir.DIRECTORY_SEPARATOR.$this->errorLog);
        }

        // display all errors, warnings and notices. They will be rerouted by the error handler
        // nothing will be displayed in the client
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
    }

    /**
     * sets all debug related constants
     */
    private function initializeDebugConstants(): void
    {
        define('DEBUG', $this->debug);

        if ($this->debug === true) {
            define('DEBUG_LOCALE', $this->debug_locale);
            define('DEBUG_LOCALE_TOKEN', $this->debug_locale_token);
        } else {
            define('DEBUG_LOCALE', false);
            define('DEBUG_LOCALE_TOKEN', false);
        }
    }

    /**
     * sets all directory related constants
     */
    private function initializeDirectoryConstants($context): void
    {
        //BS_FILE_PUBLIC_ROOT defined in index.php
        define('BS_FILE_PUBLIC_FRAMEWORK', BS_FILE_PUBLIC_ROOT.'/bs');
        define('BS_FILE_PUBLIC_APP', BS_FILE_PUBLIC_ROOT.'/app');

        //BS_FILE_PRIVATE_ROOT defined in InitByteShard
        if (defined('BS_FILE_PRIVATE_BYTESHARD') === false) {
            //BS_FILE_PRIVATE_BYTESHARD might be defined in the ajax endpoints
            define('BS_FILE_PRIVATE_BYTESHARD', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.'byteShard');
        }
        define('BS_FILE_PRIVATE_LIB', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.$this->libPath);
        define('BS_FILE_PRIVATE_APP', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.'application');
        define('BS_FILE_PRIVATE_UPLOAD', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.$this->uploadDir);
        define('BS_FILE_PRIVATE_LOG', BS_FILE_PRIVATE_ROOT.DIRECTORY_SEPARATOR.'log');

        define('BS_WEB_ROOT_DIR', rtrim(Server::getProtocol().'://'.Server::getHost().'/'.trim($context, '/'), '/'));
        define('BS_WEB_APP_DIR', BS_WEB_ROOT_DIR.'/app');
        define('BS_WEB_FRAMEWORK_DIR', BS_WEB_ROOT_DIR.'/bs');
        define('BS_WEB_UPLOAD_DIR', BS_WEB_ROOT_DIR.'/'.$this->uploadDir);
        /* EXAMPLE -> MOVE TO DOCUMENTATION
        BS_FILE_PUBLIC_ROOT       /srv/www/htdocs/<APPNAME>
        BS_FILE_PUBLIC_FRAMEWORK  /srv/www/htdocs/<APPNAME>/bs
        BS_FILE_PUBLIC_APP        /srv/www/htdocs/<APPNAME>/app

        BS_FILE_PRIVATE_ROOT      /srv/<APPNAME>
        BS_FILE_PRIVATE_FRAMEWORK /srv/<APPNAME>/byteShard
        BS_FILE_PRIVATE_LIB       /srv/<APPNAME>/lib
        BS_FILE_PRIVATE_APP       /srv/<APPNAME>/application
        BS_FILE_PRIVATE_UPLOAD    /srv/<APPNAME>/upload
        BS_FILE_PRIVATE_LOG       /srv/<APPNAME>/log

        BS_WEB_ROOT_DIR:      https://www.<APPNAME>.com/sub/
        BS_WEB_APP_DIR:       https://www.<APPNAME>.com/sub/app/
        BS_WEB_FRAMEWORK_DIR: https://www.<APPNAME>.com/sub/bs/
        BS_WEB_UPLOAD_DIR:    https://www.<APPNAME>.com/sub/upload/
        */
    }

    private function initializeFrameworkBeforeAuthentication(): void
    {
        // Komprimierung aktivieren
        // TODO: funktioniert nicht mit dem Error Handler buffer testen ob ob_end und ob_start hilft
        ini_set('zlib.output_compression', self::$zlib_output_compression);
        ini_set('zlib.output_compression_level', self::$zlib_output_compression_level);
    }

    private function initializeFrameworkAfterAuthentication(): void
    {
    }

    public function __debugInfo(): array
    {
        if (defined('DISCLOSE_CREDENTIALS') && DISCLOSE_CREDENTIALS === true && defined('LOGLEVEL') && LOGLEVEL === LogLevel::DEBUG) {
            return get_object_vars($this);
        }
        $debugInfo              = get_object_vars($this);
        $debugInfo['ldap_pass'] = $this->ldap_pass === null ? '' : 'CONFIDENTIAL';
        return $debugInfo;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__debugInfo();
    }
}
