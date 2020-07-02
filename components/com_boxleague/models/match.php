<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Boxleague
 * @author     Graham Thomas <graham.r.thomas@me.com>
 * @copyright  2020 Graham Thomas
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use \Joomla\CMS\Factory;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table;

/**
 * Boxleague model.
 *
 * @since  1.6
 */
class BoxleagueModelMatch extends \Joomla\CMS\MVC\Model\ItemModel
{
    public $_item;

        
    
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
     * @throws Exception
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication('com_boxleague');
		$user = Factory::getUser();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_boxleague')) && (!$user->authorise('core.edit', 'com_boxleague')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->getUserState('com_boxleague.edit.match.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_boxleague.edit.match.id', $id);
		}

		$this->setState('match.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('match.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer $id The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
     *
     * @throws Exception
	 */
	public function getItem($id = null)
	{
            if ($this->_item === null)
            {
                $this->_item = false;

                if (empty($id))
                {
                    $id = $this->getState('match.id');
                }

                // Get a level row instance.
                $table = $this->getTable();

                // Attempt to load the row.
                if ($table->load($id))
                {
                    

                    // Check published state.
                    if ($published = $this->getState('filter.published'))
                    {
                        if (isset($table->state) && $table->state != $published)
                        {
                            throw new Exception(Text::_('COM_BOXLEAGUE_ITEM_NOT_LOADED'), 403);
                        }
                    }

                    // Convert the JTable to a clean JObject.
                    $properties  = $table->getProperties(1);
                    $this->_item = ArrayHelper::toObject($properties, 'JObject');

                    
                }

                if (empty($this->_item))
				{
					throw new Exception(Text::_('COM_BOXLEAGUE_ITEM_NOT_LOADED'), 404);
				}
            }
        
            

		if (isset($this->_item->created_by))
		{
			$this->_item->created_by_name = JFactory::getUser($this->_item->created_by)->name;
		}

		if (isset($this->_item->modified_by))
		{
			$this->_item->modified_by_name = JFactory::getUser($this->_item->modified_by)->name;
		}

		if (isset($this->_item->box_id) && $this->_item->box_id != '')
		{
			if (is_object($this->_item->box_id))
			{
				$this->_item->box_id = ArrayHelper::fromObject($this->_item->box_id);
			}

			$values = (is_array($this->_item->box_id)) ? $this->_item->box_id : explode(',',$this->_item->box_id);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('CONCAT(`#__boxleague_box_3455549`.`boxleague_id`, \' - \', `#__boxleague_box_3455549`.`bx_name`) AS `fk_value`')
					->from($db->quoteName('#__boxleague_box', '#__boxleague_box_3455549'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->fk_value;
				}
			}

			$this->_item->box_id = !empty($textValue) ? implode(', ', $textValue) : $this->_item->box_id;

		}

		if (isset($this->_item->home_player) && $this->_item->home_player != '')
		{
			if (is_object($this->_item->home_player))
			{
				$this->_item->home_player = ArrayHelper::fromObject($this->_item->home_player);
			}

			$values = (is_array($this->_item->home_player)) ? $this->_item->home_player : explode(',',$this->_item->home_player);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('CONCAT(`#__boxleague_player_3455550`.`id`, \' - \', `#__boxleague_player_3455550`.`box_id`, \' - \', `#__boxleague_player_3455550`.`user_id`) AS `fk_value`')
					->from($db->quoteName('#__boxleague_player', '#__boxleague_player_3455550'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->fk_value;
				}
			}

			$this->_item->home_player = !empty($textValue) ? implode(', ', $textValue) : $this->_item->home_player;

		}

		if (isset($this->_item->away_player) && $this->_item->away_player != '')
		{
			if (is_object($this->_item->away_player))
			{
				$this->_item->away_player = ArrayHelper::fromObject($this->_item->away_player);
			}

			$values = (is_array($this->_item->away_player)) ? $this->_item->away_player : explode(',',$this->_item->away_player);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('CONCAT(`#__boxleague_player_3455551`.`id`, \' - \', `#__boxleague_player_3455551`.`box_id`, \' - \', `#__boxleague_player_3455551`.`user_id`) AS `fk_value`')
					->from($db->quoteName('#__boxleague_player', '#__boxleague_player_3455551'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->fk_value;
				}
			}

			$this->_item->away_player = !empty($textValue) ? implode(', ', $textValue) : $this->_item->away_player;

		}

            return $this->_item;
        }

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string $type   Name of the JTable class to get an instance of.
	 * @param   string $prefix Prefix for the table class name. Optional.
	 * @param   array  $config Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Match', $prefix = 'BoxleagueTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_boxleague/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string $alias Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
            $table      = $this->getTable();
            $properties = $table->getProperties();
            $result     = null;
            $aliasKey   = null;
            if (method_exists($this, 'getAliasFieldNameByView'))
            {
            	$aliasKey   = $this->getAliasFieldNameByView('match');
            }
            

            if (key_exists('alias', $properties))
            {
                $table->load(array('alias' => $alias));
                $result = $table->id;
            }
            elseif (isset($aliasKey) && key_exists($aliasKey, $properties))
            {
                $table->load(array($aliasKey => $alias));
                $result = $table->id;
            }
            
                return $result;
            
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('match.id');
                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer $id The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('match.id');

                
		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = Factory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
                
	}

	/**
	 * Publish the element
	 *
	 * @param   int $id    Item id
	 * @param   int $state Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();
                
		$table->load($id);
		$table->state = $state;

		return $table->store();
                
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int $id Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();

                
                    return $table->delete($id);
                
	}

	
}
