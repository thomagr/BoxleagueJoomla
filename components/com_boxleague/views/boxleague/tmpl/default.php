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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_boxleague');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_boxleague'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

    <?php BoxleagueCustomHelper::printBoxleague($this->item->id); ?>

</div>