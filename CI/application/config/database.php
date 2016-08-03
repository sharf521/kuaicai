<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'fenecll';
$active_record = TRUE;
//webservice
$db['mssql']['hostname'] = '192.168.1.11\SQL2005';
$db['mssql']['username'] = 'sa';
$db['mssql']['password'] = '123';
$db['mssql']['database'] = 'Algrithm';
$db['mssql']['dbdriver'] = 'mssql';
$db['mssql']['dbprefix'] = '';
$db['mssql']['pconnect'] = FALSE;
$db['mssql']['db_debug'] = TRUE;
$db['mssql']['cache_on'] = FALSE;
$db['mssql']['cachedir'] = '';
$db['mssql']['char_set'] = 'utf8';
$db['mssql']['dbcollat'] = 'utf8_general_ci';
$db['mssql']['swap_pre'] = '';
$db['mssql']['autoinit'] = FALSE;
$db['mssql']['stricton'] = FALSE;

//积分商城
$db['fenecll']['hostname'] = 'localhost';
$db['fenecll']['username'] = 'root';
$db['fenecll']['password'] = 'root';
$db['fenecll']['database'] = 'fenecll';
$db['fenecll']['dbdriver'] = 'mysql';
$db['fenecll']['dbprefix'] = 'ecm_';
$db['fenecll']['pconnect'] = FALSE;
$db['fenecll']['db_debug'] = TRUE;
$db['fenecll']['cache_on'] = FALSE;
$db['fenecll']['cachedir'] = '';
$db['fenecll']['char_set'] = 'utf8';
$db['fenecll']['dbcollat'] = 'utf8_general_ci';
$db['fenecll']['swap_pre'] = '';
$db['fenecll']['autoinit'] = TRUE;
$db['fenecll']['stricton'] = FALSE;

//快采商城
$db['kuaicai']['hostname'] = 'localhost';
$db['kuaicai']['username'] = 'root';
$db['kuaicai']['password'] = 'root';
$db['kuaicai']['database'] = 'kuaicai';
$db['kuaicai']['dbdriver'] = 'mysql';
$db['kuaicai']['dbprefix'] = 'ecm_';
$db['kuaicai']['pconnect'] = FALSE;
$db['kuaicai']['db_debug'] = TRUE;
$db['kuaicai']['cache_on'] = FALSE;
$db['kuaicai']['cachedir'] = '';
$db['kuaicai']['char_set'] = 'utf8';
$db['kuaicai']['dbcollat'] = 'utf8_general_ci';
$db['kuaicai']['swap_pre'] = '';
$db['kuaicai']['autoinit'] = TRUE;
$db['kuaicai']['stricton'] = FALSE;

//借贷平台
$db['p2p']['hostname'] = 'localhost';
$db['p2p']['username'] = 'root';
$db['p2p']['password'] = 'root';
$db['p2p']['database'] = 'demop2p';
$db['p2p']['dbdriver'] = 'mysql';
$db['p2p']['dbprefix'] = 'dw_';
$db['p2p']['pconnect'] = FALSE;
$db['p2p']['db_debug'] = TRUE;
$db['p2p']['cache_on'] = FALSE;
$db['p2p']['cachedir'] = '';
$db['p2p']['char_set'] = 'utf8';
$db['p2p']['dbcollat'] = 'utf8_general_ci';
$db['p2p']['swap_pre'] = '';
$db['p2p']['autoinit'] = TRUE;
$db['p2p']['stricton'] = FALSE;




/* End of file database.php */
/* Location: ./application/config/database.php */