<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  goffy (  )
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
 *  @copyright  goffy (  )
 *  @license    gpl 2.0
 *  @package    xnewsletter
 *  @author     goffy (  )
 *
 *  Version : 1 Mon 2012/11/05 14:31:32 :  Exp $
 * ****************************************************************************
 */

<div id="help-template" class="outer">
<{include file=$smarty.const._MI_XNEWSLETTER_HELP_HEADER}>

<h4 class="odd">DESCRIPTION</h4>
<p class="even">
    Newsletter module for xoops<br><br>
    Short description of basic functions:<br>
    - Usage of one or more e-mail-accounts<br>
    - Admin of one or more newsletter possible<br>
    - Subscription procedure optionally use confirmation system (double-opt-in)<br>
    - Detailed handling of permissions for subscription procedures<br>
    - Newsletter are based on templates<br>
    - Detailed handling of permissions for groups and newsletter (subscribe, write, send)<br>
    - Send: send test mail, resend to all subscribers or only to subscribers, where sending failed<br>
    - Sending newsletters to unlimited number of recipients or in packages with certain number of recipients by using
    cronjob<br>
    - Bounced email handler for handling Bounced emails in case of invalid e-mail-addresses<br>
    - Optionally synchronisation with mailing lists (e.g. majordomo)<br>
    - Maintenance function included<br>
    - Creating protocol for the important steps<br>
    <br>
</p>
<h4 class="odd">INSTALL/UNINSTALL</h4>
<p class="even">
    No special measures necessary, follow the standard installation process - extract the /xnewsletter folder into the
    ../modules directory. Install the module through Admin -> Modules. If you need detailed
    instructions on how to install a module, please see the <a
        href="https://www.gitbook.com/book/xoops/xoops-operations-guide/" target="_blank">XOOPS Operations
    Manual</a>.<br><br></p>
<h4 class="odd">TUTORIAL</h4>
<!-- -----Help Content ---------- -->


<h5 class="odd">Basic information</h5>
<p class="even">The module is based on PHPMailer and PHPMailer-BMH
    <br><br></p>

<h5 class="odd">(Un) subscriptions to newsletters</h5>
<p class="even">
    You can define for each newsletter and for each group, whether for (un) subscription or change a confirmation email
    with activation key is necessary or not (use double-option).<br>
    <br><br></p>

<h5 class="odd">Accounts</h5>
<p class="even">
    You can use one or more email-account; sending with php mail, php sendmail, smtp is possible.<br>
    There is a feature for testing pop3 and imap accounts.<br>
    <br>
    Pay attention: functions like testing account, sending emails, start Bounced email handler,... work not with local
    server (you get white page without any error).<br>
    <br><br></p>

<h5 class="odd">Newsletter categories</h5>
<p class="even">
    You can handle one or more newsletter categories.<br>
    It is possible to set permissions "read", "create", "admin" and "list subscribers" for each newsletter cat.<br>
    <br><br></p>

<h5 class="odd">Create a newsletter</h5>
<p class="even">
    The newsletter can be created with each text editor which is installed in current xoops core (e.g. TinyMCE). <br>
    For each newsletter you can use different templates (see also 'Newsletter templates').<br>
    You can define one or more newsletter categories for your newsletter.<br>
    You can add 5 files maximum as attachment to each newsletter.<br><br>
    Optionally you can also copy an older newsletter and edit or send it as a new one.<br><br>
    The type of text editor, allows mime-types and size of mail attachments can be set in module preferences.<br>
    <br><br></p>

<h5 class="odd">Newsletter templates</h5>
<p class="even">
    The newsletters are template based.<br>
    The templates are stored as files in language/{yourlanguage}/templates or stored in database.<br>
    To create a new template you can:
<ol>
    <li>make a new template html-file in in language/{yourlanguage}/templates folder and to put in the smarty-vars;</li>
    <li>from admin side create a new template item and to put in the smarty-vars.</li>
</ol>
<br>
This module uses the Xoops <a href="http://www.smarty.net/">Smarty template engine</a> to render the email letter.<br>
<br>
Available smarty-vars are:
<ul>
    <li>&lt;{$salutation}> or &lt;{$sex}>: the subscriber Salutation field</li>
    <li>&lt;{$firstname}>: the subscriber First name field</li>
    <li>&lt;{$lastname}>: the subscriber Last name field</li>
    <li>&lt;{$email}> or &lt;{$subscr_email}>: the subscriber Email field</li>
</ul>
<ul>
    <li>&lt;{$title}>: the newsletter Title field</li>
    <li>&lt;{$content}>: the newsletter Content field</li>
</ul>
<ul>
    <li>&lt;{$date}>: the sending date as timestamp integer <br><span style="font-size:0.9em;">(e.g.: &lt;{$date|date_format:"%Y/%m/%d"}> will output the date formatted as 2001/01/04)</span>
    </li>
    <li>&lt;{$unsubscribe_url}>: the unsubscribe url</li>
