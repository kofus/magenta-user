<?php 

namespace Kofus\System\Mvc\Service;

use Zend\Mvc\Service\TranslatorServiceFactory as BaseTranslatorFactory;
use Zend\ServiceManager\ServiceLocatorInterface;


class TranslatorServiceFactory extends BaseTranslatorFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return MvcTranslator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = parent::createService($serviceLocator);

        $config = $serviceLocator->get('Config');
        $pluginManagerConfig = isset($config['translator']['loaderpluginmanager']) ? $config['translator']['loaderpluginmanager'] : array();
        $pluginManager = new \Zend\I18n\Translator\LoaderPluginManager(new \Zend\ServiceManager\Config($pluginManagerConfig));
        $pluginManager->setServiceLocator($serviceLocator);
        $translator->setPluginManager($pluginManager);

        return $translator;
    }
}