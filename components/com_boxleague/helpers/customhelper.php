<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Boxleague
 * @author     Graham Thomas <graham.r.thomas@me.com>
 * @copyright  2020 Graham Thomas
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JLoader::register('BoxleagueCustomHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_boxleague' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'customhelper.php');

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Class BoxleagueCustomHelper
 *
 * @since  1.6
 */
class BoxleagueCustomHelper
{
    public static function countMatches($box_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__boxleague_match');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as a single value
        return $db->loadResult();
    }

    public static function countPlayers($box_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__boxleague_player');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as a single value
        return $db->loadResult();
    }

    public static function getPlayersFromBox($box_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_player');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        return $db->loadObjectList();
    }

    public static function addMatches($box_id, $player_id, $players, $values)
    {
        $user = Factory::getUser();

        foreach ($players as $player) {
            $values[] = "'', '$box_id', '$player_id', '0', '$player->id', '0', '0', '1', '0', '0000-00-00 00:00:00', '$user->id', '$user->id'";
        }
        return $values;
    }

    public static function buildMatches($box_id)
    {
        $matches = BoxleagueCustomHelper::countMatches($box_id);
        if ($matches > 0) {
            return;
        }
        $players = BoxleagueCustomHelper::getPlayersFromBox($box_id);
        // Get a database object
        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'box_id', 'home_player', 'home_score', 'away_player', 'away_score', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        while (!empty($players)) {
            $player = array_pop($players);
            $values = BoxleagueCustomHelper::addMatches($box_id, $player->id, $players, $values);
        }
        echo '<pre>'; print_r($columns); echo '</pre>';
        echo '<pre>'; print_r($values); echo '</pre>';
        $query->insert($db->quoteName('#__boxleague_match'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        $db->query();
    }

    public static function returnPlayerBoxScore($box_id, $player_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__boxleague_match');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        $score = 0;
        foreach ($result as $row) {
            if ($row->home_player == $player_id) {
                $score = $score + $row->home_score;
            }
            if ($row->away_player == $player_id) {
                $score = $score + $row->away_score;
            }
        }
        return $score;
    }

    public static function returnDbObject($id, $db_name)
    {
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_' . $db_name);
        $query->where($db->quoteName('id') . ' = ' . $id);
        // sets up a database query for later execution
        $db->setQuery($query);
        $result = $db->loadObjectList();
        if(sizeof($result) == 1){
            return $result[0];
        }
        return null;
    }

    public static function returnBoxleague($id)
    {
        return BoxleagueCustomHelper::returnDbObject($id, "boxleague");
    }

    public static function returnBox($id)
    {
        return BoxleagueCustomHelper::returnDbObject($id, "box");
    }

    public static function returnPlayer($id)
    {
        return BoxleagueCustomHelper::returnDbObject($id, "player");
    }

    public static function returnMatch($id)
    {
        return BoxleagueCustomHelper::returnDbObject($id, "match");
    }

    public static function returnMatchId($box_id, $player_row, $player_column)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_match');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        $score = 0;
        foreach ($result as $row) {
            if ($row->home_player == $player_row && $row->away_player == $player_column) {
                return $row->id;
            }
            if ($row->away_player == $player_row && $row->home_player == $player_column) {
                return $row->id;
            }
        }
        return null;
    }

    public static function returnMatchScore($box_id, $player_row, $player_column)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_match');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        // return array of results
        $ret = array();
        foreach ($result as $row) {
            if ($row->home_player == $player_row && $row->away_player == $player_column) {
                $ret['score'] = $row->home_score;
                $ret['id'] = $row->id;
                return $ret;
            }
            elseif ($row->away_player == $player_row && $row->home_player == $player_column) {
                $ret['score'] = $row->away_score;
                $ret['id'] = $row->id;
                return $ret;
            }
        }
        return $ret;
    }

