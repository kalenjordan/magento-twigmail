<?php

class Clean_TwigMail_Block_Adminhtml_System_Config_SendTest extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsetData('scope');
        $element->unsetData('can_use_website_value');
        $element->unsetData('can_use_default_value');

        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url = $this->getUrl('admin_twigmail/adminhtml_test/sendTest');

        /** @var Mage_Adminhtml_Block_Widget_Button $button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'send_test',
                'label'     => $this->helper('twigmail')->__('Send Test'),
                'onclick'   => 'setLocation(\'' . $url . '\')'
            ));

        return $button->toHtml();
    }
}
