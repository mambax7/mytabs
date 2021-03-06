<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mytabs
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @param        $pageid
 * @param        $tabid
 * @param string $placement
 * @param string $remove
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");

function mytabs_blockShow($pageid, $tabid, $placement = '', $remove = '')
{
    $block     = [];
    $visblocks = [];

    $blocksHandler = new XoopsModules\Mytabs\PageBlockHandler(); // xoops_getModuleHandler('pageblock', 'mytabs');
    $blocks        = $blocksHandler->getBlocks($pageid, $tabid, $placement, $remove);

    $groups = $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getGroups() : [XOOPS_GROUP_ANONYMOUS];

    foreach (array_keys($blocks) as $key) {
        foreach ($blocks[$key] as $thisblock) {
            if ($thisblock->isVisible() && array_intersect($thisblock->getVar('groups'), $groups)) {
                $visblocks[] = $thisblock;
            }
        }
    }

    $count = count($visblocks);

    for ($i = 0; $i < $count; ++$i) {
        $logger_name = $visblocks[$i]->getVar('title') . '(' . $visblocks[$i]->getVar('pageblockid') . ')';
        $GLOBALS['xoopsLogger']->startTime($logger_name);
        $thisblock = $visblocks[$i]->render($GLOBALS['xoopsTpl'], $tabid . '_' . $visblocks[$i]->getVar('pageblockid'));
        if (false !== $thisblock) {
            if (strlen($thisblock['title']) > 0) {
                if ('-' == $thisblock['title'][0]) {
                    $thisblock['title'] = '';
                }
            }
            $block[] = $thisblock;
        }
        $GLOBALS['xoopsLogger']->stopTime($logger_name);
    }

    return $block;
}
