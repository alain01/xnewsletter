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
 * Class Cat
 */
class Cat extends \XoopsObject
{
    /**
     * @access public
     */
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('cat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_name', XOBJ_DTYPE_TXTBOX, '', false, 100);
        $this->initVar('cat_info', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('cat_mailinglist', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('cat_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('cat_created', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('dohtml', XOBJ_DTYPE_INT, false); // boolean
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, true); // boolean
        $this->initVar('doxcode', XOBJ_DTYPE_INT, true); // boolean
        $this->initVar('doimage', XOBJ_DTYPE_INT, true); // boolean
        $this->initVar('dobr', XOBJ_DTYPE_INT, true); // boolean
    }

    /**
     * @param bool $action
     *
     * @return \XoopsThemeForm
     */
    public function getForm($action = false)
    {
        global $xoopsDB;

        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');

        if (false === $action) {
            $action = $_SERVER['REQUEST_URI'];
        }

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_CAT_ADD) : sprintf(_AM_XNEWSLETTER_CAT_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        // cat_name
        $form->addElement(new \XoopsFormText(_AM_XNEWSLETTER_CAT_NAME, 'cat_name', 50, 255, $this->getVar('cat_name', 'e')), true);

        // cat_info
        $cat_info_dhtemtextarea = new \XoopsFormDhtmlTextArea(_AM_XNEWSLETTER_CAT_INFO, 'cat_info', $this->getVar('cat_info', 'e'), 10, 50);
        $cat_info_dhtemtextarea->setDescription(_AM_XNEWSLETTER_CAT_INFO_DESC);
        $form->addElement($cat_info_dhtemtextarea, false);

        // category: dohtml, dosmiley, doxcode, doimage, dobr
        $options_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_TEXTOPTIONS, ' ');
        $options_tray->setDescription(_AM_XNEWSLETTER_TEXTOPTIONS_DESC);
        $html_checkbox = new \XoopsFormCheckBox('', 'dohtml', $this->getVar('dohtml'));
        $html_checkbox->addOption(1, _AM_XNEWSLETTER_ALLOWHTML);
        $options_tray->addElement($html_checkbox);
        $smiley_checkbox = new \XoopsFormCheckBox('', 'dosmiley', $this->getVar('dosmiley'));
        $smiley_checkbox->addOption(1, _AM_XNEWSLETTER_ALLOWSMILEY);
        $options_tray->addElement($smiley_checkbox);
        $xcodes_checkbox = new \XoopsFormCheckBox('', 'doxcode', $this->getVar('doxcode'));
        $xcodes_checkbox->addOption(1, _AM_XNEWSLETTER_ALLOWXCODE);
        $options_tray->addElement($xcodes_checkbox);
        $noimages_checkbox = new \XoopsFormCheckBox('', 'doimage', $this->getVar('doimage'));
        $noimages_checkbox->addOption(1, _AM_XNEWSLETTER_ALLOWIMAGES);
        $options_tray->addElement($noimages_checkbox);
        $breaks_checkbox = new \XoopsFormCheckBox('', 'dobr', $this->getVar('dobr'));
        $breaks_checkbox->addOption(1, _AM_XNEWSLETTER_ALLOWBREAK);
        $options_tray->addElement($breaks_checkbox);
        $form->addElement($options_tray);

        // cat_gperms...
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        $userGroups    = $memberHandler->getGroupList();
        // create admin checkbox
        foreach ($userGroups as $group_id => $group_name) {
            if (XOOPS_GROUP_ADMIN == $group_id) {
                $group_id_admin   = $group_id;
                $group_name_admin = $group_name;
            }
        }
        $select_perm_admin = new \XoopsFormCheckBox('', 'admin', XOOPS_GROUP_ADMIN);
        $select_perm_admin->addOption($group_id_admin, $group_name_admin);
        $select_perm_admin->setExtra("disabled='disabled'");

        // permission read cat
        $cat_gperms_read     = $grouppermHandler->getGroupIds('newsletter_read_cat', $this->getVar('cat_id'), $this->helper->getModule()->mid());
        $arr_cat_gperms_read = $this->isNew() ? '0' : $cat_gperms_read;
        $perms_tray          = new \XoopsFormElementTray(_AM_XNEWSLETTER_CAT_GPERMS_READ, '');
        // checkbox webmaster
        $perms_tray->addElement($select_perm_admin, false);
        // checkboxes other groups
        $select_perm = new \XoopsFormCheckBox('', 'cat_gperms_read', $arr_cat_gperms_read);
        foreach ($userGroups as $group_id => $group_name) {
            if (XOOPS_GROUP_ADMIN != $group_id) {
                $select_perm->addOption($group_id, $group_name);
            }
        }
        $perms_tray->addElement($select_perm, false);
        $form->addElement($perms_tray, false);
        unset($perms_tray);
        unset($select_perm);

        // permission create cat
        $cat_gperms_create     = $grouppermHandler->getGroupIds('newsletter_create_cat', $this->getVar('cat_id'), $this->helper->getModule()->mid());
        $arr_cat_gperms_create = $this->isNew() ? '0' : $cat_gperms_create;
        $perms_tray            = new \XoopsFormElementTray(_AM_XNEWSLETTER_CAT_GPERMS_CREATE . _AM_XNEWSLETTER_CAT_GPERMS_CREATE_DESC, '');
        // checkbox webmaster
        $perms_tray->addElement($select_perm_admin, false);
        // checkboxes other groups
        $select_perm = new \XoopsFormCheckBox('', 'cat_gperms_create', $arr_cat_gperms_create);
        foreach ($userGroups as $group_id => $group_name) {
            if (XOOPS_GROUP_ADMIN != $group_id && XOOPS_GROUP_ANONYMOUS != $group_id) {
                $select_perm->addOption($group_id, $group_name);
            }
        }
        $perms_tray->addElement($select_perm, false);
        $form->addElement($perms_tray, false);
        unset($perms_tray);
        unset($select_perm);

        // permission admin cat
        $cat_gperms_admin     = $grouppermHandler->getGroupIds('newsletter_admin_cat', $this->getVar('cat_id'), $this->helper->getModule()->mid());
        $arr_cat_gperms_admin = $this->isNew() ? '0' : $cat_gperms_admin;
        $perms_tray           = new \XoopsFormElementTray(_AM_XNEWSLETTER_CAT_GPERMS_ADMIN . _AM_XNEWSLETTER_CAT_GPERMS_ADMIN_DESC, '');
        // checkbox webmaster
        $perms_tray->addElement($select_perm_admin, false);
        // checkboxes other groups
        $select_perm = new \XoopsFormCheckBox('', 'cat_gperms_admin', $arr_cat_gperms_admin);
        foreach ($userGroups as $group_id => $group_name) {
            if (XOOPS_GROUP_ADMIN != $group_id && XOOPS_GROUP_ANONYMOUS != $group_id) {
                $select_perm->addOption($group_id, $group_name);
            }
        }
        $perms_tray->addElement($select_perm, false);
        $form->addElement($perms_tray, false);
        unset($perms_tray);
        unset($select_perm);

        // permission list subscriber of this cat
        $cat_gperms_list      = $grouppermHandler->getGroupIds('newsletter_list_cat', $this->getVar('cat_id'), $this->helper->getModule()->mid());
        $arr_cat_gperms_admin = $this->isNew() ? '0' : $cat_gperms_list;

        $perms_tray = new \XoopsFormElementTray(_AM_XNEWSLETTER_CAT_GPERMS_LIST, '');
        // checkbox webmaster
        $perms_tray->addElement($select_perm_admin, false);
        // checkboxes other groups
        $select_perm = new \XoopsFormCheckBox('', 'cat_gperms_list', $arr_cat_gperms_admin);
        foreach ($userGroups as $group_id => $group_name) {
            if (XOOPS_GROUP_ADMIN != $group_id && XOOPS_GROUP_ANONYMOUS != $group_id) {
                $select_perm->addOption($group_id, $group_name);
            }
        }
        $perms_tray->addElement($select_perm, false);
        $form->addElement($perms_tray, false);
        unset($perms_tray);
        unset($select_perm);

        // cat_mailinglist
        $cat_mailinglist     = $this->isNew() ? '0' : $this->getVar('cat_mailinglist');
        $mailinglistCriteria = new \CriteriaCompo();
        $mailinglistCriteria->setSort('mailinglist_id');
        $mailinglistCriteria->setOrder('ASC');
        $numrows_mailinglist = $this->helper->getHandler('Mailinglist')->getCount();
        if ($numrows_mailinglist > 0) {
            $opt_mailinglist = new \XoopsFormRadio(_AM_XNEWSLETTER_LETTER_MAILINGLIST, 'cat_mailinglist', $cat_mailinglist);
            $opt_mailinglist->addOption('0', _AM_XNEWSLETTER_LETTER_MAILINGLIST_NO);
            $mailinglistObjs = $this->helper->getHandler('Mailinglist')->getAll($mailinglistCriteria);
            foreach ($mailinglistObjs as $mailinglist_id => $mailinglistObj) {
                $opt_mailinglist->addOption($mailinglist_id, $mailinglistObj->getVar('mailinglist_name'));
            }
            $form->addElement($opt_mailinglist);
        }

        $time = $this->isNew() ? time() : $this->getVar('cat_created');
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new \XoopsFormHidden('op', 'save_cat'));
        $form->addElement(new \XoopsFormButtonTray('', _SUBMIT, 'submit', '', false));

