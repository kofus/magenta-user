<?php
return array(
    'nodes' => array(
        'available' => array(
            'U' => array(
                'label' => 'User Account',
                'label_pl' => 'User Accounts',
                'entity' => 'Kofus\User\Entity\AccountEntity',
                'controllers' => array(
                    'Kofus\User\Controller\Account',
                    'Kofus\User\Controller\Acl'
                ),
                'search_documents' => array(
                    'Kofus\User\Search\Document\AccountDocument'
                ),
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
                        'default' => array(
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'edit',
                            'id' => '{node_id}'
                        )
                    ),
                    'list' => array(
                        array(
                            'label' => 'Add',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'icon' => 'glyphicon glyphicon-plus',
                            'params' => array(
                                'id' => 'U'
                            )
                        )
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
                
            ),
            'AUTH' => array(
                'label' => 'Authentication',
                'entity' => 'Kofus\User\Entity\AuthEntity',
                'controllers' => array(
                    'Kofus\User\Controller\Auth'
                ),
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
    )
)
;