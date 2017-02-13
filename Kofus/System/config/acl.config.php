<?php
return array(
    'user' => array(
        'acl' => array(
            'rules' => array(
                'allow' => array(
                    array(
                        null,
                        'Kofus\System\Controller\Cron',
                        'trigger'
                    )
                )
            )
        )
    )
);