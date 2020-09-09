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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_boxleague') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'matchform.xml');
$canEdit = $user->authorise('core.edit', 'com_boxleague') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'matchform.xml');
$canCheckin = $user->authorise('core.manage', 'com_boxleague');
$canChange = $user->authorise('core.edit.state', 'com_boxleague');
$canDelete = $user->authorise('core.delete', 'com_boxleague');
// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_boxleague/css/list.css');
$userID = JFactory::getUser()->id;

?>

<div class="item_fields">

    <?php BoxleagueCustomHelper::printMatches($userId); ?>

</div>


