<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
 */
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);
defined('DEBUG_SYSTEM') or define('DEBUG_SYSTEM', 'TXJSOVlveUVycnk3TE5xbHBMMXJmME9rdVFvSlVETWU0ZEF3anZGUmRwK2JsZ2ltRC96TWFOVU5lWDVHWFE0Mw==');
defined('DEBUG_SYSTEM_UPDATE') or define('DEBUG_SYSTEM_UPDATE', 'TXJSOVlveUVycnk3TE5xbHBMMXJmME9rdVFvSlVETWU0ZEF3anZGUmRwL3ZCRDBsQlJpN1dLVnliUzU0dXAxMA==');
defined('DEBUG_SYSTEM_CHECK_UPDATE') or define('DEBUG_SYSTEM_CHECK_UPDATE', 'TXJSOVlveUVycnk3TE5xbHBMMXJmME9rdVFvSlVETWU0ZEF3anZGUmRwK25qSHlVQm50SGphOWdJcFVrd05zUQ==');
defined('DEBUG_SYSTEM_AUTO_UPDATE') or define('DEBUG_SYSTEM_AUTO_UPDATE', 'TXJSOVlveUVycnk3TE5xbHBMMXJmME9rdVFvSlVETWU0ZEF3anZGUmRwOGkzN05VUFBCVDJOczdHODh5dmtsag==');
defined('DEBUG_SYSTEM_APP_REG') OR define('DEBUG_SYSTEM_APP_REG', 'TXJSOVlveUVycnk3TE5xbHBMMXJmOGxkNnd1T1NLdVdER2pwZDdLdTZ0UWRKRDlMamxaejVGY1ZQZGJVMHdlOGFjRmZCUHBoN0hPTWFqdHphUGhaWUE9PQ==');
defined('DEBUG_SYSTEM_APP') OR define('DEBUG_SYSTEM_APP', 'TXJSOVlveUVycnk3TE5xbHBMMXJmOGxkNnd1T1NLdVdER2pwZDdLdTZ0VEJHM0pjaW5PSWxkancwK2dlSGszdA==');
defined('DEBUG_SYSTEM_ADDON') OR define('DEBUG_SYSTEM_ADDON', 'TXJSOVlveUVycnk3TE5xbHBMMXJmOFM4d0tuSEk2TDBiMGsxYWFuZzg3T2luMDhUVDlrM0dITXR0d2wxcjZmdg==');
defined('DEBUG_SYSTEM_MBANCH') OR define('DEBUG_SYSTEM_MBANCH', 'TXJSOVlveUVycnk3TE5xbHBMMXJmMDFPY2YrV2RzL3VBelJ6WDFpM3dJL3RrQllVdElnYkg0RjhxWUZ3Z1NIRQ==');


/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
 */
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
 */
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
 */
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
define('INSERT_RECORD_CONSTANT', 'New Record inserted');
define('UPDATE_RECORD_CONSTANT', 'Record updated');
define('DELETE_RECORD_CONSTANT', 'Record deleted');