        return $form;
    }
    
    /**
     * Get Values
     * @param null $keys
     * @param string|null $format
     * @param int|null $maxDepth
     * @return array
     */
    public function getValuesCat($keys = null, $format = null, $maxDepth = null)
    {
        $helper = \XoopsModules\Xnewsletter\Helper::getInstance();
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']               = $this->getVar('cat_id');
        $ret['name']             = $this->getVar('cat_name');
        $ret['info']             = $this->getVar('cat_info');
        $ret['mailinglist']      = $this->getVar('cat_mailinglist');
        $ret['mailinglist_text'] = '';
        if ((int)$this->getVar('cat_mailinglist') > 0) {
            $ret['mailinglist_text'] = $helper->getHandler('Mailinglist')->get($this->getVar('cat_mailinglist'))->getVar('mailinglist_name');
        }
        $ret['dohtml']    = $this->getVar('dohtml');
        $ret['dosmiley']  = $this->getVar('dosmiley');
        $ret['doxcode']   = $this->getVar('doxcode');
        $ret['doimage']   = $this->getVar('doimage');
        $ret['dobr']      = $this->getVar('dobr');
        $ret['created']   = formatTimestamp($this->getVar('cat_created'), 's');
        $ret['submitter'] = \XoopsUser::getUnameFromId($this->getVar('cat_submitter'));
        return $ret;
    }
}
