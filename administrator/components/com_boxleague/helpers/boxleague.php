<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Boxleague
 * @author     Graham Thomas <graham.r.thomas@me.com>
 * @copyright  2020 Graham Thomas
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;

/**
 * Boxleague helper.
 *
 * @since  1.6
 */
class BoxleagueHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_BOXLEAGUE_TITLE_BOXLEAGUES'),
			'index.php?option=com_boxleague&view=boxleagues',
			$vName == 'boxleagues'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_BOXLEAGUE_TITLE_BOXES'),
			'index.php?option=com_boxleague&view=boxes',
			$vName == 'boxes'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_BOXLEAGUE_TITLE_PLAYERS'),
			'index.php?option=com_boxleague&view=players',
			$vName == 'players'
		);

JHtmlSidebar::addEntry(
			JText::_('COM_BOXLEAGUE_TITLE_MATCHES'),
			'index.php?option=com_boxleague&view=matches',
			$vName == 'matches'
		);

	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = Factory::getUser();
		$result = new JObject;

		$assetName = 'com_boxleague';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