    public static function printScoreBoard($box_id, $bx_name)
    {
        $user = JFactory::getUser();

        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('player.*, user.name');
        $query->from('#__boxleague_player AS player');
        $query->join('LEFT', '#__users AS user ON user.id = user_id');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        // create table
        echo "<div>"; // style=overflow-y:scroll;>";
        echo "<table style='width:100%' class='table table-bordered table-condensed'>";

        // create the header row
        echo "<tr>";
        echo "<th style='color:white;background:#8445df;width:10px'>$nbsp</th>";
        echo "<th style='color:white;background:#8445df;width:150px'>$bx_name</th>";
        $count = 1;
        foreach ($result as $row) {
            echo "<th style='color:white;background:#8445df;text-align:center;width:30px'>$count</th>";
            $count++;
        }
        echo "<th style='color:white;background:#8445df;width:30px'>$nbsp</th>";
        echo "<tr>";

        // create the table rows
        $count = 1;
        foreach ($result as $row) {
            $runningTotal = 0;
            echo "<tr>";
            echo "<th style='color:white;background:#8445df;'>$count</th>";
            $count++;

            // add strong
            if($user->id == $row->user_id) {
                echo "<td style='text-align:left;'><strong>$row->name</strong></td>";
            } else {
                echo "<td style='text-align:left;'>$row->name</td>";
            }

            // create the row cells
            foreach ($result as $column){
                if($row->name == $column->name){
                    echo "<td style='background:#4db748'>&nbsp</td>";
                }
                else {
                    $matchScore = BoxleagueCustomHelper::returnMatchScore($box_id, $row->id, $column->id);
                    $runningTotal += $matchScore['score'];

                    $addlink = $user->id == $row->user_id;

                    if($addlink) {
                        echo "<td style='background:#ffffee;font-weight: bold;'>";
                        echo "<a style='display:block;' href='" . "/index.php/matches/match/edit/";
                        echo $matchScore['id'];
                        echo "'>";
                    } else {
                        echo "<td>";
                    }

                    if($matchScore['score'] == 0){
                        echo "&nbsp;";
                    } else {
                        echo $matchScore['score'];
                    }

                    if($addlink) {
                        echo "</a></td>";
                    } else {
                        echo "</td>";
                    }
                }
                // remove strong
                if($user->id == $row->user_id)
                    echo "</strong>";
            }
            $playerScore = $runningTotal;
            if($playerScore == 0){
                echo "<td></td>";
            } else {
                echo "<td><strong>$playerScore</strong></td>";
            }
            echo "<tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    public static function printPlayers($box_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('player.*, user.name');
        $query->from('#__boxleague_player AS player');
        $query->join('LEFT', '#__users AS user ON user.id = user_id');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        $count = 6;
        echo "<table style='width:100%'>";
        foreach ($result as $row) {
            $count--;
            echo "<tr>";
            echo "<td>$row->name</td>";
            $score = BoxleagueCustomHelper::returnPlayerBoxScore($box_id, $row->id);
            echo "<td style='text-align:right'>$score</td>";
            echo "<tr>";
        }
        for ($i = $count; $i > 0; $i--) {
            echo "<tr><td>&nbsp;</td></tr>";
        }
        echo "</table>";
        BoxleagueCustomHelper::buildMatches($box_id);
    }

    public static function printBox($id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_box');
        $query->where($db->quoteName('id') . ' = ' . $id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            BoxleagueCustomHelper::printScoreBoard($row->id, $row->bx_name);
        }
    }

    public static function printBoxes($boxleague_id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_box');
        $query->where($db->quoteName('boxleague_id') . ' = ' . $boxleague_id);
        $query->order('ordering ASC');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            BoxleagueCustomHelper::printBox($row->id);
        }
    }

    public static function printBoxleague($id)
    {
        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_boxleague');
        $query->where($db->quoteName('id') . ' = ' . $id);
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            echo "<h3> $row->bl_name </h3>";
            echo HtmlHelper::date($row->bl_start_date, Text::_('DATE_FORMAT_LC3'));
            echo " - ";
            echo HtmlHelper::date($row->bl_end_date, Text::_('DATE_FORMAT_LC3'));

            BoxleagueCustomHelper::printBoxes($row->id);
        }
    }
}