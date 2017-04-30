<?php
namespace Kofus\Log;

return array(
    
    'controllers' => array(
        'invokables' => array()
    ),
    
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php'
            )
        )
    ),
    
    'router' => array(
        'routes' => array(
            'kofus_log' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/log[/:controller[/:action[/:id[/:id2]]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        '__NAMESPACE__' => 'Kofus\Log\Controller'
                    )
                ),
                'may_terminate' => true
            )
        )
    ),
    
    'service_manager' => array(
        'invokables' => array()
        // 'KofusMailerService' => 'Kofus\Mailer\Service\MailerService'
        
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'Log' => __DIR__ . '/../view'
        ),
        'controller_map' => array(
            'Kofus\Log' => true
        ),
        'module_layouts' => array(
            'Kofus\\Log' => 'kofus/layout/admin'
        )
    ),
    
    'view_helpers' => array(
        'invokables' => array()
        // 'mailer' => 'Kofus\Mailer\View\Helper\MailerHelper'
        
    ),
    
    'controller_plugins' => array(
        'invokables' => array()
        // 'mailer' => 'Kofus\Mailer\Controller\Plugin\MailerPlugin'
        
    )
)
;