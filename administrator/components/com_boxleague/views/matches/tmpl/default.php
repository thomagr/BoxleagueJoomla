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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_boxleague/assets/css/boxleague.css');
$document->addStyleSheet(Uri::root() . 'media/com_boxleague/css/list.css');
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_boxleague');
$saveOrder = $listOrder == 'a.`ordering`';
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_boxleague&task=matches.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'matchList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_boxleague&view=matches'); ?>" method="post"
      name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif; ?>

            <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

            <div class="clearfix"></div>
            <table class="table table-striped" id="matchList">
                <thead>
                <tr>
                    <?php if (isset($this->items[0]->ordering)): ?>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                    <?php endif; ?>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <?php if (isset($this->items[0]->state)): ?>
                        <th width="1%" class="nowrap center">
                            <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_ID', 'a.`id`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_CREATED_BY', 'a.`created_by`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_MODIFIED_BY', 'a.`modified_by`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_BOXLEAGUE_ID', 'a.`boxleague_id`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_BOX_ID', 'a.`box_id`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_HOME_PLAYER', 'a.`home_player`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_AWAY_PLAYER', 'a.`away_player`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_HOME_SCORE', 'a.`home_score`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort', 'COM_BOXLEAGUE_MATCHES_AWAY_SCORE', 'a.`away_score`', $listDirn, $listOrder); ?>
                    </th>


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
                <?php foreach ($this->items as $i => $item) :
                    $ordering = ($listOrder == 'a.ordering');
                    $canCreate = $user->authorise('core.create', 'com_boxleague');
                    $canEdit = $user->authorise('core.edit', 'com_boxleague');
                    $canCheckin = $user->authorise('core.manage', 'com_boxleague');
                    $canChange = $user->authorise('core.edit.state', 'com_boxleague');
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">

                        <?php if (isset($this->items[0]->ordering)) : ?>
                            <td class="order nowrap center hidden-phone">
                                <?php if ($canChange) :
                                    $disableClassName = '';
                                    $disabledLabel = '';
                                    if (!$saveOrder) :
                                        $disabledLabel = Text::_('JORDERINGDISABLED');
                                        $disableClassName = 'inactive tip-top';
                                    endif; ?>
                                    <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
                                          title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
                                    <input type="text" style="display:none" name="order[]" size="5"
                                           value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
                                <?php else : ?>
                                    <span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td class="hidden-phone">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <?php if (isset($this->items[0]->state)): ?>
                            <td class="center">
                                <?php echo JHtml::_('jgrid.published', $item->state, $i, 'matches.', $canChange, 'cb'); ?>
                            </td>
                        <?php endif; ?>

                        <td>
                            <?php echo $item->id; ?>
                        </td>
                        <td>
                            <?php echo $item->created_by; ?>
                        </td>
                        <td>
                            <?php echo $item->modified_by; ?>
                        </td>
                        <td>
                            <?php echo $item->boxleague_id; ?>
                        </td>
                        <td>
                            <?php echo $item->box_id; ?>
                        </td>
                        <td>
                            <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'matches.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_boxleague&task=match.edit&id=' . (int)$item->id); ?>">
                                    <?php echo $this->escape($item->home_player); ?></a>
                            <?php else : ?>
                                <?php echo $this->escape($item->home_player); ?>
                            <?php endif; ?>

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

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[id];

        if (!cb) return false;

        while (true) {
            cbx = f['cb' + i];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField = document.createElement('input');

        inputField.type = 'hidden';
        inputField.name = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>