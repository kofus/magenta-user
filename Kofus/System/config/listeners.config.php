<?php
return array(
    'listeners' => array(
        'KofusErrorListener',
        'KofusLayoutListener',
        'KofusPublicFilesListener',
        'KofusI18nListener'
    ),
    
    'service_manager' => array(
        'invokables' => array(

            'KofusLayoutListener' => 'Kofus\System\Listener\LayoutListener',
            'KofusErrorListener' => 'Kofus\System\Listener\ErrorListener',
            'KofusNodeListener' => 'Kofus\System\Listener\NodeListener',
            'KofusPublicFilesListener' => 'Kofus\System\Listener\PublicFilesListener',
            'KofusI18nListener' => 'Kofus\System\Listener\I18nListener'
        )
        
    )
);