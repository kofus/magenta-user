<?php
namespace Kofus\User;

include_once __DIR__ . '/../../framework.config.php';

use Zend\Permissions\Acl\Role\GenericRole as Role;

return array(
    'controllers' => array(
        'invokables' => array(
            'Kofus\User\Controller\Auth' => 'Kofus\User\Controller\AuthController',
            'Kofus\User\Controller\Account' => 'Kofus\User\Controller\AccountController',
            'Kofus\User\Controller\Acl' => 'Kofus\User\Controller\AclController'
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
    ),
    
    
    'nodes' => array(
        'available' => array(
            'U' => array(
                'label' => 'User Account',
                'entity' => 'Kofus\User\Entity\AccountEntity',
                'controllers' => array('Kofus\User\Controller\Account', 'Kofus\User\Controller\Acl'),
                'search_documents' => array('Kofus\User\Search\Document\AccountDocument'),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\User\Form\Fieldset\UserAccountFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\UserAccountHydrator'
                            )
                        )
                    )
                ),
            	'navigation' => array(
                    	'search_result' => array(
            				'default' => array('route' => 'kofus_system', 'controller' => 'node', 'action' => 'edit', 'id' => '{node_id}')
            			)
                    ),
                'actions' => array(
                    'delete' => array(
                        'factories' => array(
                            'delete_account' => function ($sm, $node)
                            {
                                $service = $sm->get('KofusUser');
                                $service->deleteAccount($node);
                            }
                        )
                        
                    )
                    
                )
            )
            ,
            'AUTH' => array(
                'label' => 'Authentication',
                'entity' => 'Kofus\User\Entity\AuthEntity',
                'controllers' => array('Kofus\User\Controller\Auth'),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\User\Form\Fieldset\Auth\MasterFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\Auth\MasterHydrator'
                            )
                        )
                    )
                )
            )
        )
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
    
    'service_manager' => array(
        'invokables' => array(
            'KofusUserService' => 'Kofus\User\Service\UserService',
            'KofusAclService' => 'Kofus\User\Service\AclService',
            'KofusAclListener' => 'Kofus\User\Listener\AclListener'
        )
    ),
    
    'router' => array(
        'routes' => array(
            'kofus_user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/user/:controller/:action[/:id[/:id2]]',
                    'constraints' => array(
                        // 'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        // 'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        '__NAMESPACE__' => 'Kofus\User\Controller'
                    )
                ),
                'may_terminate' => true
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'User' => __DIR__ . '/../view'
        ),
        'controller_map' => array(
            'Kofus\User' => true
        ),
        'module_layouts' => array(
            'Kofus\\User' => 'kofus/layout/admin'
        )
    )
    ,
    
    'view_helpers' => array(
        'invokables' => array(
            'user' => 'Kofus\User\View\Helper\UserHelper'
        )
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'user' => 'Kofus\User\Controller\Plugin\UserPlugin'
        )
    ),
    
    'navigation' => array(
        'admin' => array(
            'user' => array(
                'label' => 'Benutzer',
                'resource' => 'U',
                'uri' => '#',
                'pages' => array(
                    'account' => array(
                        'label' => 'Benutzerkonten',
                    	'resource' => 'U',
                        'privilege' => 'administer',
                        'route' => 'kofus_user',
                        'controller' => 'account',
                        'action' => 'list'
                    ),
                    'acl' => array(
                        'label' => 'Privileges',
                    	'resource' => 'U',
                        'privilege' => 'administer',
                        'route' => 'kofus_user',
                        'controller' => 'acl',
                        'action' => 'index'
                    )
                )
                
            )
            
        )
    ),
    
);