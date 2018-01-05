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
 *
 * @copyright  Goffy ( wedega.com )
 * @license    GPL 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 *  Version :
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = 'xnewsletter_protocol.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
//
$xoTheme->addStylesheet(XNEWSLETTER_URL . '/assets/css/module.css');
$xoTheme->addMeta('meta', 'keywords', $xnewsletter->getConfig('keywords')); // keywords only for index page
$xoTheme->addMeta('meta', 'description', strip_tags(_MA_XNEWSLETTER_DESC)); // description
// breadcrumb
$breadcrumb = new xnewsletterBreadcrumb();
$breadcrumb->addLink($xnewsletter->getModule()->getVar('name'), XNEWSLETTER_URL);
$breadcrumb->addLink(_MD_XNEWSLETTER_LIST, 'javascript:history.go(-1)');
$breadcrumb->addLink(_MD_XNEWSLETTER_PROTOCOL, '');
$xoopsTpl->assign('xnewsletter_breadcrumb', $breadcrumb->render());

$letter_id = Request::getInt('letter_id', 0);
$letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
$xoopsTpl->assign('letter', $letterObj->toArray());

$protocolCriteria = new CriteriaCompo();
$protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
$protocolCriteria->setSort('protocol_id');
$protocolCriteria->setOrder('DESC');
$protocolCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);

// protocol table
if ($protocolCount > 0) {
    $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria, null, true, true);
    foreach ($protocolObjs as $protocol_id => $protocolObj) {
        $protocol_array = $protocolObj->toArray();
        $subscrObj      = $xnewsletter->getHandler('subscr')->get($protocolObj->getVar('protocol_subscriber_id'));
        if (is_object($subscrObj)) {
            $subscr_array                                = $subscrObj->toArray();
            $protocol_array['subscr']                    = $subscr_array;
            $protocol_array['subscr']['subscriber_name'] = 0 != $subscrObj->getVar('subscr_uid') ? XoopsUserUtility::getUnameFromId($subscrObj->getVar('subscr_uid')) : '';
        } else {
            $protocol_array['subscr'] = false;
        }
        $protocol_array['protocol_created_formatted'] = formatTimestamp($protocolObj->getVar('protocol_created'), $xnewsletter->getConfig('dateformat'));
        $xoopsTpl->append('protocols', $protocol_array);
    }
}

require_once __DIR__ . '/footer.php';
