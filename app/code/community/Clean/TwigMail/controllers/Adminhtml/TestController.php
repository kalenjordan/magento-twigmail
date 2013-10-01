<?php

class Clean_TwigMail_Adminhtml_TestController extends Mage_Adminhtml_Controller_Action
{
    public function sendTestAction()
    {
        $mailer = Mage::getModel('twigmail/mailer_test');
        $mailer->setData('recipient_email', 'kalen@cleanprogram.com');
        $mailer->send();

        return $this;
    }
}