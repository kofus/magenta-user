<?php
namespace Kofus\WebService;

return array(
    'controllers' => array(
        'invokables' => array(
            'Kofus\WebService\PayPalPlus\Controller\Experience' => 'Kofus\WebService\PayPalPlus\Controller\ExperienceController',
            'Kofus\WebService\PayPalPlus\Controller\Payment' => 'Kofus\WebService\PayPalPlus\Controller\PaymentController',
        )
    ),
    
    /*
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . str_replace('\\', '/', __NAMESPACE__) . '/Ekomi/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Ekomi\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ), */
    
    'router' => array(
        'routes' => array(
            'kofus_webservice_ppplus' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/webservice/ppplus/:controller[/:action[/:id[/:id2]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        'action' => 'index',
                        '__NAMESPACE__' => 'Kofus\WebService\PayPalPlus\Controller'
                    )
                ),
                'may_terminate' => true
            ),
        )
        
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'KofusWebService' => 'Kofus\WebService\Service\WebService',
            'KofusPiwikListener' => 'Kofus\WebService\Piwik\PiwikListener'
        )
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'webservice' => 'Kofus\WebService\Service\Controller\Plugin\WebServicePlugin'
        )
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'WebService' => __DIR__ . '/../view'
        ),
        'controller_map' => array(
            'Kofus\WebService' => true
        ),
        'module_layouts' => array(
            'Kofus\\WebService' => 'kofus/layout/admin'
        )
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'ekomi' => 'Kofus\WebService\Ekomi\View\Helper\EkomiHelper',
            //'shareBookmarks' => 'Kofus\WebService\Service\View\Helper\ShareBookmarksHelper'
        )
    ),
    
    'navigation' => array(
        'admin' => array(
            'paypal' => array(
                'uri' => '#',
                'label' => 'PayPal',
                'order' => 80,
                'pages' => array(
                    'profiles' => array(
                        'label' => 'Experiences',
                        'resource' => 'System',
                        'privilege' => 'administer',
                        'route' => 'kofus_webservice_ppplus',
                        'controller' => 'experience',
                        'action' => 'list'
                    ),
                    'requests' => array(
                    		'label' => 'API-Calls',
                    		'resource' => 'System',
                    		'privilege' => 'upgrade',
                    		'route' => 'kofus_archive',
                    		'controller' => 'http',
                    		'action' => 'list',
                    		'params' => array(
                    				'namespace' => 'PayPalPlus'
                    		)
                    ),
                    
    
    						)
            )
        )
    )
);