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
 * ****************************************************************************
 */

use Xmf\Request;

$currentFile = basename(__FILE__);
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// set template
$templateMain = 'xnewsletter_admin_maintenance.tpl';

// We recovered the value of the argument op in the URL$
$op = \Xmf\Request::getString('op', 'list');

$GLOBALS['xoopsTpl']->assign('xnewsletter_url', XNEWSLETTER_URL);
$GLOBALS['xoopsTpl']->assign('xnewsletter_icons_url', XNEWSLETTER_ICONS_URL);

switch ($op) {
    case 'list':
    default:
        $adminObject->displayNavigation($currentFile);
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $maintenance = "
            <table class='outer width75'>
                <tr>
                    <th>" . _AM_XNEWSLETTER_MAINTENANCE_CAT . '</th>
                    <th>' . _AM_XNEWSLETTER_MAINTENANCE_DESCR . '</th>
                    <th>' . _AM_XNEWSLETTER_MAINTENANCE_PARAM . '</th>
                    <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>
                </tr>';
        $class = 'odd';

        // delete protocols
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>protocol</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL . '</td>';
        $maintenance .= '            <td>&nbsp;</td>';
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_oldprotocol'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= "            <td class='center'>" . $cal_tray->render() . '</td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';

        // delete unconfirmed registrations
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>subscr</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETEDATE . '</td>';
        $cal = new \XoopsFormTextDateSelect('', 'del_date', 15, time() - (84600 * 10));
        $maintenance .= '            <td>' . $cal->render() . '</td>';
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_oldsubscr'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= "            <td class='center'>" . $cal_tray->render() . '</td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';

        // delete invalid catsubscr
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>catsubscr</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR . '</td>';
        $maintenance .= '            <td>&nbsp;</td>';
        $maintenance .= "            <td class='center'>";
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_invalid_catsubscr'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= $cal_tray->render();
        $maintenance .= '            </td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';
        
        // delete invalid catsubscr
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>catsubscr</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_CATDEL . '</td>';
        $maintenance .= '            <td>&nbsp;</td>';
        $maintenance .= "            <td class='center'>";
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_invalid_subscr_catdel'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= $cal_tray->render();
        $maintenance .= '            </td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';

        // check module preference xn_use_mailinglist with values in cat_mailinglist and check cat_mailinglist versus table mailinglist

        if (1 == $helper->getConfig('xn_use_mailinglist')) {
            $maintenance .= "   <tr class='{$class}'>";
            $class = ('even' === $class) ? 'odd' : 'even';
            $maintenance .= "        <form action='{$currentFile}' method='post'>";
            $maintenance .= '            <td>ml</td>';
            $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML . '</td>';
            $maintenance .= '            <td>&nbsp;</td>';
            $maintenance .= "            <td class='center'>";
            $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
            $cal_tray->addElement(new \XoopsFormHidden('op', 'del_invalid_ml'));
            $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
            $maintenance .= $cal_tray->render();
            $maintenance .= '            </td>';
            $maintenance .= '        </form>';
            $maintenance .= '    </tr>';
        }

        // delete invalid cat
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>cat</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL . '</td>';
        $maintenance .= '            <td>&nbsp;</td>';
        $maintenance .= "            <td class='center'>";
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_invalid_cat'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= $cal_tray->render();
        $maintenance .= '            </td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';

        // delete import
        $maintenance .= "   <tr class='{$class}'>";
        $class = ('even' === $class) ? 'odd' : 'even';
        $maintenance .= "        <form action='{$currentFile}' method='post'>";
        $maintenance .= '            <td>import</td>';
        $maintenance .= '            <td>' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT . '</td>';
        $maintenance .= '            <td>&nbsp;</td>';
        $maintenance .= "            <td class='center'>";
        $cal_tray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $cal_tray->addElement(new \XoopsFormHidden('op', 'del_import'));
        $cal_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
        $maintenance .= $cal_tray->render();
        $maintenance .= '            </td>';
        $maintenance .= '        </form>';
        $maintenance .= '    </tr>';

        $maintenance .= '</table>';
        $GLOBALS['xoopsTpl']->assign('maintenance', $maintenance);
        break;
    case 'del_import':
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            $result = $xoopsDB->queryF("TRUNCATE TABLE `{$xoopsDB->prefix('xnewsletter_import')}`");
            $result = $xoopsDB->queryF("REPAIR TABLE `{$xoopsDB->prefix('xnewsletter_import')}`");
            $result = $xoopsDB->queryF("OPTIMIZE TABLE `{$xoopsDB->prefix('xnewsletter_import')}`");
            $result = $xoopsDB->queryF("ALTER TABLE `{$xoopsDB->prefix('xnewsletter_import')}` AUTO_INCREMENT =1");

            $protocolObj = $helper->getHandler('Protocol')->create();
            $protocolObj->setVar('protocol_letter_id', 0);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' import] ' . _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK);
            $protocolObj->setVar('protocol_success', true);
            $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
            $protocolObj->setVar('protocol_created', time());

            if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                // IN PROGRESS
            }
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT_OK);
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_import'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETE_IMPORT);
        }
        break;
    case 'del_oldsubscr':
        $time = strtotime($_POST['del_date']);
        if ($time >= time() || 0 == $time) {
            $subscrCount = -1; // for error
        } else {
            $subscrCriteria = new \CriteriaCompo();
            $subscrCriteria->add(new \Criteria('subscr_activated', 0));
            $subscrCriteria->add(new \Criteria('subscr_created', $time, '<'));
            $subscrCount = $helper->getHandler('Subscr')->getCount($subscrCriteria);
        }

        if (true === Request::getBool('ok', false, 'POST')) {
            $deleted      = 0;
            $errors       = [];
            $subscrArrays = $helper->getHandler('Subscr')->getAll($subscrCriteria, ['subscr_id'], false, false);
            foreach ($subscrArrays as $subscrArray) {
                $subscrObj = $helper->getHandler('Subscr')->get((int)$subscrArray['subscr_id']);
                $sql       = 'DELETE';
                $sql       .= " FROM `{$xoopsDB->prefix('xnewsletter_subscr')}`";
                $sql       .= " WHERE subscr_id={$subscrArray['subscr_id']}";
                $result    = $xoopsDB->queryF($sql);
                if ($result) {
                    // Newsletterlist delete
                    $sql    = 'DELETE';
                    $sql    .= " FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}`";
                    $sql    .= " WHERE catsubscr_subscrid={$subscrArray['subscr_id']}";
                    $result = $xoopsDB->queryF($sql);
                    if (!$result) {
                        $errors[] = 'Error CAT-Subscr-ID: ' . $subscrArray['subscr_id'] . ' / ' . $result->getHtmlErrors();
                    }
                    ++$deleted;
                } else {
                    $errors[] = 'Error Subscr-ID: ' . $subscrArray['subscr_id'] . ' / ' . $result->getHtmlErrors();
                }
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $protocolObj = $helper->getHandler('Protocol')->create();
                    $protocolObj->setVar('protocol_letter_id', 0);
                    $protocolObj->setVar('protocol_subscriber_id', 0);
                    $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' reguser] ' . $error);
                    $protocolObj->setVar('protocol_success', false);
                    $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                    $protocolObj->setVar('protocol_created', time());
                    $helper->getHandler('Protocol')->insert($protocolObj);
                }
            }

            if ($deleted > 0) {
                $protocolObj = $helper->getHandler('Protocol')->create();
                $protocolObj->setVar('protocol_letter_id', 0);
                $protocolObj->setVar('protocol_subscriber_id', 0);
                $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' reguser] ' . sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK, $deleted));
                $protocolObj->setVar('protocol_success', true);
                $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                $protocolObj->setVar('protocol_created', time());
                $helper->getHandler('Protocol')->insert($protocolObj);
            }
            redirect_header($currentFile, 3, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSEROK, $deleted));
        } else {
            if ($subscrCount > 0) {
                xoops_confirm(['ok' => true, 'del_date' => $_POST['del_date'], 'op' => 'del_oldsubscr'], $currentFile, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETEUSER, $subscrCount, $_POST['del_date']));
            } else {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_MAINTENANCE_DELETENOTHING);
            }
        }
        break;
    case 'del_oldprotocol':
        if (true === Request::getBool('ok', false, 'POST')) {
            $result = $xoopsDB->queryF("TRUNCATE TABLE `{$xoopsDB->prefix('xnewsletter_protocol')}`");
            $result = $xoopsDB->queryF("REPAIR TABLE `{$xoopsDB->prefix('xnewsletter_protocol')}`");
            $result = $xoopsDB->queryF("OPTIMIZE TABLE `{$xoopsDB->prefix('xnewsletter_protocol')}`");
            $result = $xoopsDB->queryF("ALTER TABLE `{$xoopsDB->prefix('xnewsletter_protocol')}` AUTO_INCREMENT =1");

            $protocolObj = $helper->getHandler('Protocol')->create();
            $protocolObj->setVar('protocol_letter_id', 0);
            $protocolObj->setVar('protocol_subscriber_id', 0);
            $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' prot] ' . _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK);
            $protocolObj->setVar('protocol_success', true);
            $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
            $protocolObj->setVar('protocol_created', time());

            if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                // IN PROGRESS
            }
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOK);
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_oldprotocol'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETEPROTOCOL);
        }
        break;
    case 'del_invalid_catsubscr':
        //delete data in table catsubscr, if catsubscr_subscrid is no more existing in table subscr
        if (true === Request::getBool('ok', false, 'POST')) {
            $number_ids = 0;
            $deleted    = 0;
            $errors     = [];
            $sql        = 'SELECT Count(`catsubscr_id`) AS `nb_ids`';
            $sql        .= " FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_subscr')}` ON `catsubscr_subscrid` = `subscr_id`";
            $sql        .= ' WHERE (`subscr_id` Is Null)';
            $result     = $xoopsDB->query($sql);
            if ($result) {
                $row_result = $xoopsDB->fetchRow($result);
                $number_ids = $row_result[0];
            }
            if ($number_ids > 0) {
                $sql    = "DELETE `{$xoopsDB->prefix('xnewsletter_catsubscr')}`";
                $sql    .= " FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_subscr')}` ON `catsubscr_subscrid` = `subscr_id`";
                $sql    .= ' WHERE (`subscr_id` Is Null)';
                $result = $xoopsDB->query($sql);
                if ($result) {
                    ++$deleted;
                } else {
                    $errors[] = 'Error delete catsubscr: ' . $result->getHtmlErrors();
                }
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $protocolObj = $helper->getHandler('Protocol')->create();
                    $protocolObj->setVar('protocol_letter_id', 0);
                    $protocolObj->setVar('protocol_subscriber_id', 0);
                    $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' catsubscr] ' . $error);
                    $protocolObj->setVar('protocol_success', false);
                    $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                    $protocolObj->setVar('protocol_created', time());
                    if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                        $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                    }
                }
            } else {
                $protocolObj = $helper->getHandler('Protocol')->create();
                $protocolObj->setVar('protocol_letter_id', 0);
                $protocolObj->setVar('protocol_subscriber_id', 0);
                $status = 0 == $number_ids ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids);
                $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' catsubscr] ' . $status);
                $protocolObj->setVar('protocol_success', true);
                $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                $protocolObj->setVar('protocol_created', time());

                if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                    $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                }
                redirect_header($currentFile, 3, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids));
            }
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_invalid_catsubscr'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR);
        }
        break;
    case 'del_invalid_subscr_catdel':
        //delete data in table catsubscr, if cat is no more existing in table cat
        if (true === Request::getBool('ok', false, 'POST')) {
            $number_ids = 0;
            $deleted    = 0;
            $errors     = [];
            $sql        = 'SELECT Count(`catsubscr_id`) AS `nb_ids`';
            $sql        .= " FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_cat')}` ON `catsubscr_catid` = `cat_id`";
            $sql        .= ' WHERE (`cat_id` Is Null)';
            $result     = $xoopsDB->query($sql);
            if ($result) {
                $row_result = $xoopsDB->fetchRow($result);
                $number_ids = $row_result[0];
            }
            if ($number_ids > 0) {
                $sql    = "DELETE `{$xoopsDB->prefix('xnewsletter_catsubscr')}`";
                $sql    .= " FROM `{$xoopsDB->prefix('xnewsletter_catsubscr')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_cat')}` ON `catsubscr_catid` = `cat_id`";
                $sql    .= ' WHERE (`cat_id` Is Null)';
                $result = $xoopsDB->query($sql);
                if ($result) {
                    ++$deleted;
                } else {
                    $errors[] = 'Error delete catsubscr: ' . $result->getHtmlErrors();
                }
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $protocolObj = $helper->getHandler('Protocol')->create();
                    $protocolObj->setVar('protocol_letter_id', 0);
                    $protocolObj->setVar('protocol_subscriber_id', 0);
                    $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' catsubscr] ' . $error);
                    $protocolObj->setVar('protocol_success', false);
                    $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                    $protocolObj->setVar('protocol_created', time());
                    if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                        $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                    }
                }
            } else {
                $protocolObj = $helper->getHandler('Protocol')->create();
                $protocolObj->setVar('protocol_letter_id', 0);
                $protocolObj->setVar('protocol_subscriber_id', 0);
                $status = 0 == $number_ids ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids);
                $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' catsubscr] ' . $status);
                $protocolObj->setVar('protocol_success', true);
                $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                $protocolObj->setVar('protocol_created', time());

                if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                    $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                }
                redirect_header($currentFile, 3, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_OK, $number_ids));
            }
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_invalid_subscr_catdel'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_SUBCR_CATDEL);
        }
        break;
    case 'del_invalid_ml':
        if (true === Request::getBool('ok', false, 'POST')) {
            $use_mailinglist = $GLOBALS['xoopsModuleConfig']['xn_use_mailinglist'];
            $number_ids      = 0;
            $update          = 0;
            $errors          = [];
            if (0 == $use_mailinglist || '0' == $use_mailinglist) {
                //set cat_mailinglist = 0, if use mailinglist = false (if someone changed module preferences later)
                $sql = 'SELECT Count(`cat_id`) AS `nb_ids`';
                $sql .= " FROM `{$xoopsDB->prefix('xnewsletter_cat')}`";
                $sql .= ' WHERE (`cat_mailinglist` > 0)';
                $result = $xoopsDB->query($sql);
                if ($result) {
                    $row_result = $xoopsDB->fetchRow($result);
                    $number_ids = $row_result[0];
                }
                if ($number_ids > 0) {
                    $sql = "UPDATE `{$xoopsDB->prefix('xnewsletter_cat')}` SET `cat_mailinglist` = 0";
                    $result = $xoopsDB->query($sql);
                    if ($result) {
                        ++$update;
                    } else {
                        $errors[] = 'Error update cat_mailinglist: ' . $result->getHtmlErrors();
                    }
                }
            } else {
                //set cat_mailinglist = 0, if mailinglist_id is no more existing in table mailinglist
                $sql = 'SELECT Count(`cat_mailinglist`) AS `nb_ids`';
                $sql .= " FROM `{$xoopsDB->prefix('xnewsletter_cat')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_mailinglist')}` ON `cat_mailinglist` = `mailinglist_id`";
                $sql .= ' WHERE (((`mailinglist_id`) Is Null) AND ((`cat_mailinglist`)>0)) HAVING (((Count(`cat_mailinglist`))>0));';
                $result = $xoopsDB->query($sql);
                if ($result) {
                    $row_result = $xoopsDB->fetchRow($result);
                    $number_ids = $row_result[0];
                }
                if ($number_ids > 0) {
                    $sql = "UPDATE `{$xoopsDB->prefix('xnewsletter_cat')}` LEFT JOIN `{$xoopsDB->prefix('xnewsletter_mailinglist')}` ON `cat_mailinglist` = `mailinglist_id` SET `cat_mailinglist` = 0";
                    $sql .= ' WHERE (((`cat_mailinglist`)>0) AND ((`mailinglist_id`) Is Null));';
                    $result = $xoopsDB->query($sql);
                    if ($result) {
                        ++$update;
                    } else {
                        $errors[] = 'Error update cat_mailinglist: ' . $result->getHtmlErrors();
                    }
                }
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $protocolObj = $helper->getHandler('Protocol')->create();
                    $protocolObj->setVar('protocol_letter_id', 0);
                    $protocolObj->setVar('protocol_subscriber_id', 0);
                    $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' ml] ' . $error);
                    $protocolObj->setVar('protocol_success', false);
                    $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                    $protocolObj->setVar('protocol_created', time());
                    if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                        $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                    }
                }
            } else {
                $protocolObj = $helper->getHandler('Protocol')->create();
                $protocolObj->setVar('protocol_letter_id', 0);
                $protocolObj->setVar('protocol_subscriber_id', 0);
                $status = 0 == $number_ids ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK, $number_ids);
                $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' ml] ' . $status);
                $protocolObj->setVar('protocol_success', true);
                $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                $protocolObj->setVar('protocol_created', time());

                if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                    $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                }
            }
            redirect_header($currentFile, 3, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML_OK, $number_ids));
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_invalid_ml'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_ML);
        }
        break;
    case 'del_invalid_cat':
        //remove cat from letter_cats, if cat is missing (if someone deleted cat after creating letter)
        if (true === Request::getBool('ok', false, 'POST')) {
            $update     = 0;
            $errors     = [];
            $number_ids = 0;

            $letterObjs = $helper->getHandler('Letter')->getAll();
            foreach ($letterObjs as $letter_id => $letterObj) {
                $letter_cats_new = '';
                $letter_cats_old = $letterObj->getVar('letter_cats');
                $letter_cats     = [];
                $letter_cats     = explode('|', $letter_cats_old);

                foreach ($letter_cats as $cat_id) {
                    // check each cat and create new string 'letter_cats'
                    $catCriteria = new \CriteriaCompo();
                    $catCriteria->add(new \Criteria('cat_id', $cat_id));
                    $catCount = $helper->getHandler('Cat')->getCount($catCriteria);
                    if ($catCount > 0) {
                        $letter_cats_new .= $cat_id . '|';
                    }
                }
                $letter_cats_new = mb_substr($letter_cats_new, 0, -1);

                if ($letter_cats_old != $letter_cats_new) {
                    //update with correct value
                    $letterObj = $helper->getHandler('Letter')->get($letter_id);
                    $letterObj->setVar('letter_cats', $letter_cats_new);
                    if ($helper->getHandler('Letter')->insert($letterObj)) {
                        ++$update;
                    } else {
                        $errors[] = 'Error update cat: ' . $result->getHtmlErrors();
                    }
                }
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $protocolObj = $helper->getHandler('Protocol')->create();
                    $protocolObj->setVar('protocol_letter_id', 0);
                    $protocolObj->setVar('protocol_subscriber_id', 0);
                    $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' cat] ' . $error);
                    $protocolObj->setVar('protocol_success', false);
                    $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                    $protocolObj->setVar('protocol_created', time());
                    if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                        $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                    }
                }
            } else {
                $protocolObj = $helper->getHandler('Protocol')->create();
                $protocolObj->setVar('protocol_letter_id', 0);
                $protocolObj->setVar('protocol_subscriber_id', 0);
                $status = 0 == $update ? _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_NODATA : sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK, $update);
                $protocolObj->setVar('protocol_status', '[' . _MI_XNEWSLETTER_ADMENU11 . ' cat] ' . $status);
                $protocolObj->setVar('protocol_success', true);
                $protocolObj->setVar('protocol_submitter', $GLOBALS['xoopsUser']->uid());
                $protocolObj->setVar('protocol_created', time());

                if (!$helper->getHandler('Protocol')->insert($protocolObj)) {
                    $GLOBALS['xoopsTpl']->assign('error', _AM_XNEWSLETTER_MAINTENANCE_ERROR);
                }
            }
            redirect_header($currentFile, 3, sprintf(_AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL_OK, $number_ids));
        } else {
            xoops_confirm(['ok' => true, 'op' => 'del_invalid_cat'], $currentFile, _AM_XNEWSLETTER_MAINTENANCE_DELETE_INVALID_CATNL);
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
