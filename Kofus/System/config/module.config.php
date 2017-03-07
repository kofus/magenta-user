<?php
namespace Kofus\System;

include_once __DIR__ . '/../../framework.config.php';

return array(
    'controllers' => array(
        'invokables' => array(
            'Kofus\System\Controller\Database' => 'Kofus\System\Controller\DatabaseController',
            'Kofus\System\Controller\Node' => 'Kofus\System\Controller\NodeController',
            'Kofus\System\Controller\Relation' => 'Kofus\System\Controller\RelationController',
            'Kofus\System\Controller\Content' => 'Kofus\System\Controller\ContentController',
            'Kofus\System\Controller\Page' => 'Kofus\System\Controller\PageController',
            'Kofus\System\Controller\Error' => 'Kofus\System\Controller\ErrorController',
            'Kofus\System\Controller\Cron' => 'Kofus\System\Controller\CronController',
            'Kofus\System\Controller\CronScheduler' => 'Kofus\System\Controller\CronSchedulerController',
            'Kofus\System\Controller\Search' => 'Kofus\System\Controller\SearchController',
            'Kofus\System\Controller\Batch' => 'Kofus\System\Controller\BatchController',
        )
    ),
    
    'user' => array(
    		'acl' => array(
    				'resources' => array(
    						'System'
    				)
    		),
    		'controller_mappings' => array(
    				'Kofus\System\Controller\Database' => 'System',
    				'Kofus\System\Controller\Search' => 'System',
    				'Kofus\System\Controller\Batch' => 'System',
    				'Kofus\System\Controller\Cron' => 'Frontend',
    		        'Kofus\System\Controller\CronScheduler' => 'System'
    		)
    ),
    
    
    'controller_plugins' => array(
        'invokables' => array(
            'em' => 'Kofus\System\Controller\Plugin\EmPlugin',
            'nodes' => 'Kofus\System\Controller\Plugin\NodesPlugin',
            'links' => 'Kofus\System\Controller\Plugin\LinksPlugin',
            'config' => 'Kofus\System\Controller\Plugin\ConfigPlugin',
            'translations' => 'Kofus\System\Controller\Plugin\TranslationsPlugin',
            'formBuilder' => 'Kofus\System\Controller\Plugin\FormBuilderPlugin',
            'translator' => 'Kofus\System\Controller\Plugin\TranslatorPlugin',
            'locale' => 'Kofus\System\Controller\Plugin\LocalePlugin',
            'paginator' => 'Kofus\System\Controller\Plugin\PaginatorPlugin',
            'lucene' => 'Kofus\System\Controller\Plugin\LucenePlugin',
            'viewHelper' => 'Kofus\System\Controller\Plugin\ViewHelperPlugin',
            'settings' => 'Kofus\System\Controller\Plugin\SettingsPlugin'
        )
    ),
    

    
    'public_paths' => array(
    	__DIR__ . '/../public'
    ),
    
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                // 'name' => 'KOFUS',
                'use_cookies' => true,
                'cookie_httponly' => true
            // 'gc_maxlifetime' => $SESSION_REMEMBERME_SECONDS,
            // 'cookie_lifetime' => $SESSION_REMEMBERME_SECONDS,
            // 'rememberme_seconds' => $SESSION_REMEMBERME_SECONDS
                        )
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            //'Zend\Session\Validator\RemoteAddr',
            //'Zend\Session\Validator\HttpUserAgent'
        )
    ),
    
    'translator' => array(
        // 'locale' => 'de_DE',
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php'
            )
        ),
        'loaderpluginmanager' => [
            'factories' => [
                'nodes' => function ($lpm)
                {
                    $sm = $lpm->getServiceLocator();
                    $loader = new \Kofus\System\I18n\Translator\Loader\Nodes($sm);
                    return $loader;
                }
            ]
        ],
        'remote_translation' => [
            [
                'type' => 'nodes'
            ]
        ]
    ),
    
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . str_replace('\\', '/', __NAMESPACE__) . '/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
    
    'console' => array(
        	'router' => array(
    	'routes' => array(
    	    'cron' => array(
    	    		'type'    => 'simple',       
    	    		'options' => array(
    	    				'route'    => 'cron',
    	    				'defaults' => array(
    	    						'controller' => 'Kofus\System\Controller\Cron',
    	    						'action'     => 'trigger'
    	    				)
    	    		)
    	    )
        	)
    )
        ),
    
    'router' => array(
        'routes' => array(

            'error' => array(
                'type' => 'Kofus\System\Mvc\ErrorRoute',
                'may_terminate' => true,
                'options' => array(
                    'defaults' => array(
                        '__NAMESPACE__' => 'Kofus\System\Controller',
                        'controller' => 'error',
                        'action' => 'index'
                    )
                )
            ),
            'kofus_system' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/system/:controller/:action[/:id[/:id2]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        '__NAMESPACE__' => 'Kofus\System\Controller'
                    )
                ),
                'may_terminate' => true
            ),
            'cron' => array(
            		'type' => 'Segment',
            		'options' => array(
            				'route' => '/cron[/:passphrase[/:id]]',
            				'defaults' => array(
            						'__NAMESPACE__' => 'Kofus\System\Controller',
            						'controller' => 'cron',
            						'action' => 'trigger'
            				)
            		),
            ),
            
        )
    ),
    'view_manager' => array(
        'doctype' => 'HTML5',
        'not_found_template' => 'kofus/error/404',
        'exception_template' => 'kofus/error/exception',
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/backend.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'controller_map' => array(
            'Kofus\System' => true
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'module_layouts' => array(
            'Kofus\System\Controller\Database' => 'kofus/layout/admin',
            'Kofus\System\Controller\Node' => 'kofus/layout/admin',
            'Kofus\System\Controller\Page' => 'kofus/layout/admin',
            'Kofus\System\Controller\Relation' => 'kofus/layout/admin',
        	'Kofus\System\Controller\Search' => 'kofus/layout/admin',
        	'Kofus\System\Controller\Batch' => 'kofus/layout/admin',
            'Kofus\System\Controller\CronScheduler' => 'kofus/layout/admin',
            
        )
    ),
    
   
    'view_helpers' => array(
        'invokables' => array(
            'flashMessages' => 'Kofus\System\View\Helper\FlashMessagesHelper',
            'bodyTag' => 'Kofus\System\View\Helper\BodyTagHelper',
            'assets' => 'Kofus\System\View\Helper\AssetsHelper',
            'optimizer' => 'Kofus\System\View\Helper\OptimizerHelper',
            'config' => 'Kofus\System\View\Helper\ConfigHelper',
            'kofusNavigation' => 'Kofus\System\View\Helper\NavigationHelper',
            'locale' => 'Kofus\System\View\Helper\LocaleHelper',
            'translateNode' => 'Kofus\System\View\Helper\TranslateNodeHelper',
            'translateLink' => 'Kofus\System\View\Helper\TranslateLinkHelper',
            'navTree' => 'Kofus\System\View\Helper\Navigation\TreeHelper',
            'nodes' => 'Kofus\System\View\Helper\NodesHelper',
            'formFieldset' => 'Kofus\System\View\Helper\Form\FieldsetHelper',
            'spamSpan' => 'Kofus\System\View\Helper\SpamSpanHelper',
            'paginationColumnSort' => 'Kofus\System\View\Helper\PaginationColumnSortHelper',
            'session' => 'Kofus\System\View\Helper\SessionHelper',
			'nodeNavigation' => 'Kofus\System\View\Helper\NodeNavigationHelper',
            'shortenString' => 'Kofus\System\View\Helper\ShortenStringHelper',
            'implodeValidPieces' => 'Kofus\System\View\Helper\ImplodeValidPiecesHelper',
            'settings' => 'Kofus\System\View\Helper\SettingsHelper'
        )
    ),
    
    'service_manager' => array(
        'factories' => array(
            'MvcTranslator' => 'Kofus\System\Mvc\Service\TranslatorServiceFactory',
            
            'Cache' => function ($sm)
            {
                if (! is_dir('data/cache'))
                    mkdir('data/cache', 0777);
                return new \Zend\Cache\Storage\Adapter\Filesystem(array(
                    'cache_dir' => 'data/cache',
                    'ttl' => 3600, // 1h
                    'key_pattern' => '/^[a-z0-9\.]*$/Di'
                ));
            },
            'SessionCache' => function ($sm)
            {
                $session = $sm->get('Zend\Session\SessionManager');
                return new \Zend\Cache\Storage\Adapter\Filesystem(array(
                    'cache_dir' => 'data/cache',
                    'namespace' => $session->getId(),
                    'ttl' => 3600, // 1h
                    'key_pattern' => '/^[a-z0-9\.]*$/Di'
                ));
            },
            /*
            'KofusLibService' => function ($sm)
            {
                return new \Kofus\System\Service\LibService($sm);
            } */
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        ),
        'invokables' => array(
            // Services
            'KofusDatabase' => 'Kofus\System\Service\DatabaseService',
            'KofusConfig' => 'Kofus\System\Service\ConfigService',
            'KofusConfigService' => 'Kofus\System\Service\ConfigService',
            'KofusLocale' => 'Kofus\System\Service\LocaleService',
        	'KofusLocaleService' => 'Kofus\System\Service\LocaleService',
            'KofusNodeService' => 'Kofus\System\Service\NodeService',
            'KofusFormService' => 'Kofus\System\Service\FormService',
            'KofusNavigationService' => 'Kofus\System\Service\NavigationService',
            'KofusTranslationService' => 'Kofus\System\Service\TranslationService',
            'KofusLinkService' => 'Kofus\System\Service\LinkService',
            'KofusLuceneService' => 'Kofus\System\Service\LuceneService',
            'KofusSettingsService' => 'Kofus\System\Service\SettingsService',
            'KofusSettings' => 'Kofus\System\Service\SettingsService',
            
            // Crons
        	'KofusBatchService' => 'Kofus\System\Service\BatchService',
            'KofusDbBackupCron' => 'Kofus\System\Cron\DbBackupCron',
            'KofusTestMailCron' => 'Kofus\System\Cron\TestMailCron',
            'KofusLuceneUpdateCron' => 'Kofus\System\Cron\LuceneUpdateCron',
            'KofusLuceneCron' => 'Kofus\System\Cron\LuceneCron',
            
            // Listeners
            'KofusErrorListener' => 'Kofus\System\Listener\ErrorListener',
            'KofusPublicFilesListener' => 'Kofus\System\Listener\PublicFilesListener',
            'KofusNodeListener' => 'Kofus\System\Listener\NodeListener',
            'KofusLuceneListener' => 'Kofus\System\Listener\LuceneListener',
            'KofusI18nListener' => 'Kofus\System\Listener\I18nListener',
            'KofusLayoutListener' => 'Kofus\System\Listener\LayoutListener',
            
            
        )
    ),
);
