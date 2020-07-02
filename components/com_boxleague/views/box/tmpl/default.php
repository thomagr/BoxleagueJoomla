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

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_BOXLEAGUE_FORM_LBL_BOX_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_BOXLEAGUE_FORM_LBL_BOX_BOXLEAGUE_ID'); ?></th>
			<td><?php echo $this->item->boxleague_id; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_BOXLEAGUE_FORM_LBL_BOX_BX_NAME'); ?></th>
			<td><?php echo $this->item->bx_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_BOXLEAGUE_FORM_LBL_BOX_BX_ORDER'); ?></th>
			<td><?php echo $this->item->bx_order; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_boxleague&task=box.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_BOXLEAGUE_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_boxleague.box.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_BOXLEAGUE_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_BOXLEAGUE_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_BOXLEAGUE_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=box.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_BOXLEAGUE_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>