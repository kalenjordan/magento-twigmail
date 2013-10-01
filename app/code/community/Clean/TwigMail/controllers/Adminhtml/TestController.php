<?php

class Clean_TwigMail_Adminhtml_TestController extends Mage_Adminhtml_Controller_Action
{
    public function sendTestAction()
    {
        $testRecipient = Mage::getStoreConfig("trans_email/ident_general/email");

        $mailer = Mage::getModel('twigmail/mailer_test');
        $mailer->setData('recipient_email', $testRecipient);
        $mailer->send();

        Mage::getSingleton('adminhtml/session')->addSuccess("Sent test email to $testRecipient");
        $this->getResponse()->setRedirect($this->getUrl('adminhtml/system_config/edit', array('section' => 'system')));

        return $this;
    }
}