<?php
return array(
    'navbars' => array(
        'admin' => array(
            'title' => 'Administration',
            'system' => true
        ),
        'locales' => array(
            'title' => 'Locales',
            'system' => true
        )
    ),
    
    'navigation' => array(
        'admin' => array( /*
            'system' => array(
                'label' => 'System',
                'enabled' => false,
                'uri' => '#',
                'resource' => 'System',
                'privilege' => 'administer',
                'pages' => array(
                    'db_upgrade' => array(
                        'label' => 'Rebuild Database',
                        'route' => 'kofus_system',
                        'controller' => 'database',
                        'action' => 'upgrade',
                        'resource' => 'System',
                        'privilege' => 'administer'
                    ),
                    'links_rebuild' => array(
                        'label' => 'Rebuild Links',
                        'resource' => 'System',
                        'privilege' => 'administer',
                        'route' => 'kofus_system',
                        'controller' => 'node',
                        'action' => 'rebuildlinks'
                    ),
                    'lucene' => array(
                        'label' => 'Search index',
                        'resource' => 'System',
                        'privilege' => 'administer',
                        'route' => 'kofus_system',
                        'controller' => 'search',
                        'action' => 'index'
                    )
                    ,
                    'cron' => array(
                        'label' => 'Cron',
                        'resource' => 'System',
                        'privilege' => 'administer',
                        'route' => 'kofus_system',
                        'controller' => 'cron-scheduler',
                        'action' => 'list'
                    )
                    
                )
                
            ),
            /*
            'cms' => array(
                'label' => 'CMS',
                'uri' => '#',
                'pages' => array(
                    'page' => array(
                        'label' => 'Pages',
                        'resource' => 'PG',
                        'privilege' => 'administer',
                        'route' => 'kofus_system',
                        'controller' => 'page',
                        'action' => 'list'
                    )
                )
            ) */
        )
    )
)
;
