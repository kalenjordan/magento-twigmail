<?php

class Clean_TwigMail_Model_Mailer_Test extends Clean_TwigMail_Model_Mailer
{
    protected function _getTemplateId()
    {
        return 'clean_twigmail_test_email_template';
    }

    protected function _getTemplateVariables()
    {
        return array(
            'day_of_the_week' => date('D'),
            'subject'       => 'Test',
        );
    }
}