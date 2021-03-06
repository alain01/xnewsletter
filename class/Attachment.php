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
 * @license    GNU General Public License 2.0
 * @package    xnewsletter
 * @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

//use XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/common.php';

/**
 * Class Attachment
 */
class Attachment extends \XoopsObject
{
    public $helper = null;
    public $db;

    //Constructor

    public function __construct()
    {
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('attachment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_letter_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_name', XOBJ_DTYPE_TXTBOX, null, false, 200);
        $this->initVar('attachment_type', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('attachment_submitter', XOBJ_DTYPE_INT, null, false);
        $this->initVar('attachment_created', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('attachment_size', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('attachment_mode', XOBJ_DTYPE_INT, _XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, false);
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

        $title = $this->isNew() ? sprintf(_AM_XNEWSLETTER_ATTACHMENT_ADD) : sprintf(_AM_XNEWSLETTER_ATTACHMENT_EDIT);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_NAME, $this->getVar('attachment_name')));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_SIZE, "<span title='" . $this->getVar('attachment_size') . " B'>" . xnewsletter_bytesToSize1024($this->getVar('attachment_size')) . '</span>'));

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_ATTACHMENT_TYPE, $this->getVar('attachment_type')));

        // attachment_mode
        $mode_select = new \XoopsFormRadio(_AM_XNEWSLETTER_ATTACHMENT_MODE, 'attachment_mode', $this->getVar('attachment_mode'));
        $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASATTACHMENT, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASATTACHMENT);
        $mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_ASLINK, _AM_XNEWSLETTER_ATTACHMENT_MODE_ASLINK);
        //$mode_select->addOption(_XNEWSLETTER_ATTACHMENTS_MODE_AUTO, _AM_XNEWSLETTER_ATTACHMENT_MODE_AUTO);  // for future features
        $form->addElement($mode_select);

        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_SUBMITTER, $GLOBALS['xoopsUser']->uname()));
        $form->addElement(new \XoopsFormLabel(_AM_XNEWSLETTER_CREATED, formatTimestamp($time, 's')));

        $form->addElement(new \XoopsFormHidden('op', 'save_attachment'));
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
    public function getValuesAttachment($keys = null, $format = null, $maxDepth = null)
    {
        $ret['id']        = $this->getVar('attachment_id');
        $ret['letter_id'] = $this->getVar('attachment_letter_id');
        $ret['name']      = $this->getVar('attachment_name');
        $ret['type']      = $this->getVar('attachment_type');
        $ret['size']      = $this->getVar('attachment_size');
        $ret['mode']      = $this->getVar('attachment_mode');
        $ret['created']   = formatTimestamp($this->getVar('attachment_created'), 's');
        $ret['submitter'] = \XoopsUser::getUnameFromId($this->getVar('attachment_submitter'));
        return $ret;
    }
}
