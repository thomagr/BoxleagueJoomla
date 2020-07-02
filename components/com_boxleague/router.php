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

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;

/**
 * Class BoxleagueRouter
 *
 */
class BoxleagueRouter extends RouterView
{
	private $noIDs;
	public function __construct($app = null, $menu = null)
	{
		$params = Factory::getApplication()->getParams('com_boxleague');
		$this->noIDs = (bool) $params->get('sef_ids');
		
		$boxleagues = new RouterViewConfiguration('boxleagues');
		$this->registerView($boxleagues);
			$boxleague = new RouterViewConfiguration('boxleague');
			$boxleague->setKey('id')->setParent($boxleagues);
			$this->registerView($boxleague);
			$boxleagueform = new RouterViewConfiguration('boxleagueform');
			$boxleagueform->setKey('id');
			$this->registerView($boxleagueform);$boxes = new RouterViewConfiguration('boxes');
		$this->registerView($boxes);
			$box = new RouterViewConfiguration('box');
			$box->setKey('id')->setParent($boxes);
			$this->registerView($box);
			$boxform = new RouterViewConfiguration('boxform');
			$boxform->setKey('id');
			$this->registerView($boxform);$players = new RouterViewConfiguration('players');
		$this->registerView($players);
			$player = new RouterViewConfiguration('player');
			$player->setKey('id')->setParent($players);
			$this->registerView($player);
			$playerform = new RouterViewConfiguration('playerform');
			$playerform->setKey('id');
			$this->registerView($playerform);$matches = new RouterViewConfiguration('matches');
		$this->registerView($matches);
			$match = new RouterViewConfiguration('match');
			$match->setKey('id')->setParent($matches);
			$this->registerView($match);
			$matchform = new RouterViewConfiguration('matchform');
			$matchform->setKey('id');
			$this->registerView($matchform);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));

		if ($params->get('sef_advanced', 0))
		{
			$this->attachRule(new StandardRules($this));
			$this->attachRule(new NomenuRules($this));
		}
		else
		{
			JLoader::register('BoxleagueRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
			JLoader::register('BoxleagueHelpersBoxleague', __DIR__ . '/helpers/boxleague.php');
			$this->attachRule(new BoxleagueRulesLegacy($this));
		}
	}


	
		/**
		 * Method to get the segment(s) for an boxleague
		 *
		 * @param   string  $id     ID of the boxleague to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getBoxleagueSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an boxleagueform
			 *
			 * @param   string  $id     ID of the boxleagueform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getBoxleagueformSegment($id, $query)
			{
				return $this->getBoxleagueSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an box
		 *
		 * @param   string  $id     ID of the box to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getBoxSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an boxform
			 *
			 * @param   string  $id     ID of the boxform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getBoxformSegment($id, $query)
			{
				return $this->getBoxSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an player
		 *
		 * @param   string  $id     ID of the player to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getPlayerSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an playerform
			 *
			 * @param   string  $id     ID of the playerform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getPlayerformSegment($id, $query)
			{
				return $this->getPlayerSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an match
		 *
		 * @param   string  $id     ID of the match to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getMatchSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an matchform
			 *
			 * @param   string  $id     ID of the matchform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getMatchformSegment($id, $query)
			{
				return $this->getMatchSegment($id, $query);
			}

	
		/**
		 * Method to get the segment(s) for an boxleague
		 *
		 * @param   string  $segment  Segment of the boxleague to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getBoxleagueId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an boxleagueform
			 *
			 * @param   string  $segment  Segment of the boxleagueform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getBoxleagueformId($segment, $query)
			{
				return $this->getBoxleagueId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an box
		 *
		 * @param   string  $segment  Segment of the box to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getBoxId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an boxform
			 *
			 * @param   string  $segment  Segment of the boxform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getBoxformId($segment, $query)
			{
				return $this->getBoxId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an player
		 *
		 * @param   string  $segment  Segment of the player to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getPlayerId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an playerform
			 *
			 * @param   string  $segment  Segment of the playerform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getPlayerformId($segment, $query)
			{
				return $this->getPlayerId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an match
		 *
		 * @param   string  $segment  Segment of the match to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getMatchId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an matchform
			 *
			 * @param   string  $segment  Segment of the matchform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getMatchformId($segment, $query)
			{
				return $this->getMatchId($segment, $query);
			}
}
