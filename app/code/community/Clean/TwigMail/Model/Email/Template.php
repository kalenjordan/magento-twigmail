<?php

class Clean_TwigMail_Model_Email_Template extends Clean_TwigMail_Model_Email_Template_Abstract
{
    protected $_templateVariables;

    protected function _isTwigTemplate()
    {
        $data = $this->getData('template_config_data');
        if (is_array($data) && isset($data['renderer']) && $data['renderer'] == 'twig') {
            return true;
        }

        $templateText = $this->getTemplateText();
        if ($templateText && substr($templateText, 0, 10) == '{# twig #}') {
            return true;
        }

        return false;
    }

    public function getTemplateSubject()
    {
        if (! $this->_isTwigTemplate()) {
            return parent::getTemplateSubject();
        }

        if ($this->_isDefaultTemplate()) {
            $templateText = $this->getTemplateText();

            preg_match("/{#\ssubject:\s(.*)#}/", $templateText, $matches);
            if (!isset($matches[1])) {
                throw new Exception("Wasn't able to find subject line in template (ex: {# subject: Subject Line #}).");
            }

            return trim($matches[1]);
        } else {
            return $this->getData('template_subject');
        }
    }

    public function getProcessedTemplateSubject(array $variables)
    {
        if (! $this->_isTwigTemplate()) {
            return parent::getProcessedTemplateSubject($variables);
        }

        $templateSubject = $this->getTemplateSubject();
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);

        $processedTemplateSubject = $twig->render($templateSubject, $variables);

        return $processedTemplateSubject;
    }

    public function getProcessedTemplate(array $variables = array())
    {
        if (! $this->_isTwigTemplate()) {
            return parent::getProcessedTemplate($variables);
        }

        if (!isset($variables['logo_url'])) {
            $variables['logo_url'] = $this->_getLogoUrl(Mage::app()->getStore()->getId());
        }

        if (!isset($variables['store_url'])) {
            $variables['store_url'] = Mage::getBaseUrl();
        }

        if (!isset($variables['logo_alt'])) {
            $variables['logo_alt'] = $this->_getLogoAlt(Mage::app()->getStore()->getId());
        }

        if ($this->_isDefaultTemplate()) {
            return $this->_renderDefaultTemplate($variables);
        } else {
            return $this->_renderDatabaseTemplate($variables);
        }
    }

    protected function _isDefaultTemplate()
    {
        return $this->hasData('template_file_path');
    }

    protected function _initializeTwig($loader)
    {
        $twig = new Twig_Environment($loader);

        $engine = new \Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine();
        $extension = new \Aptoma\Twig\Extension\MarkdownExtension($engine);
        $twig->addExtension($extension);

        $tokenParser = new \Aptoma\Twig\TokenParser\MarkdownTokenParser($engine);
        $twig->addTokenParser($tokenParser);

        return $twig;
    }

    protected function _renderDefaultTemplate($variables)
    {
        $pathToEmailTemplates = Mage::getBaseDir('locale') .
            DS . $this->getData('locale') .
            DS . 'template' .
            DS . 'email';

        $loader = new Twig_Loader_Filesystem($pathToEmailTemplates);
        $twig = $this->_initializeTwig($loader);
        $filePath = $this->getData('template_file_path');

        return $twig->render($filePath, $variables);
    }

    protected function _renderDatabaseTemplate($variables)
    {
        $loader = new Twig_Loader_String();

        $content = $this->getTemplateText();
        $twig = $this->_initializeTwig($loader);

        return $twig->render($content, $variables);
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

    public function loadByCode($templateCode)
    {
        return parent::loadByCode($templateCode);
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

        $this->setTemplateText($templateText);
        $this->setId($templateId);
        $this->setTemplateFilePath($data['file']);
        $this->setLocale($locale);

        return $this;
    }
}