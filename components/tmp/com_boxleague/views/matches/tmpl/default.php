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
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">


    <div class="table-responsive">
        <table class="table table-striped" id="matchList">
            <thead>
            <tr>
                <?php if (isset($this->items[0]->state)): ?>
                    <th width="5%">
                        <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>

                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_MODIFIED_BY', 'a.modified_by', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_BOX_ID', 'a.box_id', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_HOME_PLAYER', 'a.home_player', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_AWAY_PLAYER', 'a.away_player', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_HOME_SCORE', 'a.home_score', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_MATCHES_AWAY_SCORE', 'a.away_score', $listDirn, $listOrder); ?>
                </th>


                <?php if ($canEdit || $canDelete): ?>
                    <th class="center">
                        <?php echo JText::_('COM_BOXLEAGUE_MATCHES_ACTIONS'); ?>
                    </th>
                <?php endif; ?>

            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <?php $canEdit = $user->authorise('core.edit', 'com_boxleague'); ?>

                <?php if (!$canEdit && $user->authorise('core.edit.own', 'com_boxleague')): ?>
                    <?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
                <?php endif; ?>

                <tr class="row<?php echo $i % 2; ?>">

                    <?php if (isset($this->items[0]->state)) : ?>
                        <?php $class = ($canChange) ? 'active' : 'disabled'; ?>
                        <td class="center">
                            <a class="btn btn-micro <?php echo $class; ?>"
                               href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_boxleague&task=match.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
                                <?php if ($item->state == 1): ?>
                                    <i class="icon-publish"></i>
                                <?php else: ?>
                                    <i class="icon-unpublish"></i>
                                <?php endif; ?>
                            </a>
                        </td>
                    <?php endif; ?>

                    <td>

                        <?php echo $item->id; ?>
                    </td>
                    <td>

                        <?php echo JFactory::getUser($item->created_by)->name; ?>                </td>
                    <td>

                        <?php echo JFactory::getUser($item->modified_by)->name; ?>                </td>
                    <td>

                        <?php echo $item->box_id; ?>
                    </td>
                    <td>
                        <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'matches.', $canCheckin); ?>
                        <?php endif; ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_boxleague&view=match&id=' . (int)$item->id); ?>">
                            <?php echo $this->escape($item->home_player); ?></a>
                    </td>
                    <td>

                        <?php echo $item->away_player; ?>
                    </td>
                    <td>

                        <?php echo $item->home_score; ?>
                    </td>
                    <td>

                        <?php echo $item->away_score; ?>
                    </td>


                    <?php if ($canEdit || $canDelete): ?>
                        <td class="center">
                            <?php if ($canEdit): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=match.edit&id=' . $item->id, false, 2); ?>"
                                   class="btn btn-mini" type="button"><i class="icon-edit"></i></a>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=matchform.remove&id=' . $item->id, false, 2); ?>"
                                   class="btn btn-mini delete-button" type="button"><i class="icon-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($canCreate) : ?>
        <a href="<?php echo Route::_('index.php?option=com_boxleague&task=matchform.edit&id=0', false, 0); ?>"
           class="btn btn-success btn-small"><i
                    class="icon-plus"></i>
            <?php echo Text::_('COM_BOXLEAGUE_ADD_ITEM'); ?></a>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php if ($canDelete) : ?>
    <script type="text/javascript">

        jQuery(document).ready(function () {
            jQuery('.delete-button').click(deleteItem);
        });

        function deleteItem() {

            if (!confirm("<?php echo Text::_('COM_BOXLEAGUE_DELETE_MESSAGE'); ?>")) {
                return false;
            }
        }
    </script>
<?php endif; ?>
