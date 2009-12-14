<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txsubsitewizardM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('tools', 'txsubsitewizardM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

$TCA['tx_subsitewizard_subsites'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:subsitewizard/locallang_db.xml:tx_subsitewizard_subsites',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_subsitewizard_subsites.gif',
	),
);
?>