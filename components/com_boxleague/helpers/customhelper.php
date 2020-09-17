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
        JLog::add('countMatches() ' . $box_id, JLog::DEBUG, 'my-error-category');

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
        JLog::add('countPlayers() ' . $box_id, JLog::DEBUG, 'my-error-category');

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
        JLog::add('getPlayersFromBox() ' . $box_id, JLog::DEBUG, 'my-error-category');

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
        JLog::add('addMatches() ' . $box_id . " " . $player_id, JLog::DEBUG, 'my-error-category');

        $user = Factory::getUser();

        foreach ($players as $player) {
            $values[] = "'', '$box_id', '$player_id', null, '$player->id', null, '0', '1', '0', null, '$user->id', '$user->id'";
        }
        return $values;
    }

    public static function buildMatches($boxleague_id, $box_id)
    {
        JLog::add('buildMatches() ' . $box_id, JLog::DEBUG, 'my-error-category');

        $matches = BoxleagueCustomHelper::countMatches($box_id);
        if ($matches > 0) {
            return;
        }

        JLog::add('building matches ' . $box_id, JLog::DEBUG, 'my-error-category');

        $players = BoxleagueCustomHelper::getPlayersFromBox($box_id);
        // Get a database object
        $db = JFactory::getDbo();
        // Create a new query object
        $query = $db->getQuery(true);
        // Insert columns.
        $columns = array('id', 'boxleague_id','box_id', 'home_player', 'home_score', 'away_player', 'away_score', 'ordering', 'state', 'checked_out', 'checked_out_time', 'created_by', 'modified_by');
        $values = array();
        while (!empty($players)) {
            $player = array_pop($players);
            $values = BoxleagueCustomHelper::addMatches($boxleague_id, $box_id, $player->id, $players, $values);
        }
        $query->insert($db->quoteName('#__boxleague_match'));
        $query->columns($columns);
        $query->values($values);
        $db->setQuery($query);
        $db->query();
    }

    public static function scoreAdjust($score)
    {
        if($score == -1){
            return 10; // walkover
        } elseif($score == -2){
            return $score = 0; // injured
        } else {
            return $score;
        }
    }

    public static function scoreAdjustString($score)
    {
        if($score == -1){
            return 'W'; // walkover
        } elseif($score == -2){
            return $score = 'Inj'; // injured
        } else {
            return $score;
        }
    }

    public static function calculatePlayerBoxScore($box_id, $player_id)
    {
        JLog::add('calculatePlayerBoxScore() ' . $box_id . " " . $player_id, JLog::DEBUG, 'my-error-category');

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
        $runningScore = 0;
        foreach ($result as $row) {
            if ($row->home_player == $player_id) {
                $runningScore += BoxleagueCustomHelper::scoreAdjust($row->home_score);
            } elseif ($row->away_player == $player_id) {
                $runningScore += BoxleagueCustomHelper::scoreAdjust($row->away_score);
            }
        }
        return $runningScore;
    }

    public static function getTableContentById($id, $db_name)
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

    public static function getBoxleagueById($id)
    {
        return BoxleagueCustomHelper::getTableContentById($id, "boxleague");
    }

    public static function getBoxById($id)
    {
        return BoxleagueCustomHelper::getTableContentById($id, "box");
    }

    public static function getPlayerById($id)
    {
        return BoxleagueCustomHelper::getTableContentById($id, "player");
    }

    public static function getMatchById($id)
    {
        return BoxleagueCustomHelper::getTableContentById($id, "match");
    }

    public static function getCurrentBoxleagueId()
    {
        // return the first unarchived boxleague
        // Get a database object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__boxleague_boxleague')
            ->where('bl_archive = 0')
            ->order('id DESC')
            ->setLimit('1');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $id = $db->loadResult();

        JLog::add('getCurrentBoxleagueId() ' . $id, JLog::DEBUG, 'my-error-category');

        return $id;
    }

    public static function getMatchIdByPlayers($box_id, $player_row, $player_column)
    {
        JLog::add('getMatchIdByPlayers() ' . $box_id . " " . $player_row . " " . $player_column, JLog::DEBUG, 'my-error-category');

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

    public static function getBoxesInBoxleague($boxleague_id)
    {
        JLog::add('getBoxesInBoxleague() ' . $boxleague_id, JLog::DEBUG, 'my-error-category');

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

        return $result;
    }

    public static function getPlayersInBox($box_id)
    {
        JLog::add('getPlayersInBox() ' . $box_id, JLog::DEBUG, 'my-error-category');

        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('player.*, user.name, user.email, profile.profile_value as phone');
        $query->from('#__boxleague_player AS player');
        $query->join('LEFT', '#__users AS user ON user.id = user_id');
        $query->join('LEFT', '#__user_profiles AS profile ON user.id = profile.user_id');
        $query->where($db->quoteName('box_id') . ' = ' . $box_id,
            $db->quoteName('profile.key') . ' = ' . $db->quoteName('profile.phone'));
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();

        return $result;
    }

    public static function getMatchesInBox($box_id)
    {
        JLog::add('getMatchesInBox() ' . $box_id, JLog::DEBUG, 'my-error-category');

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

        return $result;
    }

    public static function getMatchScore($matches, $player1, $player2)
    {
        JLog::add('getMatchScore() ' . $player1->name . " " . $player2->name, JLog::DEBUG, 'my-error-category');

        // return array of results [score, match_id]
        $ret = array();
        foreach ($matches as $match) {
            if ($match->home_player == $player1->id && $match->away_player == $player2->id) {
                $ret['score'] = BoxleagueCustomHelper::scoreAdjustString($match->home_score);
                $ret['id'] = $match->id;
                JLog::add('score home ' . $ret['score'], JLog::DEBUG, 'my-error-category');
                return $ret;
            }
            elseif ($match->away_player == $player1->id && $match->home_player == $player2->id) {
                $ret['score'] = BoxleagueCustomHelper::scoreAdjustString($match->away_score);
                $ret['id'] = $match->id;
                JLog::add('score away ' . $ret['score'], JLog::DEBUG, 'my-error-category');
                return $ret;
            }
        }

        JLog::add('score none', JLog::DEBUG, 'my-error-category');
        return $ret;
    }

    public static function printMatchCell($player){
        JLog::add('printMatchCell() ' . $player->user_id, JLog::DEBUG, 'my-error-category');

        $user = JFactory::getUser();

        // highlight user with <strong>
        if($user->id == $player->user_id){
            echo "<td style='text-align:left;'><strong>" . $player->name . "</strong></td>";
        } else {
            echo "<td style='text-align:left'>" . $player->name . "</td>";
        }
    }

    public static function printMatch($matches, $players, $player1, $player2, $archive){
        JLog::add('printMatch() ' . $player1->name . " " . $player2->name, JLog::DEBUG, 'my-error-category');

        echo "<tr>";
        foreach ($matches as $match){
            if($match->home_player == $player1->id && $match->away_player == $player2->id){
                BoxleagueCustomHelper::printMatchCell($player1);
                BoxleagueCustomHelper::printMatchScore($matches, $player1, $player2, $archive);
                echo "<td>vs</td>";
                BoxleagueCustomHelper::printMatchCell($player2);
                BoxleagueCustomHelper::printMatchScore($matches, $player2, $player1, $archive);
            }

            if($match->home_player == $player2->id && $match->away_player == $player1->id) {
                BoxleagueCustomHelper::printMatchCell($player1);
                BoxleagueCustomHelper::printMatchScore($matches, $player1, $player2, $archive);
                echo "<td>vs</td>";
                BoxleagueCustomHelper::printMatchCell($player2);
                BoxleagueCustomHelper::printMatchScore($matches, $player2, $player1, $archive);
            }
        }
        echo "</tr>";
    }

    public static function printMatches()
    {
        JLog::add('printMatches() ' . " " . $bx_name, JLog::DEBUG, 'my-error-category');

        $user = JFactory::getUser();

        // get the current boxleague
        $boxleagueId = BoxleagueCustomHelper::getCurrentBoxleagueId();
        $boxleague = BoxleagueCustomHelper::getBoxleagueById($boxleagueId);

        // get all of the boxes in the boxleague
        $boxes = BoxleagueCustomHelper::getBoxesInBoxleague($boxleagueId);

        // for each box in the boxleague
        // when box is found and handled return without carrying on
        foreach ($boxes as $box) {
            // get the players in the box
            $players = BoxleagueCustomHelper::getPlayersInBox($box->id);

            foreach ($players as $player) {
                // see if player is the user id
                if($player->user_id == $user->id){
                    // get the matches in the box
                    $matches = BoxleagueCustomHelper::getMatchesInBox($box->id);

                    // print out the contact details
                    echo "<table class='table table-condensed table-responsive'>";
                    echo "<tr'><td style='border: 0;' colspan='2'><h5>Player Contact Details</h5>";
                    echo "<p>Your details can be changed via <a href='/index.php/my-account'>My Account</a>.</p>";
                    echo "</td></tr>";
                    foreach ($players as $contact) {
                        echo "<tr>";
                        BoxleagueCustomHelper::printMatchCell($contact);
                        echo "<td style='text-align:left'>" . "<a href='mailto:" . $contact->email . "'>" . $contact->email . "</a></td>";
                        echo "<td style='text-align:left'>" . str_replace("\"","",$contact->phone) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<p>To enter your match score click in the shaded area.</p>";
                    // print out the matches
                    echo "<table style='border: none' class='table table-condensed table-responsive'>";
                    echo "<tr><td style='border: 0;' colspan='3'><h5>Week 1</h5></td></tr>";
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[0], $players[2], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[1], $players[4], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[3], $players[5], $boxleague->bl_archive);
                    echo "<tr><td style='border: 0;' colspan='3'><h5>Week 2</h5></td></tr>";
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[0], $players[4], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[1], $players[5], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[2], $players[3], $boxleague->bl_archive);
                    echo "<tr><td style='border: 0;' colspan='3'><h5>Week 3</h5></td></tr>";
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[0], $players[5], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[1], $players[3], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[2], $players[4], $boxleague->bl_archive);
                    echo "<tr><td style='border: 0;' colspan='3'><h5>Week 4</h5></td></tr>";
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[0], $players[3], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[1], $players[2], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[4], $players[5], $boxleague->bl_archive);
                    echo "<tr><td style='border: 0;' colspan='3'><h5>Week 5</h5></td></tr>";
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[0], $players[1], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[3], $players[4], $boxleague->bl_archive);
                    BoxleagueCustomHelper::printMatch($matches, $players, $players[5], $players[2], $boxleague->bl_archive);
                    echo "<tr><td style='border: 0;' colspan='5'><h5>Week 6</h5></td></tr>";
                    echo "<tr><td colspan='5'>Spare</td></tr>";
                    echo "</table>";

                    // exit this function
                    return;
                }
            }
        }
    }

    public static function printMatchScore($matches, $player1, $player2, $archive)
    {
        JLog::add('printMatchScore() ' . $player1->name . " " . $player2->name, JLog::DEBUG, 'my-error-category');

        $user = JFactory::getUser();

        $matchScore = BoxleagueCustomHelper::getMatchScore($matches, $player1, $player2);

        // add link if user matches and boxleague is not archived
        $addlink = ($user->id == $player1->user_id || $user->authorise('core.admin')) && !$archive;

        if($addlink) {
            echo "<td style='background:#ffdcdc; font-weight: bold; min-width: 10px;'>";
            echo "<a style='display:block;' href='" . "/index.php/my-matches/match/edit/";
            echo $matchScore['id'];
            echo "'>";
        } else {
            echo "<td>";
        }

        JLog::add('score ' . $matchScore['score'], JLog::DEBUG, 'my-error-category');

        if(!is_null($matchScore['score'])){
            echo $matchScore['score'];
        } else {
            echo "&nbsp;";
        }

        if($addlink) {
            echo "</a></td>";
        } else {
            echo "</td>";
        }
        return $matchScore;
    }

    public static function printScoreBoard($box_id, $bx_name, $archive)
    {
        JLog::add('printScoreBoard() ' . $box_id . " " . $bx_name, JLog::DEBUG, 'my-error-category');

        $user = JFactory::getUser();
        $players = BoxleagueCustomHelper::getPlayersInBox($box_id);
        $matches = BoxleagueCustomHelper::getMatchesInBox($box_id);

        // create table and header rows

        echo "<div>";
        echo "<table style='' class='table table-bordered table-condensed table-responsive'>";

        // create header rows
        echo "<tr>";
        echo "<th style='color:white;background:#8445df;width:10px'>$nbsp</th>";
        echo "<th style='color:white;background:#8445df;width:150px'>$bx_name</th>";
        $count = 1;
        foreach ($players as $row) {
            echo "<th style='color:white;background:#8445df;text-align:center;width:20px'>$count</th>";
            $count++;
        }
        for ($i = $count; $i <= 6; $i++) {
            echo "<th style='color:white;background:#8445df;text-align:center;width:20px'>$nbsp</th>";
        }
        echo "<th style='color:white;background:#8445df;width:20px'>$nbsp</th>";
        echo "<tr>";

        // create body rows
        $count = 1;
        foreach ($players as $row) {
            $runningTotal = null;
            echo "<tr>";
            echo "<th style='color:white;background:#8445df;'>$count</th>";
            $count++;

            echo "<td style='text-align:left;'>";
            // add strong
            if($user->id == $row->user_id) {
                echo "<strong>$row->name</strong>";
            } else {
                echo $row->name;
            }
            echo "</td>";

            // create the row cells
            $cells = 0;
            foreach ($players as $column){
                $cells++;

                if($row->name == $column->name){
                    echo "<td style='background:#4db748'>&nbsp</td>";
                } else {
                    $matchScore = BoxleagueCustomHelper::printMatchScore($matches, $row, $column, $archive);
                    if(!is_null($matchScore['score'])) {
                        $runningTotal += $matchScore['score'];
                    }
                }
            }

            // add empty columns if less than 6 players
            for ($i = $cells; $i < 6; $i++) {
                echo "<td>&nbsp;</td>";
            }

            echo "<td>";
            if(!is_null($runningTotal)){
                echo "<strong>" . BoxleagueCustomHelper::calculatePlayerBoxScore($box_id, $row->id) . "</strong>";
            } else {
                echo "&nbsp;";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    public static function printPlayers($box_id)
    {
        JLog::add('printPlayers() ' . $box_id . " " . $bx_name, JLog::DEBUG, 'my-error-category');

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
            $score = BoxleagueCustomHelper::calculatePlayerBoxScore($box_id, $row->id);
            echo "<td style='text-align:right'>$score</td>";
            echo "<tr>";
        }
        for ($i = $count; $i > 0; $i--) {
            echo "<tr><td>&nbsp;</td></tr>";
        }
        echo "</table>";
    }

    public static function printBoxes($id, $archive)
    {
        JLog::add('printBoxes() ' . $id, JLog::DEBUG, 'my-error-category');

        // Get a database object
        $db = JFactory::getDbo();
        // Get all boxes for this boxleague
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__boxleague_box');
        $query->where($db->quoteName('boxleague_id') . ' = ' . $id);
        $query->order('ordering ASC');
        // sets up a database query for later execution
        $db->setQuery($query);
        // fetch result as an object list
        $result = $db->loadObjectList();
        foreach ($result as $row) {
            BoxleagueCustomHelper::printScoreBoard($row->id, $row->bx_name, $archive);
        }
    }

    public static function printBoxleague($id)
    {
        JLog::add('printBoxleague() ' . $id . " " . $bx_name, JLog::DEBUG, 'my-error-category');

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
            if(!$id->bl_archive){
                echo "<p>To enter your match score click on your box row in the shaded area.</p>";
                echo "<p>For player contact details and week by week matches go to <a href='index.php/my-matches'>My Matches</a>.</p>";
            }
            echo HtmlHelper::date($row->bl_start_date, Text::_('DATE_FORMAT_LC3'));
            echo " - ";
            echo HtmlHelper::date($row->bl_end_date, Text::_('DATE_FORMAT_LC3'));

            BoxleagueCustomHelper::printBoxes($row->id, $row->bl_archive);
        }
    }

    public static function canUserEdit($match, $user){
        JLog::add('canUserEdit()', JLog::DEBUG, 'my-error-category');

        $user = JFactory::getUser();

        if($user->authorise('core.admin')){
            return true;
        }

        $home_player = BoxleagueCustomHelper::getPlayerById($match->home_player);
        $away_player = BoxleagueCustomHelper::getPlayerById($match->away_player);

        $home_user   = JFactory::getUser($home_player->user_id);
        $away_user   = JFactory::getUser($away_player->user_id);

        $canEdit = $home_player->user_id == $user->id || $away_player->user_id == $user->id;

        return $canEdit;
    }
}