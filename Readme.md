Magento TwigMail
================

Still pretty experimental, but provides basic support for Twig email templates for Magento.

Installation
------------

Add to your composer.json:

    "require": {
        "kalenjordan/magento-twigmail": "dev-master",
    }

Then, when you're declaring a template file, just add the `<renderer>twig</renderer>`
node.

    <template>
        <email>
            <clean_twigmail_test_email_template translate="label" module="cleanshare">
                <label>Test Email</label>
                <file>twigmail/test.html.twig</file>
                <type>html</type>
                <renderer>twig</renderer>
            </clean_twigmail_test_email_template>
        </email>
    </template>
    
You can trigger a test email by going to *System* > *Configuration* > 
*System* > *Twig E-Mail Templates* > *Send Test*

To Do
-----

Haven't quite worked out yet how best to allow template overrides to happen.  Should
be possible by just overriding the email template config node, but overriding using
transactional email templates in the databse isn't possible yet.

I tend to like managing email copy in the code base though, so it wasn't a huge
priority for me.
