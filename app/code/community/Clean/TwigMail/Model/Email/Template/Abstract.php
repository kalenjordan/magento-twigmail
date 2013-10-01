<?php

if ((string)Mage::getConfig()->getModuleConfig('Aschroder_SMTPPro')->active == 'true') {
    class Clean_TwigMail_Model_Email_Template_Abstract extends Aschroder_SMTPPro_Model_Email_Template { }
} elseif ((string)Mage::getConfig()->getModuleConfig('Ebizmarts_Mandrill')->active == 'true') {
    class Clean_TwigMail_Model_Email_Template_Abstract extends Ebizmarts_Mandrill_Model_Email_Template { }
} else {
    class Clean_TwigMail_Model_Email_Template_Abstract extends Mage_Core_Model_Email_Template { }
}