<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_subsitewizard_subsites'] = array (
	'ctrl' => $TCA['tx_subsitewizard_subsites']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,title,parentpid,uplinkpid,contact,contactmail,contactphone,comment,kostenstelle,praesenzverantwortlicher,laufzeit'
	),
	'feInterface' => $TCA['tx_subsitewizard_subsites']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'parentpid' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.parentpid',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'uplinkpid' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.uplinkpid',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'contact' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.contact',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'contactmail' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.contactmail',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'contactphone' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.contactphone',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'comment' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.comment',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'kostenstelle' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.kostenstelle',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'praesenzverantwortlicher' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.praesenzverantwortlicher',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'laufzeit' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites.laufzeit',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, parentpid;;;;3-3-3, uplinkpid, contact, contactmail, contactphone, comment, kostenstelle, praesenzverantwortlicher, laufzeit')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>