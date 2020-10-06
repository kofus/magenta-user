<?php
return array(
    'nodes' => array(
        'available' => array(
            'UR' => array(
                'label' => 'Role',
                'label_pl' => 'Roles',
                'entity' => 'Kofus\User\Entity\RoleEntity',
                'controllers' => array(
                    'Kofus\User\Controller\Role'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\User\Form\Fieldset\Role\MasterFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\Role\MasterHydrator'
                            )
                        )
                    )
                ),
                'navigation' => array(
                    'list' => array(
                        array(
                            'label' => 'Add',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'icon' => 'glyphicon glyphicon-plus',
                            'params' => array(
                                'id' => 'UR'
                            )
                        )
                    )
                ),
            ),
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
                                'class' => 'Kofus\User\Form\Fieldset\Account\MasterFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\Account\MasterHydrator'
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
            ),
            'AUTHLOGIN' => array(
                'label' => 'Login Authentication',
                'label_pl' => 'Login Authentications',
                'entity' => 'Kofus\User\Entity\AuthLoginEntity',
                'controllers' => array(
                    'Kofus\User\Controller\Auth'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'login' => array(
                                'class' => 'Kofus\User\Form\Fieldset\Auth\LoginFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\Auth\LoginHydrator'
                            )
                        )
                    )
                )
            ),
            'AUTHPASS' => array(
                'label' => 'Passphrase Authentication',
                'label_pl' => 'Passphrase Authentications',
                'entity' => 'Kofus\User\Entity\AuthPassphraseEntity',
                'controllers' => array(
                    'Kofus\User\Controller\Auth'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\User\Form\Fieldset\Auth\PassphraseFieldset',
                                'hydrator' => 'Kofus\User\Form\Hydrator\Auth\PassphraseHydrator'
                            )
                        )
                    )
                )
            ),
            'AUTH' => array(
                'label' => 'Authentication',
                'entity' => 'Kofus\User\Entity\AuthEntity'
            )
            
        )
    )
)
;