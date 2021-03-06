<?php
/**
* DB Cleaner Helper
*
* Grabs the list of items we need from the DB and exports it nicely.
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

/* Get config settings
$configs = array();
$result = $db->sql_query('SELECT * FROM ' . CONFIG_TABLE);
while ($row = $db->sql_fetchrow($result))
{
	$configs[] = $row;
}

output_list($configs, 'config_name');
*/

/* Get auth settings
$permissions = array();
$result = $db->sql_query('SELECT * FROM ' . ACL_OPTIONS_TABLE);
while ($row = $db->sql_fetchrow($result))
{
	unset($row['auth_option_id']);
	$permissions[] = $row;
}

output_list($permissions, 'auth_option');
*/

$modules = array();
$sql = 'SELECT * FROM ' . MODULES_TABLE . '
	ORDER BY module_id ASC';
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{
	$modules[] = $row;
}

output_list($modules, 'module_id', true);

die();

function output_list($data, $key, $list_key = false)
{
	$max_length = 0;
	foreach ($data as $row)
	{
		if (utf8_strlen($row[$key]) > $max_length)
		{
			$max_length = utf8_strlen($row[$key]);
		}
	}
	$total_tabs = ceil(($max_length + 3) / 4);

	$output = '';
	foreach ($data as $row)
	{
		if ($list_key == false)
		{
			$output .= "\t\t\t'{$row[$key]}'";

			$tabs = ($total_tabs - ceil((utf8_strlen($row[$key]) + 3) / 4));
			for($i = 0; $i <= $tabs; $i++)
			{
				$output .= "\t";
			}

			$output .= '=> array(';

			unset($row[$key]);
		}
		else
		{
			$output .= "\t\tarray(";
		}

		foreach ($row as $column => $value)
		{
			$output .= "'$column' => '$value', ";
		}

		$output = substr($output, 0, -2) . "),\n";
	}

	echo $output;
}
?>