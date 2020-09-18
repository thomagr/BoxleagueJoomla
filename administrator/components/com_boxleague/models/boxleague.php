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

jimport('joomla.application.component.modeladmin');

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;

/**
 * Boxleague model.
 *
 * @since  1.6
 */
class BoxleagueModelBoxleague extends \Joomla\CMS\MVC\Model\AdminModel
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_BOXLEAGUE';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_boxleague.boxleague';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

        
        
        
        
        
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Boxleague', $prefix = 'BoxleagueTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
     *
     * @throws
	 */
	public function getForm($data = array(), $loadData = true)
	{
            // Initialise variables.
            $app = Factory::getApplication();

            // Get the form.
            $form = $this->loadForm(
                    'com_boxleague.boxleague', 'boxleague',
                    array('control' => 'jform',
                            'load_data' => $loadData
                    )
            );

            

            if (empty($form))
            {
                return false;
            }

            return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
     *
     * @throws
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_boxleague.edit.boxleague.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
                        
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
            
            if ($item = parent::getItem($pk))
            {
                // Do any procesing on fields here if needed
            }

            return $item;
            
	}

	/**
	 * Method to duplicate an Boxleague
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = Factory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_boxleague'))
		{
			throw new Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
                    
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}
				

				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
                    
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

    public function getBoxByName($boxleague_id, $box_name)
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__boxleague_box')
            ->where($db->quoteName('boxleague_id') . ' = ' . $boxleague_id, 'AND')
            ->where($db->quoteName('bx_name') . ' = ' . $db->quote($box_name));

        // sets up a database query for later execution
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if(sizeof($result) == 1){
            return $result[0];
        }
        return null;
    }

    public function getUserByName($user_name)
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__users')
            ->where($db->quoteName('name') . ' = ' . $db->quote($user_name));
        // sets up a database query for later execution
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if(sizeof($result) == 1){
            return $result[0];
        }
        return null;
    }

    public function getPlayers($boxleague_id, $box_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__boxleague_player')
            ->where($db->quoteName('boxleague_id') . ' = ' . $boxleague_id, 'AND')
            ->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getBoxes($boxleague_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_box');
        $query->where($db->quoteName('boxleague_id') . ' = ' . $boxleague_id);
        // sets up a database query for later execution
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getImportList()
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__player_import AS player');
        $query->order('box, player ASC');
        // sets up a database query for later execution
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function createBoxleague($name)
    {
        JLog::add('createBoxleague() ', JLog::DEBUG, 'my-error-category');

        $user = Factory::getUser();

        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'bl_name','bl_start_date', 'bl_end_date', 'bl_archive', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        $values[] = "'', '$name', null, null, '0', '0', '1', '0', null, '$user->id', '$user->id'";
        $query->insert($db->quoteName('#__boxleague_boxleague'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        $db->query();

        return $db->insertid();
    }

    public function createBox($boxleague_id, $name, $order)
    {
        JLog::add('createBox() ', JLog::DEBUG, 'my-error-category');

        $user = Factory::getUser();

        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'boxleague_id','bx_name', 'bx_order', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        $values[] = "'', '$boxleague_id', '$name', '$order', '0', '1', '0', null, '$user->id', '$user->id'";
        $query->insert($db->quoteName('#__boxleague_box'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        $db->query();

        return $db->insertid();
    }

    public function createPlayer($boxleague_id, $box_id, $user_id)
    {
        JLog::add('createPlayer() ', JLog::DEBUG, 'my-error-category');

        $user = Factory::getUser();

        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'boxleague_id','box_id', 'user_id', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        $values[] = "'', '$boxleague_id', '$box_id', '$user_id', '0', '1', '0', null, '$user->id', '$user->id'";
        $query->insert($db->quoteName('#__boxleague_player'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        $db->query();

        return $db->insertid();
    }

    public function addMatcheValues($boxleague_id, $box_id, $player_id, $players, $values)
    {
        $user = Factory::getUser();

        foreach ($players as $player) {
            $values[] = "'', '$boxleague_id', '$box_id', '$player_id', null, '$player->id', null, '0', '1', '0', null, '$user->id', '$user->id'";
        }
        return $values;
    }

    public function createMatches($boxleague_id, $box_id, $players)
    {
        JLog::add('createMatches() ' . $box_id, JLog::DEBUG, 'my-error-category');

        // Get a database object
        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'boxleague_id', 'box_id', 'home_player', 'home_score', 'away_player', 'away_score', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        while (!empty($players)) {
            $player = array_pop($players);
            $values = $this->addMatcheValues($boxleague_id, $box_id, $player->id, $players, $values);
        }
        $query->insert($db->quoteName('#__boxleague_match'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);

        //throw new Exception(Text::_($query->__toString()));

        $db->query();
    }

    public function checkPlayerNames()
    {
        JLog::add('checkPlayerNames() ', JLog::DEBUG, 'my-error-category');

        // Get a database object
        // Check all players are in the users table
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__player_import AS player');
        $query->join('LEFT OUTER', '#__users AS user ON user.name = player.player');
        $query->where($db->quoteName('user.id') . ' IS NULL');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        $values = array();
        foreach ($result as $row) {
            $values[] = $row->player;
        }

        return $values;
    }

    public function checkBoxes()
    {
        JLog::add('checkBoxes() ', JLog::DEBUG, 'my-error-category');

        // Get a database object
        // Check all players are in the users table
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT box');
        $query->from('#__player_import AS player');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        $values = array();
        foreach ($result as $row){
            $values[] = $row->box;
        }

        return $values;
    }

    public function checkPlayers()
    {
        JLog::add('checkPlayers() ', JLog::DEBUG, 'my-error-category');

        // Get a database object
        // Check all players are in the users table
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT player');
        $query->from('#__player_import AS player');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        $values = array();
        foreach ($result as $row){
            $values[] = $row->box;
        }

        return $values;
    }

    public function import()
    {
        $user = Factory::getUser();

        // Access checks.
        if (!$user->authorise('core.create', 'com_boxleague'))
        {
            throw new Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $missingPlayers = $this->checkPlayerNames();
        if(count($missingPlayers)){
            $exceptions = implode(", ", $missingPlayers);
            throw new Exception(Text::_('Players not found in user table ' . $exceptions));
        }

        $boxleague_id = $this->createBoxleague('new boxleague');

        $boxes = $this->checkBoxes();
        if(!count($boxes)){
            throw new Exception(Text::_('No boxes found in import table'));
        }

        $players = $this->checkPlayers();
        if(!count($boxes)){
            throw new Exception(Text::_('No players found in import table'));
        }

        // create boxes
        foreach($boxes as $box){
            $this->createBox($boxleague_id, 'Box ' . $box, $box);
        }

        // create players
        $players = $this->getImportList();
        foreach($players as $player){
            $box = $this->getBoxByName($boxleague_id, 'Box ' . $player->box);
            $playerUser = $this->getUserByName($player->player);
            $this->createPlayer($boxleague_id, $box->id, $playerUser->id);
        }

        // create matches
        $boxes = $this->getBoxes($boxleague_id);
        foreach($boxes as $box){
            $players = $this->getPlayers($boxleague_id, $box->id);
            $this->createMatches($boxleague_id, $box->id, $players);
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__boxleague_boxleague');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
