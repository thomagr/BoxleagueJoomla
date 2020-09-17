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

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_boxleague', JPATH_SITE);
$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_boxleague/js/form.js');
$user = Factory::getUser();
$canEdit = BoxleagueHelpersBoxleague::canUserEdit($this->item, $user);

$match = BoxleagueCustomHelper::getMatchById($this->item->id);
$box = BoxleagueCustomHelper::getBoxById($match->box_id);
$boxleague = BoxleagueCustomHelper::getBoxById($box->boxleague_id);
$home_player = BoxleagueCustomHelper::getPlayerById($match->home_player);
$away_player = BoxleagueCustomHelper::getPlayerById($match->away_player);
$boxleague = BoxleagueCustomHelper::returnBoxleague($box->boxleague_id);
$home_user = JFactory::getUser($home_player->user_id);
$away_user = JFactory::getUser($away_player->user_id);

?>

<div class="match-edit front-end-edit">
    <?php if (!$canEdit) : ?>
        <h3>
            <?php throw new Exception(Text::_('COM_BOXLEAGUE_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
        </h3>
    <?php else : ?>
        <?php if (!empty($this->item->id)): ?>
            <h1><?php echo $box->bx_name; ?></h1>
            <div class="form-horizontal">
                <div class="control-group">
                    <div class="control-label">Home Player</div>
                    <div class="controls">
                        <input type="text" value="<?php echo $home_user->name; ?>" class="readonly" readonly="">
                    </div>
                </div>
            <div class="control-group">
                <div class="control-label">Away Player</div>
                <div class="controls">
                    <input type="text" value="<?php echo $away_user->name; ?>" class="readonly" readonly="">
                </div>
            </div>
        <?php else: ?>
            <h1><?php echo Text::_('COM_BOXLEAGUE_ADD_ITEM_TITLE'); ?></h1>
        <?php endif; ?>

        <form id="form-match"
              action="<?php echo Route::_('index.php?option=com_boxleague&task=match.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

            <?php echo $this->form->renderField('id'); ?>

            <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>"/>

            <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>

            <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>"/>

            <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>"/>

            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
            <?php echo $this->form->renderField('box_id'); ?>

            <?php foreach ((array)$this->item->box_id as $value): ?>
                <?php if (!is_array($value)): ?>
                    <input type="hidden" class="box_id" name="jform[box_idhidden][<?php echo $value; ?>]"
                           value="<?php echo $value; ?>"/>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php echo $this->form->renderField('home_player'); ?>

            <?php foreach ((array)$this->item->home_player as $value): ?>
                <?php if (!is_array($value)): ?>
                    <input type="hidden" class="home_player" name="jform[home_playerhidden][<?php echo $value; ?>]"
                           value="<?php echo $value; ?>"/>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php echo $this->form->renderField('away_player'); ?>

            <?php foreach ((array)$this->item->away_player as $value): ?>
                <?php if (!is_array($value)): ?>
                    <input type="hidden" class="away_player" name="jform[away_playerhidden][<?php echo $value; ?>]"
                           value="<?php echo $value; ?>"/>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php echo $this->form->renderField('home_score'); ?>

            <?php echo $this->form->renderField('away_score'); ?>

            <div class="control-group">
                <div class="controls">

                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-primary">
                            <?php echo Text::_('JSUBMIT'); ?>
                        </button>
                    <?php endif; ?>
                    <a class="btn"
                       href="<?php echo Route::_('index.php?option=com_boxleague&task=matchform.cancel'); ?>"
                       title="<?php echo Text::_('JCANCEL'); ?>">
                        <?php echo Text::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_boxleague"/>
            <input type="hidden" name="task"
                   value="matchform.save"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    <?php endif; ?>
</div>
