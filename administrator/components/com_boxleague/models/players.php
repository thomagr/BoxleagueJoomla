<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Boxleague
 * @author     Graham Thomas <graham.r.thomas@me.com>
 * @copyright  2020 Graham Thomas
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Boxleague records.
 *
 * @since  1.6
 */
class BoxleagueModelPlayers extends \Joomla\CMS\MVC\Model\ListModel
{
    /**
     * Constructor.
     *
     * @param array $config An optional associative array of configuration settings.
     *
     * @see        JController
     * @since      1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.`id`',
                'ordering', 'a.`ordering`',
                'state', 'a.`state`',
                'created_by', 'a.`created_by`',
                'modified_by', 'a.`modified_by`',
                'boxleague_id', 'a.`boxleague_id`',
                'box_id', 'a.`box_id`',
                'user_id', 'a.`user_id`',
            );
        }
        parent::__construct($config);
    }

    /**
     * Get an array of data items
     *
     * @return mixed Array of data items on success, false on failure.
     */
    public function getItems()
    {
        $items = parent::getItems();
        foreach ($items as $oneItem) {
            if (isset($oneItem->boxleague_id)) {
                $values = explode(',', $oneItem->boxleague_id);
                $textValue = array();
                foreach ($values as $value) {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query
                        ->select('`#__boxleague_boxleague_3455531`.`bl_name` AS `fk_value`')
                        ->from($db->quoteName('#__boxleague_boxleague', '#__boxleague_boxleague_3455531'))
                        ->where($db->quoteName('#__boxleague_boxleague_3455531.id') . ' = ' . $db->quote($db->escape($value)));
                    $db->setQuery($query);
                    $results = $db->loadObject();
                    if ($results) {
                        $textValue[] = $results->fk_value;
                    }
                    $oneItem->boxleague_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->boxleague_id;
                }
            }
            if (isset($oneItem->box_id)) {
                $values = explode(',', $oneItem->box_id);
                $textValue = array();
                foreach ($values as $value) {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query
                        ->select('`#__boxleague_box_3455540`.`bx_name` AS `fk_value`')
                        ->from($db->quoteName('#__boxleague_box', '#__boxleague_box_3455540'))
                        ->where($db->quoteName('#__boxleague_box_3455540.id') . ' = ' . $db->quote($db->escape($value)));
                    $db->setQuery($query);
                    $results = $db->loadObject();
                    if ($results) {
                        $textValue[] = $results->fk_value;
                    }
                }
                $oneItem->box_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->box_id;
            }
            if (isset($oneItem->user_id)) {
                $values = explode(',', $oneItem->user_id);
                $textValue = array();
                foreach ($values as $value) {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query
                        ->select('`#__users_3455541`.`name`')
                        ->from($db->quoteName('#__users', '#__users_3455541'))
                        ->where($db->quoteName('#__users_3455541.id') . ' = ' . $db->quote($db->escape($value)));
                    $db->setQuery($query);
                    $results = $db->loadObject();
                    if ($results) {
                        $textValue[] = $results->name;
                    }
                }
                $oneItem->user_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->user_id;
            }
        }
        return $items;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering Elements order
     * @param string $direction Order direction
     *
     * @return void
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState("a.id", "ASC");
        $context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $context);
        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);
        if ($parts) {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id A prefix for the store id.
     *
     * @return   string A store id.
     *
     * @since    1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return   JDatabaseQuery
     *
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select', 'DISTINCT a.*'
            )
        );
        $query->from('`#__boxleague_player` AS a');
        // Join over the users for the checked out user
        $query->select("uc.name AS uEditor");
        $query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
        // Join over the user field 'created_by'
        $query->select('`created_by`.name AS `created_by`');
        $query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');
        // Join over the user field 'modified_by'
        $query->select('`modified_by`.name AS `modified_by`');
        $query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
        // Join over the foreign key 'boxleague_id'
        $query->select('`#__boxleague_boxleague_3455531`.`bl_name` AS boxleagues_fk_value_3455531');
        $query->join('LEFT', '#__boxleague_boxleague AS #__boxleague_boxleague_3455531 ON #__boxleague_boxleague_3455531.`id` = a.`boxleague_id`');
        // Join over the foreign key 'box_id'
        $query->select('CONCAT(`#__boxleague_box_3455540`.`boxleague_id`, \' \', `#__boxleague_box_3455540`.`bx_name`) AS boxes_fk_value_3455540');
        $query->join('LEFT', '#__boxleague_box AS #__boxleague_box_3455540 ON #__boxleague_box_3455540.`id` = a.`box_id`');
        // Join over the foreign key 'user_id'
        $query->select('`#__users_3455541`.`name` AS users_fk_value_3455541');
        $query->join('LEFT', '#__users AS #__users_3455541 ON #__users_3455541.`id` = a.`user_id`');
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int)$published);
        } elseif (empty($published)) {
            $query->where('(a.state IN (0, 1))');
        }
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
            }
        }
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', "a.id");
        $orderDirn = $this->state->get('list.direction', "ASC");
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }
        return $query;
    }
}
