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

// We recovered the value of the argument op in the URL$
$op             = \Xmf\Request::getString('op', 'list');
$save_and_check = \Xmf\Request::getString('save_and_check', 'none');
$accounts_id    = \Xmf\Request::getInt('accounts_id', 0);
$post           = \Xmf\Request::getString('post', '');

if ('' == $post && 'save_accounts' === $op && 'none' === $save_and_check) {
    $op = 'edit_account';
}

switch ($op) {
    case 'check_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        if (0 == $accounts_id) {
            redirect_header($currentFile, 3, _AM_XNEWSLETTER_ERROR_NO_VALID_ID);
        } else {
            $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        }

        $mailhost = $accountObj->getVar('accounts_server_in');
        $port     = $accountObj->getVar('accounts_port_in');
        switch ($accountObj->getVar('accounts_type')) {
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3:
                $service = 'pop3';
                break;
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP:
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL:
                $service = 'imap';
                break;
            case 'default':
            default:
                $service = '';
                break;
        }
        $service_option      = $accountObj->getVar('accounts_securetype_in');
        $accounts_password   = $accountObj->getVar('accounts_password');
        $accounts_username   = $accountObj->getVar('accounts_username');
        $accounts_inbox      = $accountObj->getVar('accounts_inbox');
        $accounts_inbox_ok   = false;
        $accounts_hardbox    = $accountObj->getVar('accounts_hardbox');
        $accounts_hardbox_ok = false;
        $accounts_softbox    = $accountObj->getVar('accounts_softbox');
        $accounts_softbox_ok = false;

        $command = $mailhost . ':' . $port;
        if ('' != $service) {
            $command .= '/' . $service;
        }
        if ('' != $service_option) {
            $command .= '/' . $service_option;
        }

        echo "<table class='outer width100' cellspacing='1'>";
        echo '<tr>';
        echo '    <th></th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_INFO . '</th>';
        echo '</tr>';

        $mbox = @imap_open('{' . $command . '}', $accounts_username, $accounts_password); // || die ("can't connect: " . imap_last_error());
        if (false === $mbox) {
            echo '<tr>';
            echo '<td>' . XNEWSLETTER_IMG_FAILED . '</td>';
            echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX . _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . '</td>';
            echo '<td>' . imap_last_error() . '</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td>' . XNEWSLETTER_IMG_OK . '</td>';
            echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_MAILBOX . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . '</td>';
            echo '<td></td>';
            echo '</tr>';

            $folders = imap_list($mbox, '{' . $command . '}', '*');
            if (false === $folders) {
                echo '<tr>';
                echo '<td>' . XNEWSLETTER_IMG_FAILED . '</td>';
                echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS . _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . '</td>';
                echo '<td>' . imap_last_error() . '</td>';
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td>' . XNEWSLETTER_IMG_OK . '</td>';
                echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OPEN_FOLDERS . _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . '</td>';
                echo '<td>';
                if (is_array($folders)) {
                    //                    reset($folders);
                    sort($folders);
                    $foldercreated = 0;
                    //                    while (list($key, $val) = each($folders)) {
                    foreach ($folders as $key => $val) {
                        echo "($key) ";
                        echo imap_utf7_decode(str_replace('{' . $command . '}', '', $val)) . "<br>\n";
                        if ('{' . $command . '}' . $accounts_inbox == $val) {
                            $accounts_inbox_ok = true;
                        }
                        if ('{' . $command . '}' . $accounts_hardbox == $val) {
                            $accounts_hardbox_ok = true;
                        } else {
                            @imap_createmailbox($mbox, imap_utf7_encode('{' . $command . '}' . $accounts_hardbox));
                            $foldercreated = 1;
                        }
                        if ('{' . $command . '}' . $accounts_softbox == $val) {
                            $accounts_softbox_ok = true;
                        } else {
                            @imap_createmailbox($mbox, imap_utf7_encode('{' . $command . '}' . $accounts_softbox));
                            $foldercreated = 1;
                        }
                    }
                    if (1 == $foldercreated) {
                        $folders_recheck = imap_list($mbox, '{' . $command . '}', '*');
                        //                        while (list($key, $val) = each($folders_recheck)) {
                        foreach ($folders_recheck as $key => $val) {
                            if ('{' . $command . '}' . $accounts_hardbox == $val) {
                                $accounts_hardbox_ok = true;
                            }
                            if ('{' . $command . '}' . $accounts_softbox == $val) {
                                $accounts_softbox_ok = true;
                            }
                        }
                    }
                }

                echo '</td>';
                echo '</tr>';
                echo '<tr>';
                if ('1' == $accountObj->getVar('accounts_use_bmh')) {
                    if (true === $accounts_inbox_ok && true === $accounts_hardbox_ok && true === $accounts_softbox_ok) {
                        echo '<td>' . XNEWSLETTER_IMG_OK . '</td>';
                    } else {
                        echo '<td>' . XNEWSLETTER_IMG_FAILED . '</td>';
                    }
                    echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH . '</td>';
                    echo '<td>';
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_INBOX . ' ';
                    if (true === $accounts_inbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br>';
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_HARDBOX . ' ';
                    if (true === $accounts_hardbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br>';
                    echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH_SOFTBOX . ' ';
                    if (true === $accounts_softbox_ok) {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_OK . XNEWSLETTER_IMG_OK;
                    } else {
                        echo _AM_XNEWSLETTER_ACCOUNTS_CHECK_FAILED . XNEWSLETTER_IMG_FAILED;
                    }
                    echo '<br>';
                    echo '</td>';
                } else {
                    echo '<td>' . XNEWSLETTER_IMG_OK . '</td>';
                    echo '<td>' . _AM_XNEWSLETTER_ACCOUNTS_CHECK_BMH . _AM_XNEWSLETTER_ACCOUNTS_CHECK_SKIPPED . '</td>';
                    echo '<td></td>';
                }
                echo '</tr>';
            }
            imap_close($mbox);
        }
        echo '</table>';
        break;
    case 'list':
    case 'list_accounts':
    default:
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        $adminObject->displayButton('left');

        $limit            = $helper->getConfig('adminperpage');
        $accountsCriteria = new \CriteriaCompo();
        $accountsCriteria->setSort('accounts_id ASC, accounts_type');
        $accountsCriteria->setOrder('ASC');
        $accountsCount = $helper->getHandler('Accounts')->getCount();
        $start         = \Xmf\Request::getInt('start', 0);
        $accountsCriteria->setStart($start);
        $accountsCriteria->setLimit($limit);
        $accountsObjs = $helper->getHandler('Accounts')->getAll($accountsCriteria);
        if ($accountsCount > $limit) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new \XoopsPageNav($accountsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        echo "<table class='outer width100' cellspacing='1'>";
        echo '<tr>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_ID . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_TYPE . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_NAME . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_YOURNAME . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_YOURMAIL . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_ACCOUNTS_DEFAULT . '</th>';
        echo '    <th>' . _AM_XNEWSLETTER_FORMACTION . '</th>';
        echo '</tr>';

        if ($accountsCount > 0) {
            $class = 'odd';
            foreach ($accountsObjs as $accounts_id => $accountsObj) {
                echo "<tr class='" . $class . "'>";
                $class = ('even' === $class) ? 'odd' : 'even';
                echo "<td class='center'>{$accounts_id}</td>";
                $accounts_types = [
                    _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL     => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL,
                    _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL,
                    _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3         => _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3,
                    _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP         => _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP,
                    _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL        => _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL,
                ];
                echo "<td>{$accounts_types[$accountsObj->getVar('accounts_type')]}</td>";
                echo "<td>{$accountsObj->getVar('accounts_name')}</td>";
                echo "<td>{$accountsObj->getVar('accounts_yourname')}</td>";
                echo "<td>{$accountsObj->getVar('accounts_yourmail')}</td>";
                $verif_accounts_default = (1 == $accountsObj->getVar('accounts_default')) ? _YES : _NO;
                echo "<td class='center'>{$verif_accounts_default}</td>";

                echo "<td class='center'>";
                echo "    <a href='?op=edit_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>";
                echo "    <a href='?op=delete_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>";
                if (_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL != $accountsObj->getVar('accounts_type')
                    && _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL != $accountsObj->getVar('accounts_type')) {
                    echo "    <a href='?op=check_account&accounts_id={$accounts_id}'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_check.png alt='" . _AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK . "' title='" . _AM_XNEWSLETTER_ACCOUNTS_TYPE_CHECK . "'></a>";
                }
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '<br>';
        echo "<div>{$pagenav}</div>";
        echo '<br>';
        break;
    case 'new_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $accountObj = $helper->getHandler('Accounts')->create();
        $accountObj = xnewsletter_setPost($accountObj, $_POST);
        $form       = $accountObj->getForm();
        $form->display();
        break;
    case 'save_accounts':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $accountObj           = $helper->getHandler('Accounts')->get($accounts_id);
        $_POST['accounts_id'] = $accounts_id;
        $accountObj           = xnewsletter_setPost($accountObj, $_POST);

        $accountsCriteria = new \CriteriaCompo();
        $accountsCriteria->add(new \Criteria('accounts_default', 1));
        $count_accounts_default = $helper->getHandler('Accounts')->getCount($accountsCriteria);
        if ($count_accounts_default > 0) {
            if (1 == $accountObj->getVar('accounts_default')) {
                $verif_accounts_default = 1;
                //reset old accounts_default
                if (!$helper->getHandler('Accounts')->updateAll('accounts_default', 0, null, false)) {
                    exit('MySQL-Error: ' . $GLOBALS['xoopsDB']->error());
                }
            } else {
                $verif_accounts_default = 0;
            }
        } else {
            $verif_accounts_default = 1;
        }
        $accountObj->setVar('accounts_default', $verif_accounts_default);
        if ('' != $accountObj->getVar('accounts_yourmail')
            && _AM_XNEWSLETTER_ACCOUNTS_TYPE_YOUREMAIL != $accountObj->getVar('accounts_yourmail')) {
            if ($helper->getHandler('Accounts')->insert($accountObj)) {
                if ('none' === $save_and_check) {
                    redirect_header('?op=list', 3, _AM_XNEWSLETTER_FORMOK);
                } else {
                    redirect_header("?op=check_account&accounts_id={$accountObj->getVar('accounts_id')}", 3, _AM_XNEWSLETTER_FORMOK);
                }
            }
        } else {
            $accountObj->setErrors(_MA_XNEWSLETTER_SUBSCRIPTION_ERROR_NOEMAIL);
        }

        echo $accountObj->getHtmlErrors();
        $form = $accountObj->getForm();
        $form->display();
        break;
    case 'edit_account':
        $adminObject->displayNavigation($currentFile);
        $adminObject->addItemButton(_AM_XNEWSLETTER_NEWACCOUNTS, '?op=new_account', 'add');
        $adminObject->addItemButton(_AM_XNEWSLETTER_ACCOUNTSLIST, '?op=list', 'list');
        $adminObject->displayButton('left');

        $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        if (!empty($_POST)) {
            xnewsletter_setPost($accountObj, $_POST);
        }
        $form = $accountObj->getForm();
        $form->display();
        break;
    case 'delete_account':
        $accountObj = $helper->getHandler('Accounts')->get($accounts_id);
        if (true === \Xmf\Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($helper->getHandler('Accounts')->delete($accountObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $accountObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => true, 'accounts_id' => $accounts_id, 'op' => 'delete_account'], $currentFile, sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $accountObj->getVar('accounts_name')));
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
