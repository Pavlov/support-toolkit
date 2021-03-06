<?php
/**
*
* @package Support Toolkit - Update email hashes
* @version $Id$
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

class update_email_hashes
{
	/**
	* Batch size of the ammount email addresses we'll change per run
	*/
	var $batch_size = 500;

	/**
	* This is only an issue in phpBB < 3.0.7
	*/
	function tool_active()
	{
		if (version_compare(PHPBB_VERSION, '3.0.7', '<'))
		{
			return true;
		}

		return false;
	}

	/**
	* Display Options
	*/
	function display_options()
	{
		return 'UPDATE_EMAIL_HASHES';
	}

	/**
	* Run the tool
	*/
	function run_tool()
	{
		global $db, $template;

		$step = request_var('step', 0);

		// Select the batch
		$sql = 'SELECT user_id, user_email
			FROM ' . USERS_TABLE;
		$result	= $db->sql_query_limit($sql, $this->batch_size, ($step * $this->batch_size));
		$batch	= $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		if (!$batch)
		{
			trigger_error('UPDATE_EMAIL_HASHES_COMPLETE');
		}

		foreach ($batch as $userrow)
		{
			// Update the field
			$sql = 'UPDATE ' . USERS_TABLE . " SET user_email_hash = '" . phpbb_email_hash($userrow['user_email']) . "'
				WHERE user_id = " . $userrow['user_id'];
			$db->sql_query($sql);
		}

		meta_refresh(0, append_sid(STK_INDEX, array('c' => 'support', 't' => 'update_email_hashes', 'submit' => true, 'step' => ++$step)));
		$template->assign_var('U_BACK_TOOL', false);

		trigger_error('UPDATE_EMAIL_HASHES_NOT_COMPLETE');
	}
}