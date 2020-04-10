<?php if (!defined('APPLICATION')) exit();

defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
define( 'MAILBOXVALIDATOR_ROOT', dirname( __FILE__ ) . DS );

echo $this->Form->Open();
echo $this->Form->Errors();
?>
<h1><?php echo T('Introduction'); ?></h1>
<div class="Info">
    <br/>
    <p style="font-size: 14px;">
        <?php echo T('This plugin enables user to block disposable email, detect free email type or reject an invalid email from sign up or using your services.'); ?>
    </p>
    <!----><p style="font-size: 14px;">Please enter the MailboxValidator API key, and enable the option to block the email domain. Your setting will be saved once you save the settings by clicking "Save Changes".</p>
</div>

<div class="Configuration">
    <ul>
        <li>
            <label for="MailboxValidator-API-Key"><?php echo T('MailboxValidator API Key'); ?></label>
            <?php echo $this->Form->TextBox('Plugins.MailboxValidator.APIKey', array('id' => 'MailboxValidator-API-Key', 'size' => 80, 'class' => 'InputBox')); ?>
        </li>
        <li>&nbsp;</li>
        <li>
            <!----><label>Valid Email Validator</label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.ValidEmailOption', T('On'), array('id' => 'MailboxValidator-Valid-Email', 'value' => 'on')); ?>
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.ValidEmailOption', T('Off'), array('id' => 'MailboxValidator-Valid-Email', 'value' => 'off')); ?>
            <br/>
            Block invalid email from sign up/using your service. This option will perform a comprehensive validation, including SMTP server check.
        </li>
        <li>&nbsp;</li>
        <!----><li>
            <label>Disposable Email Validator</label>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.DisposableEmailOption', T('On'), array('id' => 'MailboxValidator-Disposable-Email', 'value' => 'on')); ?>
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.DisposableEmailOption', T('Off'), array('id' => 'MailboxValidator-Disposable-Email', 'value' => 'off')); ?>
            <br/>
            Block disposable email from sign up/using your service.
        </li>
        <li>&nbsp;</li>
        <li>
            <label>Free Email Validator</label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.FreeEmailOption', T('On'), array('id' => 'MailboxValidator-Free-Email', 'value' => 'on')); ?>
            <?php echo $this->Form->Radio('Plugins.MailboxValidator.FreeEmailOption', T('Off'), array('id' => 'MailboxValidator-Free-Email', 'value' => 'off')); ?>
            <br/>
            Block free email type from sign up/using your service.
        </li>
        <li>&nbsp;</li>
        <!--<li>
            <label>Role Email Validator</label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php //echo $this->Form->Radio('Plugins.MailboxValidator.RoleEmailOption', T('On'), array('id' => 'MailboxValidator-Role-Email', 'value' => 'on')); ?>
            <?php //echo $this->Form->Radio('Plugins.MailboxValidator.RoleEmailOption', T('Off'), array('id' => 'MailboxValidator-Role-Email', 'value' => 'off')); ?>
            <br/>
            Block role based type email, such as admin@, support@, sales@ and so on, from sign up/using your service.
        </li>
        <li>&nbsp;</li>
        <li>
            <label>Custom domain blacklist</label>
            <br/>
            <?php //echo $this->Form->textBox('Plugins.MailboxValidator.CustomBlacklistDomains', array('id' => 'MailboxValidator-Custom-Blacklist-Domains', 'MultiLine' => TRUE)); ?>
            <br/>
            Enter the domain names (Seperated by "Enter") that you would like to block from sign up/using your service.
        </li>-->
    </ul>
</div>
<?php echo $this->Form->Close('Save Changes'); ?>