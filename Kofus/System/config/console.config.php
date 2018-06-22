<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'lucene' => array(
                    'options' => array(
                        'route' => 'lucene rebuild [<node_type>]',
                        'help_text' => 'Rebuild search index for all nodes',
                        'defaults' => array(
                            'action' => 'rebuildLuceneIndex',
                            'controller' => 'console',
                            '__NAMESPACE__' => 'Kofus\System\Controller'
                        ),
                    )
                ),
                'optimize' => array(
                    'options' => array(
                        'route' => 'optimize',
                        'help_text' => 'Delete detached records and files',
                        'defaults' => array(
                            'action' => 'optimize',
                            'controller' => 'console',
                            '__NAMESPACE__' => 'Kofus\System\Controller'
                        ),
                    )
                ),
                'database-upgrade' => array(
                    'options' => array(
                        'route' => 'database upgrade',
                        'help_text' => 'Rebuild database structure using Doctrine entities',
                        'defaults' => array(
                            'action' => 'database-upgrade',
                            'controller' => 'console',
                            '__NAMESPACE__' => 'Kofus\System\Controller'
                        ),
                    )
                ),
                'database-backup' => array(
                    'options' => array(
                        'route' => 'database backup',
                        'help_text' => 'Rebuild database structure using Doctrine entities',
                        'defaults' => array(
                            'action' => 'database-backup',
                            'controller' => 'console',
                            '__NAMESPACE__' => 'Kofus\System\Controller'
                        ),
                    )
                ),
                
                'run-batch' => array(
                    'options' => array(
                        'route' => 'batch <batch>',
                        'help_text' => 'Run any batch script by its ZF2 service name.',
                        'defaults' => array(
                            'action' => 'runBatch',
                            'controller' => 'console',
                            '__NAMESPACE__' => 'Kofus\System\Controller'
                        ),
                    )
                ),
            )
        )
    )
);