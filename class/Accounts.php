<?php

namespace XoopsModules\Xnewsletter;

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

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Accounts
 */
class Accounts extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('accounts_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('accounts_type', XOBJ_DTYPE_INT, _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL, false);
        $this->initVar('accounts_name', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_yourname', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_yourmail', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_username', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_password', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_server_in', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_port_in', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_securetype_in', XOBJ_DTYPE_TXTBOX, null, false, 20);
        $this->initVar('accounts_server_out', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_port_out', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_securetype_out', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('accounts_use_bmh', XOBJ_DTYPE_INT, null, false);
        $this->initVar('accounts_inbox', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_hardbox', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_movehard', XOBJ_DTYPE_INT, null, false);
        $this->initVar('accounts_softbox', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('accounts_movesoft', XOBJ_DTYPE_INT, null, false);
        $this->initVar('accounts_default', XOBJ_DTYPE_INT, null, false); // boolean
        $this->initVar('accounts_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('accounts_created', XOBJ_DTYPE_INT, time(), false);
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB;

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_ACCOUNTS_ADD) : sprintf(_AM_XNEWSLETTER_ACCOUNTS_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, $this->helper->getModule()->getVar('dirname') . '_form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $default = $this->getVar('accounts_type');

        switch ($default) {
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL:
            default:
                $dis_accounts_userpass     = true;
                $dis_accounts_server_in    = true;
                $dis_accounts_server_out   = true;
                $dis_accounts_use_bmh      = true;
                $dis_accounts_button_check = true;
                break;
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL:
                $dis_accounts_userpass     = false;
                $dis_accounts_server_in    = true;
                $dis_accounts_server_out   = false;
                $dis_accounts_use_bmh      = true;
                $dis_accounts_button_check = true;
                break;
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3:
                $dis_accounts_userpass     = false;
                $dis_accounts_server_in    = false;
                $dis_accounts_server_out   = false;
                $dis_accounts_use_bmh      = true;
                $dis_accounts_button_check = false;
                break;
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP:
            case _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL:
                $dis_accounts_userpass     = false;
                $dis_accounts_server_in    = false;
                $dis_accounts_server_out   = false;
                $dis_accounts_use_bmh      = false;
                $dis_accounts_button_check = false;
                break;
        }

        $accstd_select = new \XoopsFormSelect(_AM_XNEWSLETTER_ACCOUNTS_TYPE, 'accounts_type', $this->getVar('accounts_type'));
        $accstd_select->setExtra('onchange="document.forms.' . $this->helper->getModule()->getVar('dirname') . '_form.submit()"');
        $accstd_select->addOption(_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL, _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL);
        $accstd_select->addOption(_XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL, _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL);
        $accstd_select->addOption(_XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3, _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3);
        $accstd_select->addOption(_XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP, _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP);
        $accstd_select->addOption(_XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL, _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL);
        $form->addElement($accstd_select, true);

        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_NAME, 'accounts_name', 50, 255, $this->getVar('accounts_name')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_YOURNAME, 'accounts_yourname', 50, 255, $this->getVar('accounts_yourname')), true);
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_YOURMAIL, 'accounts_yourmail', 50, 255, $this->getVar('accounts_yourmail')), true);

        $form->addElement(new \XoopsFormRadioYN(_AM_XNEWSLETTER_ACCOUNTS_DEFAULT, 'accounts_default', $this->getVar('accounts_default'), _YES, _NO), false);

        if (false === $dis_accounts_userpass) {
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_USERNAME, 'accounts_username', 50, 255, $this->getVar('accounts_username')), true);
            $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_PASSWORD, 'accounts_password', 50, 255, $this->getVar('accounts_password')), true);
        }

        if (false === $dis_accounts_server_in) {
            $incomming_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_ACCOUNTS_INCOMING, '');
            $incomming_tray->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_SERVER_IN, 'accounts_server_in', 50, 255, $this->getVar('accounts_server_in')));
            $incomming_tray->addElement(new \XoopsFormText('<br>' . _AM_XNEWSLETTER_ACCOUNTS_PORT_IN, 'accounts_port_in', 50, 255, $this->getVar('accounts_port_in')));
            $formfield_securetype_in = new \XoopsFormSelect('<br>' . _AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_IN, 'accounts_securetype_in', $this->getVar('accounts_securetype_in'));
            $formfield_securetype_in->addOption('', '');
            $formfield_securetype_in->addOption('notls', 'NOTLS / STARTTLS');
            $formfield_securetype_in->addOption('ssl', 'SSL');
            $formfield_securetype_in->addOption('tls', 'TLS');
            $incomming_tray->addElement($formfield_securetype_in);
            $form->addElement($incomming_tray);
        }

        if (false === $dis_accounts_server_out) {
            $outcomming_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_ACCOUNTS_OUTGOING, '');
            $outcomming_tray->addElement(new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_SERVER_OUT, 'accounts_server_out', 50, 255, $this->getVar('accounts_server_out')));
            $outcomming_tray->addElement(new \XoopsFormText('<br>' . _AM_XNEWSLETTER_ACCOUNTS_PORT_OUT, 'accounts_port_out', 50, 255, $this->getVar('accounts_port_out')));
            $formfield_securetype_out = new \XoopsFormSelect('<br>' . _AM_XNEWSLETTER_ACCOUNTS_SECURETYPE_OUT, 'accounts_securetype_out', $this->getVar('accounts_securetype_out'));
            $formfield_securetype_out->addOption('', '');
            $formfield_securetype_out->addOption('notls', 'NOTLS / STARTTLS');
            $formfield_securetype_out->addOption('ssl', 'SSL');
            $formfield_securetype_out->addOption('tls', 'TLS');
            $outcomming_tray->addElement($formfield_securetype_out);
            $form->addElement($outcomming_tray);
        }

        if (false === $dis_accounts_use_bmh) {
            $form->addElement(new \XoopsFormLabel('', _AM_XNEWSLETTER_ACCOUNTS_BOUNCE_INFO));

            $formfield_use_bmh = new \XoopsFormRadioYN(_AM_XNEWSLETTER_ACCOUNTS_USE_BMH, 'accounts_use_bmh', $this->getVar('accounts_use_bmh'), _YES, _NO);
            $form->addElement($formfield_use_bmh, false);

            $formfield_inbox = new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_INBOX, 'accounts_inbox', 50, 255, $this->getVar('accounts_inbox'));
            $form->addElement($formfield_inbox, false);

            //Hardbox
            $hard_tray          = new \XoopsFormElementTray(_AM_XNEWSLETTER_BOUNCETYPE . ' ' . _XNEWSLETTER_BOUNCETYPE_HARD, '<br>');
            $formfield_movehard = new \XoopsFormRadioYN(_AM_XNEWSLETTER_ACCOUNTS_MOVEHARD, 'accounts_movehard', $this->getVar('accounts_movehard'), _YES, _NO);
            $hard_tray->addElement($formfield_movehard, false);
            $formfield_hardbox = new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_HARDBOX, 'accounts_hardbox', 50, 255, $this->getVar('accounts_hardbox'));
            $hard_tray->addElement($formfield_hardbox, false);
            $hard_tray->addElement(new \XoopsFormLabel('', _AM_XNEWSLETTER_ACCOUNTS_HARDBOX_DESC), false);
            $form->addElement($hard_tray, false);

            //Softbox
            $soft_tray          = new \XoopsFormElementTray(_AM_XNEWSLETTER_BOUNCETYPE . ' ' . _XNEWSLETTER_BOUNCETYPE_SOFT, '<br>');
            $formfield_movesoft = new \XoopsFormRadioYN(_AM_XNEWSLETTER_ACCOUNTS_MOVESOFT, 'accounts_movesoft', $this->getVar('accounts_movesoft'), _YES, _NO);
            $soft_tray->addElement($formfield_movesoft, false);
            $formfield_softbox = new \XoopsFormText(_AM_XNEWSLETTER_ACCOUNTS_SOFTBOX, 'accounts_softbox', 50, 255, $this->getVar('accounts_softbox'));
            $soft_tray->addElement($formfield_softbox, false);
            $soft_tray->addElement(new \XoopsFormLabel('', _AM_XNEWSLETTER_ACCOUNTS_HARDBOX_DESC), false);
            $form->addElement($soft_tray, false);
        }
        $time = $this->isNew() ? time() : $this->getVar('accounts_created');
        $form->addElement(new \XoopsFormHidden('accounts_submitter', $GLOBALS['xoopsUser']->uid()));
        $form->addElement(new \XoopsFormHidden('accounts_created', $time));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        $buttonTray = new \XoopsFormElementTray(' ', '&nbsp;&nbsp;');
        $buttonTray->addElement(new \XoopsFormHidden('op', 'save_accounts'));
        $buttonTray->addElement(new \XoopsFormButtonTray('', _SUBMIT, 'submit', '', false));
        if (false === $dis_accounts_button_check) {
            $button_check = new \XoopsFormButton('', 'save_and_check', _AM_XNEWSLETTER_SAVE_AND_CHECK, 'submit');
            $buttonTray->addElement($button_check);
        }
        $form->addElement($buttonTray, false);

        return $form;
    }

    /**
     * Get Values
     * @param null $keys
     * @param string|null $format
     * @param int|null $maxDepth
     * @return array
     */
    public function getValuesAccount($keys = null, $format = null, $maxDepth = null)
    {
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id'] = $this->getVar('accounts_id');
        $accounts_types = [
            _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_MAIL     => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPMAIL,
            _XNEWSLETTER_ACCOUNTS_TYPE_VAL_PHP_SENDMAIL => _AM_XNEWSLETTER_ACCOUNTS_TYPE_PHPSENDMAIL,
            _XNEWSLETTER_ACCOUNTS_TYPE_VAL_POP3         => _AM_XNEWSLETTER_ACCOUNTS_TYPE_POP3,
            _XNEWSLETTER_ACCOUNTS_TYPE_VAL_SMTP         => _AM_XNEWSLETTER_ACCOUNTS_TYPE_SMTP,
            _XNEWSLETTER_ACCOUNTS_TYPE_VAL_GMAIL        => _AM_XNEWSLETTER_ACCOUNTS_TYPE_GMAIL,
        ];
        $ret['type']           = $this->getVar('accounts_type');
        $ret['type_text']      = $accounts_types[$this->getVar('accounts_type')];
        $ret['name']           = $this->getVar('accounts_name');
        $ret['yourname']       = $this->getVar('accounts_yourname');
        $ret['yourmail']       = $this->getVar('accounts_yourmail');
        $ret['username']       = $this->getVar('accounts_username');
        $ret['password']       = $this->getVar('accounts_password');
        $ret['server_in']      = $this->getVar('accounts_server_in');
        $ret['port_in']        = $this->getVar('accounts_port_in');
        $ret['securetype_in']  = $this->getVar('accounts_securetype_in');
        $ret['server_out']     = $this->getVar('accounts_server_out');
        $ret['port_out']       = $this->getVar('accounts_port_out');
        $ret['securetype_out'] = $this->getVar('accounts_securetype_out');
        $ret['use_bmh']        = $this->getVar('accounts_use_bmh');
        $ret['inbox']          = $this->getVar('accounts_inbox');
        $ret['hardbox']        = $this->getVar('accounts_hardbox');
        $ret['movehard']       = $this->getVar('accounts_movehard');
        $ret['softbox']        = $this->getVar('accounts_softbox');
        $ret['movesoft']       = $this->getVar('accounts_movesoft');
        $ret['default']        = $this->getVar('accounts_default');
        $ret['default_text']   = $this->getVar('accounts_default') == 1 ? _YES : _NO;
        $ret['created']        = formatTimestamp($this->getVar('accounts_created'), 's');
        $ret['submitter']      = \XoopsUser::getUnameFromId($this->getVar('accounts_submitter'));
        return $ret;
    }
}
