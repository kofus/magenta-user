<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;

class LocaleService extends AbstractService
{
    public function init()
    {
        $this->setLocale($this->getLocale());
    }
    
    protected $locale;
    
    public function getLocale()
    {
        if (! $this->locale) {
            $session = new \Zend\Session\Container('Locale');
            if (isset($session->locale)) {
                $this->locale = $session->locale;
            } else {
                $this->locale = \Locale::getDefault();
            }
        }
        return $this->locale;
    }
    
    public function setLocale($locale)
    {
        $session = new \Zend\Session\Container('Locale');
        
        $this->locale = $locale;
        $this->getServiceLocator()->get('translator')->setLocale($locale);
        //setlocale(LC_ALL, $locale);
        $session->locale = $locale;
        \Locale::setDefault($locale);
        
        return $this;
    }
    
    public function getLanguage($locale=null)
    {
        if (! $locale)
            $locale = $this->getLocale();
    	return \Locale::getPrimaryLanguage($locale);
    }
    
    public function getDisplayLanguage($locale=null)
    {
    	if (! $locale)
    		$locale = $this->getLocale();
    	return \Locale::getDisplayLanguage($locale, $this->getLocale());
    }
    
    public function getDisplayRegion($locale=null)
    {
    	if (! $locale)
    		$locale = $this->getLocale();
    	return \Locale::getDisplayRegion($locale, $this->getLocale());
    }
    
    public function getRegion($locale=null)
    {
        if (! $locale)
            $locale = $this->getLocale();
    	return \Locale::getRegion($locale);
    }
    
    public function getIntlDateFormatter($dateType, $timeType)
    {
        $dateTime = new \DateTime();
        $formatter = new \IntlDateFormatter($this->getLocale(), $dateType, $timeType);
        /* if ($this->getLocale() == 'en_US' && $dateType == \IntlDateFormatter::MEDIUM) {
            $formatter->setPattern('MM/dd/yyyy');
        } */
        return $formatter;
    }
    
    public function __toString()
    {
        return $this->getLocale();
    }
    
	
}