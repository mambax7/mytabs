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
 */

require_once __DIR__ . '/header.php';

if (\Xmf\Request::hasVar('op', 'REQUEST')) {
    $op = $_REQUEST['op'];
} else {
    redirect_header('main.php', 1, _NOPERM);
}

$pageblockHandler = new XoopsModules\Mytabs\PageBlockHandler();

switch ($op) {
    case 'save':

        if (!isset($_POST['pageblockid'])) {
            $block = $pageblockHandler->create();
        } elseif (!$block = $pageblockHandler->get($_POST['pageblockid'])) {
            $block = $pageblockHandler->create();
        }

        $block->setVar('pageid', $_POST['pageid']);
        $block->setVar('blockid', $_POST['blockid']);
        $block->setVar('title', $_POST['title']);

        if (\Xmf\Request::hasVar('options', 'POST') && (count($_POST['options']) > 0)) {
            $options = $_POST['options'];
            $count   = count($options);
            for ($i = 0; $i < $count; ++$i) {
                if (is_array($options[$i])) {
                    $options[$i] = implode(',', $options[$i]);
                }
            }
            $block->setVar('options', implode('|', $options));
        } else {
            $block->setVar('options', '');
        }

        $block->setVar('tabid', $_POST['tabid']);
        $block->setVar('priority', $_POST['priority']);
        $block->setVar('showalways', $_POST['alwayson']);
        $block->setVar('placement', $_POST['placement']);
        $block->setVar('fromdate', strtotime($_POST['fromdate']['date']) + $_POST['fromdate']['time']);
        $block->setVar('todate', strtotime($_POST['todate']['date']) + $_POST['todate']['time']);
        $block->setVar('pbcachetime', $_POST['pbcachetime']);
        $block->setVar('cachebyurl', $_POST['cachebyurl']);
        $block->setVar('note', $_POST['note']);
        $block->setVar('groups', $_POST['groups']);

        if ($pageblockHandler->insert($block)) {
            redirect_header('main.php?pageid=' . $block->getVar('pageid'), 1, _AM_MYTABS_SUCCESS);
        }
        break;

    case 'new':
    case 'edit':

        xoops_cp_header();
        mytabs_adminmenu(0);

        if ('new' === $op) {
            $block = $pageblockHandler->create();
            $block->setVar('pageid', $_REQUEST['pageid']);
            $block->setVar('tabid', $_POST['tabid']);
            $block->setVar('blockid', $_POST['blockid']);
            $block->setVar('fromdate', time());
            $block->setVar('todate', time());
            $block->setBlock($_POST['blockid']);
        } else {
            $block = $pageblockHandler->get($_REQUEST['pageblockid']);
            $block->setBlock();
        }
        $pageid = $block->getVar('pageid');

        echo '<a href="main.php">' . _AM_MYTABS_HOME . '</a>&nbsp;';

        if ($pageid > 0) {
            $pageHandler = new XoopsModules\Mytabs\PageHandler();
            $page        = $pageHandler->get($pageid);
            echo '&raquo;&nbsp;';
            echo '<a href="main.php?pageid=' . $pageid . '">' . $page->getVar('pagetitle') . '</a>';
        }

        $form = $block->getForm();
        echo $form->render();

        xoops_cp_footer();
        break;

    case 'delete':
        $obj = $pageblockHandler->get($_REQUEST['pageblockid']);
        if (\Xmf\Request::hasVar('ok', 'REQUEST') && 1 == $_REQUEST['ok']) {
            if ($pageblockHandler->delete($obj)) {
                redirect_header('main.php?pageid=' . $obj->getVar('pageid'), 3, sprintf(_AM_MYTABS_DELETEDSUCCESS, $obj->getVar('title')));
            } else {
                xoops_cp_header();
                echo implode('<br>', $obj->getErrors());
                xoops_cp_footer();
            }
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => 1, 'pageblockid' => $_REQUEST['pageblockid'], 'op' => 'delete'], 'block.php', sprintf(_AM_MYTABS_RUSUREDEL, $obj->getVar('title')));
            xoops_cp_footer();
        }
        break;
}
