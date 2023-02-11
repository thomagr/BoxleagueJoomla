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
$canCreate = $user->authorise('core.create', 'com_boxleague') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'boxleagueform.xml');
$canEdit = $user->authorise('core.edit', 'com_boxleague') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'boxleagueform.xml');
$canCheckin = $user->authorise('core.manage', 'com_boxleague');
$canChange = $user->authorise('core.edit.state', 'com_boxleague');
$canDelete = $user->authorise('core.delete', 'com_boxleague');
// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_boxleague/css/list.css');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

    <?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    <div class="table-responsive">
        <table class="table table-striped" id="boxleagueList">
            <thead>
            <tr>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_BOXLEAGUES_BL_NAME', 'a.bl_name', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_BOXLEAGUES_BL_START_DATE', 'a.bl_start_date', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_BOXLEAGUES_BL_END_DATE', 'a.bl_end_date', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_BOXLEAGUES_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <th class=''>
                    <?php echo JHtml::_('grid.sort', 'COM_BOXLEAGUE_BOXLEAGUES_BL_ARCHIVE', 'a.bl_archive', $listDirn, $listOrder); ?>
                </th>


                <?php if ($canEdit || $canDelete): ?>
                    <th class="center">
                        <?php echo JText::_('COM_BOXLEAGUE_BOXLEAGUES_ACTIONS'); ?>
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

                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_boxleague&view=boxleague&id=' . (int)$item->id); ?>">
                            <?php echo $this->escape($item->bl_name); ?></a>
                    </td>
                    <td>

                        <?php
                        $date = $item->bl_start_date;
                        echo $date > 0 ? JHtml::_('date', $date, JText::_('DATE_FORMAT_LC3')) : '-';
                        ?>                </td>
                    <td>

                        <?php
                        $date = $item->bl_end_date;
                        echo $date > 0 ? JHtml::_('date', $date, JText::_('DATE_FORMAT_LC3')) : '-';
                        ?>
                    </td>

                    <td>
                        <?php echo $item->id; ?>
                    </td>

                    <td>
                        <?php if($item->bl_archive == 1) echo "Yes"; ?>
                    </td>



                    <?php if ($canEdit || $canDelete): ?>
                        <td class="center">
                            <?php if ($canEdit): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=boxleague.edit&id=' . $item->id, false, 2); ?>"
                                   class="btn btn-mini" type="button"><i class="icon-edit"></i></a>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=boxleagueform.remove&id=' . $item->id, false, 2); ?>"
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
        <a href="<?php echo Route::_('index.php?option=com_boxleague&task=boxleagueform.edit&id=0', false, 0); ?>"
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
