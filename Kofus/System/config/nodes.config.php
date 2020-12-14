<?php
return array(
    'nodes' => array(
        'enabled' => array(
            'LANGUAGE',
            'COUNTRY',
            'TL'
        ),
        'available' => array(
            'T' => array(
                'label' => 'Tag',
                'label_pl' => 'Tags',
                'entity' => 'Kofus\System\Entity\TagEntity',
                'controllers' => array(
                    'Kofus\System\Controller\Tag'
                ),
                'search_documents' => array(
                    'Kofus\System\Search\Document\TagDocument'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\System\Form\Fieldset\Tag\MasterFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\Tag\MasterHydrator'
                            )
                        )
                    )
                ),
                'navigation' => array(
                    'list' => array(
                        'add' => array(
                            'label' => 'Add',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'icon' => 'glyphicon glyphicon-plus',
                            'params' => array(
                                'id' => 'T',
                            )
                        )
                    )
                )
            ),
            'TV' => array(
                'label' => 'Tag Vocabulary',
                'label_pl' => 'Tag Vocabularies',
                'entity' => 'Kofus\System\Entity\TagVocabularyEntity',
                'controllers' => array(
                    'Kofus\System\Controller\TagVocabulary'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\System\Form\Fieldset\TagVocabulary\MasterFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\TagVocabulary\MasterHydrator'
                            )
                        )
                    )
                ),
                'navigation' => array(
                    'list' => array(
                        
                        'add' => array(
                            'label' => 'Tag Vocabulary',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'icon' => 'glyphicon glyphicon-plus',
                            'params' => array(
                                'id' => 'TV'
                            )
                        ),
                    ),
                    'view' => array(
                        'edit' => array(
                            'label' => '',
                            'icon' => 'glyphicon glyphicon-pencil',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'edit',
                            'params' => array(
                                'id' => '{node_id}',
                            )
                        ),
                        'add' => array(
                            'label' => 'Tag',
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'icon' => 'glyphicon glyphicon-plus',
                            'params' => array(
                                'id' => 'T',
                            ),
                            'query' => array('vocabulary' => '{node_id}')
                        ),
                    )
                )
            ),
            
            'TL' => array(
                'label' => 'Translation',
                'label_pl' => 'Translations',
                'entity' => 'Kofus\System\Entity\TranslationEntity',
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\System\Form\Fieldset\Translation\MasterFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\Translation\MasterHydrator'
                            )
                        )
                    )
                )
            ),
            'C' => array(
                'label' => 'Content',
                'entity' => 'Kofus\System\Entity\ContentEntity',
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\System\Form\Fieldset\Content\MasterFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\Content\MasterHydrator'
                            )
                        )
                    )
                )
            ),
            'PG' => array(
                'label' => 'Page',
                'label_pl' => 'Pages',
                'entity' => 'Kofus\System\Entity\PageEntity',
                'controllers' => array(
                    'Kofus\System\Controller\Page'
                ),
                'links' => array(
                    'default' => '/{:language}/{:uriSegments}'
                ),
                'form' => array(
                    'default' => array(
                        'fieldsets' => array(
                            'master' => array(
                                'class' => 'Kofus\System\Form\Fieldset\Page\MasterFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\Page\MasterHydrator'
                            ),
                            'navigation' => array(
                                'class' => 'Kofus\System\Form\Fieldset\Page\NavigationFieldset',
                                'hydrator' => 'Kofus\System\Form\Hydrator\Page\NavigationHydrator'
                            )
                        )
                    )
                ),
                'navigation' => array(
                    'view' => array(),
                    'list' => array(
                        'add' => array(
                            'label' => 'Add',
                            'icon' => 'glyphicon glyphicon-plus',
                            
                            'route' => 'kofus_system',
                            'controller' => 'node',
                            'action' => 'add',
                            'params' => array(
                                'id' => 'PG'
                            )
                        )
                        
                        
                    )
                )
            ),
            'LANGUAGE' => array(
                'label' => 'Language',
                'search_documents' => array(
                    'Kofus\System\Search\Document\LanguageDocument'
                ),
                'values' => array(
                    'aa',
                    'ab',
                    'af',
                    'am',
                    'an',
                    'ar',
                    'as',
                    'ay',
                    'az',
                    'ba',
                    'be',
                    'bg',
                    'bh',
                    'bi',
                    'bn',
                    'bo',
                    'br',
                    'ca',
                    'co',
                    'cs',
                    'cy',
                    'da',
                    'de',
                    'dz',
                    'el',
                    'en',
                    'eo',
                    'es',
                    'et',
                    'eu',
                    'fa',
                    'fi',
                    'fj',
                    'fo',
                    'fr',
                    'fy',
                    'ga',
                    'gd',
                    'gl',
                    'gn',
                    'gu',
                    'gv',
                    'ha',
                    'he',
                    'hi',
                    'hr',
                    'ht',
                    'hu',
                    'hy',
                    'ia',
                    'ie',
                    'ii',
                    'ik',
                    'in',
                    'io',
                    'is',
                    'it',
                    'iu',
                    'ja',
                    'jv',
                    'ka',
                    'kk',
                    'kl',
                    'km',
                    'kn',
                    'ko',
                    'ks',
                    'ku',
                    'ky',
                    'la',
                    'li',
                    'ln',
                    'lo',
                    'lt',
                    'lv',
                    'mg',
                    'mi',
                    'mk',
                    'ml',
                    'mn',
                    'mo',
                    'mr',
                    'ms',
                    'mt',
                    'my',
                    'na',
                    'ne',
                    'nl',
                    'no',
                    'oc',
                    'om',
                    'or',
                    'pa',
                    'pl',
                    'ps',
                    'pt',
                    'qu',
                    'rm',
                    'rn',
                    'ro',
                    'ru',
                    'rw',
                    'sa',
                    'sd',
                    'sg',
                    'sh',
                    'si',
                    'sk',
                    'sl',
                    'sm',
                    'sn',
                    'so',
                    'sq',
                    'sr',
                    'ss',
                    'st',
                    'su',
                    'sv',
                    'sw',
                    'ta',
                    'te',
                    'tg',
                    'th',
                    'ti',
                    'tk',
                    'tl',
                    'tn',
                    'to',
                    'tr',
                    'ts',
                    'tt',
                    'tw',
                    'ug',
                    'uk',
                    'ur',
                    'uz',
                    'vi',
                    'vo',
                    'wa',
                    'wo',
                    'xh',
                    'yi',
                    'yo',
                    'zh',
                    'zu'
                )
            ),
            'COUNTRY' => array(
                'label' => 'Country',
                'search_documents' => array(
                    'Kofus\System\Search\Document\CountryDocument'
                ),
                'values' => array(
                    'AD',
                    'AE',
                    'AF',
                    'AG',
                    'AI',
                    'AL',
                    'AM',
                    'AN',
                    'AO',
                    'AQ',
                    'AR',
                    'AS',
                    'AT',
                    'AU',
                    'AW',
                    'AX',
                    'AZ',
                    'BA',
                    'BB',
                    'BD',
                    'BE',
                    'BF',
                    'BG',
                    'BH',
                    'BI',
                    'BJ',
                    'BL',
                    'BM',
                    'BN',
                    'BO',
                    'BQ',
                    'BR',
                    'BS',
                    'BT',
                    'BV',
                    'BW',
                    'BY',
                    'BZ',
                    'CA',
                    'CC',
                    'CD',
                    'CF',
                    'CG',
                    'CH',
                    'CI',
                    'CK',
                    'CL',
                    'CM',
                    'CN',
                    'CO',
                    'CR',
                    'CU',
                    'CV',
                    'CW',
                    'CX',
                    'CY',
                    'CZ',
                    'DE',
                    'DJ',
                    'DK',
                    'DM',
                    'DO',
                    'DZ',
                    'EC',
                    'EE',
                    'EG',
                    'EH',
                    'ER',
                    'ES',
                    'ET',
                    'FI',
                    'FJ',
                    'FK',
                    'FM',
                    'FO',
                    'FR',
                    'GA',
                    'GB',
                    'GD',
                    'GE',
                    'GF',
                    'GG',
                    'GH',
                    'GI',
                    'GL',
                    'GM',
                    'GN',
                    'GP',
                    'GQ',
                    'GR',
                    'GS',
                    'GT',
                    'GU',
                    'GW',
                    'GY',
                    'HK',
                    'HM',
                    'HN',
                    'HR',
                    'HT',
                    'HU',
                    'ID',
                    'IE',
                    'IL',
                    'IM',
                    'IN',
                    'IO',
                    'IQ',
                    'IR',
                    'IS',
                    'IT',
                    'JE',
                    'JM',
                    'JO',
                    'JP',
                    'KE',
                    'KG',
                    'KH',
                    'KI',
                    'KM',
                    'KN',
                    'KP',
                    'KR',
                    'KW',
                    'KY',
                    'KZ',
                    'LA',
                    'LB',
                    'LC',
                    'LI',
                    'LK',
                    'LR',
                    'LS',
                    'LT',
                    'LU',
                    'LV',
                    'LY',
                    'MA',
                    'MC',
                    'MD',
                    'ME',
                    'MF',
                    'MG',
                    'MH',
                    'MK',
                    'ML',
                    'MM',
                    'MN',
                    'MO',
                    'MP',
                    'MQ',
                    'MR',
                    'MS',
                    'MT',
                    'MU',
                    'MV',
                    'MW',
                    'MX',
                    'MY',
                    'MZ',
                    'NA',
                    'NC',
                    'NE',
                    'NF',
                    'NG',
                    'NI',
                    'NL',
                    'NO',
                    'NP',
                    'NR',
                    'NU',
                    'NZ',
                    'OM',
                    'PA',
                    'PE',
                    'PF',
                    'PG',
                    'PH',
                    'PK',
                    'PL',
                    'PM',
                    'PN',
                    'PR',
                    'PS',
                    'PT',
                    'PW',
                    'PY',
                    'QA',
                    'RE',
                    'RO',
                    'RS',
                    'RU',
                    'RW',
                    'SA',
                    'SB',
                    'SC',
                    'SD',
                    'SE',
                    'SG',
                    'SH',
                    'SI',
                    'SJ',
                    'SK',
                    'SL',
                    'SM',
                    'SN',
                    'SO',
                    'SR',
                    'SS',
                    'ST',
                    'SV',
                    'SX',
                    'SY',
                    'SZ',
                    'TC',
                    'TD',
                    'TF',
                    'TG',
                    'TH',
                    'TJ',
                    'TK',
                    'TL',
                    'TM',
                    'TN',
                    'TO',
                    'TR',
                    'TT',
                    'TV',
                    'TW',
                    'TZ',
                    'UA',
                    'UG',
                    'UM',
                    'US',
                    'UY',
                    'UZ',
                    'VA',
                    'VC',
                    'VE',
                    'VG',
                    'VI',
                    'VN',
                    'VU',
                    'WF',
                    'WS',
                    'YE',
                    'YT',
                    'ZA',
                    'ZM',
                    'ZW'
                )
            )
        
        )
    )

);