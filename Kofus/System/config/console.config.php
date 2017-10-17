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
            )
        )
    )
);