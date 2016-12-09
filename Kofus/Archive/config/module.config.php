<?php

namespace Kofus\Archive;

return array (
		
		'controllers' => array (
				'invokables' => array (
						'Kofus\Archive\Controller\Mail' => 'Kofus\Archive\Controller\MailController',
						'Kofus\Archive\Controller\Http' => 'Kofus\Archive\Controller\HttpController',
						'Kofus\Archive\Controller\Soap' => 'Kofus\Archive\Controller\SoapController',
						'Kofus\Archive\Controller\Session' => 'Kofus\Archive\Controller\SessionController',
						'Kofus\Archive\Controller\Sql' => 'Kofus\Archive\Controller\SqlController',
						'Kofus\Archive\Controller\Lucene' => 'Kofus\Archive\Controller\LuceneController',
						'Kofus\Archive\Controller\Event' => 'Kofus\Archive\Controller\EventController' ,
						'Kofus\Archive\Controller\UriStack' => 'Kofus\Archive\Controller\UriStackController' ,
				) 
		),
		'user' => array (
				'acl' => array (
						'resources' => array (
								'Archive'
						)
				),
				'controller_mappings' => array (
						'Kofus\Archive\Controller\Http' => 'Archive',
						'Kofus\Archive\Controller\Soap' => 'Archive',
						'Kofus\Archive\Controller\Event' => 'Archive',
						'Kofus\Archive\Controller\Sql' => 'Archive',
						'Kofus\Archive\Controller\Lucene' => 'Archive',
						'Kofus\Archive\Controller\Session' => 'Archive',
						'Kofus\Archive\Controller\UriStack' => 'Frontend'
				)
		),		
		
		'listeners' => array (
				'EventsForArchive' 
		),
		
		'service_manager' => array (
				'invokables' => array (
						'KofusArchive' => 'Kofus\Archive\Service\ArchiveService',
						'EventsForArchive' => 'Kofus\Archive\Listener\EventListener' 
				)
				,
				'factories' => array (
						'KofusArchiveSqlLogger' => function ($sm) {
							return \Kofus\Archive\Sqlite\Table\Sql::getInstance('doctrine');
						} 
				) 
		),
		
		'router' => array (
				'routes' => array (
						'kofus_archive' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/archive/:controller/:action/:namespace[/:id]',
										'constraints' => array (
												'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'language' => '[a-z][a-z]' 
										),
										'defaults' => array (
												'language' => 'de',
												'__NAMESPACE__' => 'Kofus\Archive\Controller',
												'namespace' => 'default' 
										) 
								),
						),
				) 
		),
		
		'view_manager' => array (
				'template_path_stack' => array (
						'Archive' => __DIR__ . '/../view' 
				),
				'controller_map' => array (
						'Kofus\Archive' => true 
				),
				'module_layouts' => array (
						'Kofus\\Archive' => 'kofus/layout/admin' 
				) 
		),
		
		'view_helpers' => array (
				'invokables' => array (
						'archive' => 'Kofus\Archive\View\Helper\ArchiveHelper', 
						'urlBack' => 'Kofus\Archive\View\Helper\UrlBackHelper', 
				) 
		),
		
		'controller_plugins' => array (
				'invokables' => array (
						'archive' => 'Kofus\Archive\Controller\Plugin\ArchivePlugin' 
				) 
		),
		
		'translator' => array (
				'translation_file_patterns' => array (
						array (
								'type' => 'phpArray',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.php' 
						) 
				) 
		),
		

		
		'navigation' => array (
				'admin' => array (
						'archive' => array (
								'label' => 'Archive',
								'uri' => '#',
								'pages' => array (
										'archive_sent' => array (
												'label' => 'Mails',
												'resource' => 'MTMPL',
												'privilege' => 'administer',
												'route' => 'kofus_archive',
												'controller' => 'mail',
												'action' => 'list' 
										),
										'sessions' => array (
												'label' => 'Sessions',
												'route' => 'kofus_archive',
												'controller' => 'session',
												'action' => 'list' 
										),
										'events' => array (
												'label' => 'Events',
												'route' => 'kofus_archive',
												'controller' => 'event',
												'action' => 'list' 
										),
										'sql' => array (
												'label' => 'Doctrine SQL',
												'route' => 'kofus_archive',
												'controller' => 'sql',
												'action' => 'list',
												'params' => array('namespace' => 'doctrine')
										),
										'lucene' => array (
												'label' => 'Lucene',
												'route' => 'kofus_archive',
												'controller' => 'lucene',
												'action' => 'list',

										),
										
								)
								 
						) 
				) 
		) 
);