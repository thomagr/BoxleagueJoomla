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
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_boxleague/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	js('input:hidden.box_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('box_idhidden')){
			js('#jform_box_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_box_id").trigger("liszt:updated");
	js('input:hidden.home_player').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('home_playerhidden')){
			js('#jform_home_player option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_home_player").trigger("liszt:updated");
	js('input:hidden.away_player').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('away_playerhidden')){
			js('#jform_away_player option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_away_player").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'match.cancel') {
			Joomla.submitform(task, document.getElementById('match-form'));
		}
		else {
			
			if (task != 'match.cancel' && document.formvalidator.isValid(document.id('match-form'))) {
				
				Joomla.submitform(task, document.getElementById('match-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_boxleague&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="match-form" class="form-validate form-horizontal">

	
				<?php echo $this->form->renderField('id'); ?>
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'match')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'match', JText::_('COM_BOXLEAGUE_TAB_MATCH', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_BOXLEAGUE_FIELDSET_MATCH'); ?></legend>
				<?php echo $this->form->renderField('box_id'); ?>
				<?php
				foreach((array)$this->item->box_id as $value)
				{
					if(!is_array($value))
					{
						echo '<input type="hidden" class="box_id" name="jform[box_idhidden]['.$value.']" value="'.$value.'" />';
					}
				}
				?>
				<?php echo $this->form->renderField('home_player'); ?>
				<?php
				foreach((array)$this->item->home_player as $value)
				{
					if(!is_array($value))
					{
						echo '<input type="hidden" class="home_player" name="jform[home_playerhidden]['.$value.']" value="'.$value.'" />';
					}
				}
				?>
				<?php echo $this->form->renderField('away_player'); ?>
				<?php
				foreach((array)$this->item->away_player as $value)
				{
					if(!is_array($value))
					{
						echo '<input type="hidden" class="away_player" name="jform[away_playerhidden]['.$value.']" value="'.$value.'" />';
					}
				}
				?>
				<?php echo $this->form->renderField('home_score'); ?>
				<?php echo $this->form->renderField('away_score'); ?>
				<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