</ul>
<ul>
    <ul>
        <li>&lt;{$xoops_url}>: the site main url (e.g. http://localhost/)</li>
        <li>&lt;{$xoops_langcode}>: the site langcode (e.g. en)</li>
        <li>&lt;{$xoops_charset}>: the site cherset (e.g. UTF-8)</li>
    </ul>
    <br><br></p>

    <h5 class="odd">Sending newsletter</h5>
    <p class="even">
        You can show a preview of a newsletter before sending.<br>
        You can send the newsletter for testing to a defined email-address.<br>
        The newsletters will be sent subscriber by subscriber.<br>
        For each sending action a protocol will be created.<br>
        If one or more send failed, you can see it in the protocol.<br>
        You can restart sending procedure. You can send it again to all subscribers or send it only to the subscribers,
        where sending procedure failed).<br>
        <br>
        You can send all emails immediately or limit emails send in one package.<br>
        The number of emails and the minutes until next sending can be defined in module preferences (e.g. 200 emails
        all 60 minutes).<br>
        The first package will be sent immediately. To start the next sending procedure you need an external cronjob,
        which is calling "../modules/xnewsletter/cron.php". Xoops cannot do this with
        current version (2.5.5).<br>
        <br>
        Pay attention: functions like testing account, sending emails, start Bounced email handler,... work not with
        local server (you get white page).<br>
        <br><br></p>

    <h5 class="odd">Task list</h5>
    <p class="even">
        If you limit emails send in one package, you can see all newsletters waiting for next cronjob and the time, when
        cronjob can send the newsletter.<br>
        If you do not use this option, this list must always be empty.<br>
        Normally the tab "Task list" is hidden, if this option is disabled.<br>
        <br><br></p>

    <h5 class="odd">Handle mailing lists</h5>
    <p class="even">
        If you have an existing mailing list, you can synchronize the (un) subscriptions of one newsletter category with
        one mailing list.<br>
        I use majordomo beside this newsletter module because then I can also send an email from my email-client to the
        newsletter recipients.<br>
        One of the disadvantages of mailing lists is, that, if one person is registered in two or more mailing lists and
        you send a one newsletter to all mailing lists, this person gets the same
        newsletter more than one time. With xNewsletter he gets only one newsletter.<br>
        <br>
        <br>Normally the tab "Mailing list" is hidden, if this option is disabled.<br>
        <br>
        <b>xNewsletter cannot create mailing lists</b>.
        <br><br></p>

    <h5 class="odd">Bounced email handler (BMH)</h5>
    <p class="even">
        If you send newsletters, there will always be some emails not delivered to recipient (Bounced email), because
        email is no more valid, mailbox is full, and so on.<br>
        To handle this event and to react on this, you can use BMH.<br>
        You can activate BMH for each account.<br>
        Mails, which are detected as Bounced emails by BMH, can be deleted or moved in a special folder, you have to
        define.<br>
        Possible actions for Bounced emails:<br>
        -- no action (only store)<br>
        -- quit temporary the subscriptions of this email-address<br>
        -- delete the subscriptions of this email-address<br>
        <br>
        <br>
    <h6 class="odd">Types of Bounced emails</h6>
    <span style="text-decoration: underline;">bounce type hard:</span>
    This means that there is a permanent error when sending a mail, e.g. unknown recipient, unknown domain, and so
    on.<br>
    This mails will be deleted after detection, so it is recommended using the move hard option (the email will be only
    moved in this folder, you can check this mail later, if you want).<br>
    <br>
    <span style="text-decoration: underline;">bounce type soft:</span><br>
    This means that there is a temporary problem with sending a mail, e.g. mailbox full, server not available, and so
    on.<br>
    This mails will not be deleted after detection, but it is recommended using the move soft option in order to keep
    your basic in box clean.<br>
    <br>
    Pay attention: functions like testing account, sending emails, start Bounced email handler,... work not with local
    server (you get white page).<br>
    <br><br></p>

    <h5 class="odd">Maintenance</h5>
    <p class="even">
        This module has a maintain function, which can repair several faults in the data.<br>
        <br><br></p>

    <h5 class="odd">Import</h5>
    <p class="even">
        You can import data with various plug-ins:
    <ul>
        <li>csv</li>
        <li>module rmbulletin</li>
        <li>module smartpartner</li>
        <li>module weblinks</li>
        <li>module evennews</li>
        <li>module subscribers</li>
        <li>users from xoops users</li>
    </ul>
    The import tool works in this way:<br>
    - Adding email to the list of subscribers<br>
    - Subscribe this email to one newsletter cat<br>
    Before you run import tool therefore, please create first minimum one newsletter cat, otherwise the email cannot be
    subscribed to a cat and the import fails.<br>
    You have the possibility<br>
    - to import the E-Mail-Addresses without any check (recommended for big import data sets) or<br>
    - after reading data you can decide for each email<br>
    -- whether you want to make a subscription or not<br>
    -- to which newsletter you want to make the subscription<br>
    If an email is already registered, import of this email should be skipped (default action, do not touch existing
    registrations).
    <br>
    Sample files (sample1col.csv, sample4col.csv) for csv-import you can find in ../xnewsletter/plugins/
    <br><br>
    <b>Attention:</b>In case of registrations/subscription with this import tool there will not be sent an email
    notification to the email owner.<br>
    <br><br>
    <b>Importation of big email lists:</b><br>
    To avoid cache overflow during importation, there are two limits:<br>
    - first limit: only 100000 lines (e.g. of a csv-file) can be stored in temporary import table<br>
    - second limit: you can finally import data from temporary import table in packages of max. 25000<br>
    <br>
    For import of more than 100k emails following procedure can be done:<br>
    - run first import email 1 to 100000
    - import finally emails in packages of 25k
    - run second import emails 100001 to 200000 (select option "skip existing subscriptions")
    - import finally in packages of 25k
    and so on.<br>
    If file size is not too big to upload, you can repeat as often you want.<br>
    <br><br>
    If you get somewhere a white page, it is no problem because if option "skip existing subscriptions" is selected the
    import procedure restart at the first not imported email-address.<br>
    Reduce number of importing emails or package size and try again.<br>
    <br><br></p>

    </div>
