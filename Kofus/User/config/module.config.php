<?php
namespace Kofus\User;


return array(
    'controllers' => array(
        'invokables' => array(
            'Kofus\User\Controller\Auth' => 'Kofus\User\Controller\AuthController',
            'Kofus\User\Controller\Account' => 'Kofus\User\Controller\AccountController',
            'Kofus\User\Controller\Acl' => 'Kofus\User\Controller\AclController',
            'Kofus\User\Controller\Autologout' => 'Kofus\User\Controller\AutologoutController',
            'Kofus\User\Controller\Role' => 'Kofus\User\Controller\RoleController',
            
        )
    ),
    'user' => array(
        'controller_mappings' => array(
            'Kofus\User\Controller\Autologout' => 'Frontend'
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
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/user/:controller[/:action[/:id[/:id2]]]',
                    'constraints' => array(
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        'action' => 'index',
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
    
    
    
);