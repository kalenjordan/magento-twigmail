<?php

require_once 'vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

class Clean_TwigMail_Model_Email_Template extends Clean_TwigMail_Model_Email_Template_Abstract
{
    protected $_templateVariables;

    protected function _isTwigTemplate()
    {
        $data = $this->getData('template_config_data');
        if (is_array($data) && isset($data['renderer']) && $data['renderer'] == 'twig') {
            return true;
        }

        return false;
    }

    public function getProcessedTemplateSubject(array $variables)
    {
        if (!isset($variables['subject'])) {
            throw new Exception("There should be a subject template variable set");
        }

        return $variables['subject'];
    }

    public function getProcessedTemplate(array $variables = array())
    {
        if (! $this->_isTwigTemplate()) {
            return parent::getProcessedTemplate($variables);
        }

        $pathToEmailTemplates = Mage::getBaseDir('locale') . DS . $this->getData('locale') . DS . 'template' . DS . 'email';
        $loader = new Twig_Loader_Filesystem($pathToEmailTemplates);
        $twig = new Twig_Environment($loader);

        $filePath = $this->getData('template_file_path');
        return $twig->render($filePath, $variables);
    }

    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        $this->_templateVariables = $vars;
        return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
    }

    public function getTemplateVariables()
    {
        return $this->_templateVariables;
    }

    public function setTemplateFilePath($filePath)
    {
        $this->setData('template_file_path', $filePath);
    }

    public function setLocale($locale)
    {
        $this->setData('locale', $locale);
    }

    public function loadDefault($templateId, $locale=null)
    {
        $defaultTemplates = self::getDefaultTemplates();
        if (!isset($defaultTemplates[$templateId])) {
            return $this;
        }

        $data = &$defaultTemplates[$templateId];
        $this->setData('template_config_data', $data);

        if ($this->_isTwigTemplate()) {
            return $this->_loadDefaultTwig($data, $templateId, $locale);
        }

        return parent::loadDefault($templateId, $locale);
    }

    protected function _loadDefaultTwig($data, $templateId, $locale = null)
    {
        $this->setTemplateType($data['type']=='html' ? self::TYPE_HTML : self::TYPE_TEXT);

        $templateText = Mage::app()->getTranslator()->getTemplateFile(
            $data['file'], 'email', $locale
        );

        $variables = $this->getTemplateVariables();
        if (!isset($variables['subject'])) {
            throw new Exception("There should be a subject line set on the template");
        }

        $this->setTemplateSubject($variables['subject']);
        $this->setTemplateText($templateText);
        $this->setId($templateId);

        $this->setTemplateFilePath($data['file']);
        $this->setLocale($locale);

        return $this;
    }
}