<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  Goffy ( wedega.com )
 *
 *  You may not change or alter any portion of this comment or credits
 *  of supporting developers from this source code or any supporting
 *  source code which is considered copyrighted (c) material of the
 *  original comment or credit authors.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  ---------------------------------------------------------------------------
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version : 1 Wed 2012/11/28 22:18:22 :  Exp $
 * ****************************************************************************
 */

use Xmf\Request;
use XoopsModules\Xnewsletter;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = "{$helper->getModule()->dirname()}_empty.tpl";
require_once XOOPS_ROOT_PATH . '/header.php';

$xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
$xoTheme->addMeta('meta', 'keywords', $helper->getConfig('keywords')); // keywords only for index page
$xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
// breadcrumb
$breadcrumb = new Xnewsletter\Breadcrumb();
$breadcrumb->addLink($helper->getModule()->getVar('name'), XNEWSLETTER_URL);
$xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

if (!$xoopsUser) {
    //Guest no Access !!!
    redirect_header(XOOPS_URL . '/modules/' . $helper->getModule()->dirname() . '/index.php', 3, _NOPERM);
}

$op        = Request::getString('op', 'list');
$letter_id = Request::getInt('letter_id', 0);

if ($letter_id < 1) {
    redirect_header('letter.php', 3, _AM_XNEWSLETTER_SEND_ERROR_NO_LETTERID);
}

$sendletter_perm = xnewsletter_getUserPermissionsByLetter($letter_id);

if (!$sendletter_perm['send']) {
    redirect_header(XOOPS_URL . '/modules/' . $helper->getModule()->dirname() . '/index.php', 3, _NOPERM);
}

$start_sending    = false;
$protocolCriteria = new \CriteriaCompo();
$protocolCriteria->add(new \Criteria('protocol_letter_id', $letter_id));
$protocolCriteria->add(new \Criteria('protocol_subscriber_id', 0, '>'));
$protocolCriteria->setLimit(1);
$protocolCount = $helper->getHandler('Protocol')->getCount($protocolCriteria);
if ('send_test' !== $op && $protocolCount > 0) {
    if (true === Request::getBool('ok', false, 'POST')) {
        $start_sending = true;
    } else {
        xoops_confirm(['ok' => true, 'op' => $op, 'letter_id' => $letter_id], $_SERVER['REQUEST_URI'], _AM_XNEWSLETTER_SEND_SURE_SENT);
    }
} else {
    $start_sending = true;
}

if (true === $start_sending) {
    $xn_send_in_packages = $helper->getConfig('xn_send_in_packages');
    if ($xn_send_in_packages > 0) {
        $xn_send_in_packages_time = $helper->getConfig('xn_send_in_packages_time');
    } else {
        $xn_send_in_packages_time = 0;
    }
    require_once XOOPS_ROOT_PATH . '/modules/xnewsletter/include/task.inc.php';
    // create tasks
    $result_create = xnewsletter_createTasks($op, $letter_id, $xn_send_in_packages, $xn_send_in_packages_time);
    // execute tasks
    $result_exec = xnewsletter_executeTasks($xn_send_in_packages, $letter_id);
    redirect_header('letter.php', 3, $result_exec);
}

require_once __DIR__ . '/footer.php';
