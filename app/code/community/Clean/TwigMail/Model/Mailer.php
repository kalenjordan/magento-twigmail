<?php

abstract class Clean_TwigMail_Model_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    abstract protected function _getTemplateId();

    protected function _getRecipientEmails()
    {
        if (! $this->getData('recipient_email')) {
            throw new Exception("No recipient has been set on the TwigMail Mailer");
        }

        return $this->getData('recipient_email');
    }

    abstract protected function _getTemplateVariables();

    /**
     * Overload this with an array of emails to receive a BCC.
     * @return null
     */
    protected function _getBccEmails()
    {
        return null;
    }

    protected function _getSenderIdentity()
    {
        return 'customer/create_account/email_identity';
    }

    protected function _getEmailInfo()
    {
        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $recipient = $this->_getRecipientEmails();

        if (!$recipient) {
            throw new Exception("There wasn't any recipient specified for clean mailer email");
        }

        if (strpos($recipient, ',') !== false) {
            $recipients = explode(',', $recipient);
        } else {
            $recipients = array($recipient);
        }

        foreach ($recipients as $recipient) {
            $emailInfo->addTo(trim($recipient));
        }

        $bcc = $this->_getBccEmails();
        if (strpos($bcc, ',') !== false) {
            $bccArray = explode(',', $bcc);
        } else {
            $bccArray = array($bcc);
        }

        foreach ($bccArray as $bcc) {
            if ($bcc) {
                $emailInfo->addBcc($bcc);
            }
        }

        return $emailInfo;
    }

    public function send()
    {
        $this->addEmailInfo($this->_getEmailInfo())
            ->setSender(Mage::getStoreConfig($this->_getSenderIdentity()))
            ->setTemplateId($this->_getTemplateId())
            ->setTemplateParams($this->_getTemplateVariables());

        return parent::send();
    }
}
