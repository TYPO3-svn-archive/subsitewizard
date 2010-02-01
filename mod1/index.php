<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Steffen Kamper <info@sk-typo3.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

$LANG->includeLLFile('EXT:subsitewizard/mod1/locallang.xml');
require_once (PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF, 1); // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]


/**
 * Module 'Subsitewizard' for the 'subsitewizard' extension.
 *
 * @author	Steffen Kamper <info@sk-typo3.de>
 * @package	TYPO3
 * @subpackage	tx_subsitewizard
 */
class tx_subsitewizard_module1 extends t3lib_SCbase {
	var $pageinfo;

	protected $extConf;
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init() {
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		$this->extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['subsitewizard']);
		parent::init();

	/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig() {
		global $LANG;
		$this->MOD_MENU = array (
			'function' => array (
				'1' => $LANG->getLL('menu_create'),
				'2' => $LANG->getLL('menu_list'),
				'3' => $LANG->getLL('menu_conf'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main() {
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		// initialize doc
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->setModuleTemplate(t3lib_extMgm::extPath('subsitewizard') . 'mod1//mod_template.html');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->getContextMenuCode();
		$this->doc->inDocStyles = '

		';
		$docHeaderButtons = $this->getButtons();

		if (($this->id && $access) || ($BE_USER->user['admin'] && ! $this->id)) {

			// Draw the form
			$this->doc->form = '<form action="" method="post" enctype="multipart/form-data" name="sswizardform" id="sswizardform">';

			// JavaScript
			$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
								var browserWin="";


						function setFormValueFromBrowseWin(fName,value,label,exclusiveValues)	{	//
							//alert(fName+"\n"+value+"\n"+label+"\n"+exclusiveValues);
							el = value.split("_");
							id = el[el.length - 1];
							fObj=document.getElementById("sswizardform");
							fObj[fName].value = id;
						}

							</script>
						';
			$this->doc->postCode = '
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

			$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('subsitewizard') . 'mod1/subsitewizard.css';

			// Render content:
			$this->moduleContent();
		} else {
			// If no access or if ID == zero
			$docHeaderButtons['save'] = '';
			$this->content .= $this->doc->spacer(10);
		}

		// compile document
		$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
		$markers['CONTENT'] = $this->content;

		// Build the <body> for the module
		$this->content = $this->doc->startPage($LANG->getLL('title'));
		$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		$this->content .= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);

	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent() {

		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent() {
		switch ((string) $this->MOD_SETTINGS['function']) {
			case 1 :
				$content = $this->getSubsiteWizardForm();
				$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('ssnew'), $content, 0, 1);
			break;
			case 2 :
				$content = $this->getSubsitesTable();
				$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('subsites'), $content, 0, 1);
				break;
			case 3 :
				$content = $this->getConfiguration();
				$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('configuration'), $content, 0, 1);
				break;

		}
	}

	/**
	 * Hets the form
	 */
	protected function getSubsiteWizardForm() {
		$post = t3lib_div::_POST('data');
		$form = '';
		$this->validated = FALSE;

		if ($post['sscreatesubsite']) {
			$form = $this->processSubmit();
		}
		if ($this->validated) {
			return $form;
		}

		$form .= '
			<fieldset class="subsitewizard">
				<legend>' . $GLOBALS['LANG']->getLL('legend.general') . '</legend>
				<p>
					<label for="sstitle">*' . $GLOBALS['LANG']->getLL('sstitle') . ':</label>
					<input type="text" id="sstitle" name="data[sstitle]" value="' . htmlspecialchars($post['sstitle']) . '" size="60" />
				</p>
				<p>
					<label for="ssptitle">*' . $GLOBALS['LANG']->getLL('pagetitle') . ':</label>
					<input type="text" id="ssptitle" name="data[ssptitle]" value="' . htmlspecialchars($post['ssptitle']) . '" size="60" />
				</p>
				<p>
					<label for="ssnavtitle">*' . $GLOBALS['LANG']->getLL('navtitle') . ':</label>
					<input type="text" id="ssnavtitle" name="data[ssnavtitle]" value="' . htmlspecialchars($post['ssnavtitle']) . '" size="60" />
				</p>
				<p>
					<label for="ssparentpid">*' . $GLOBALS['LANG']->getLL('parentpid') . ':</label>
					<input type="text" id="ssparentpid" name="data[ssparentpid]" value="' . htmlspecialchars($post['ssparentpid']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('ssparentpid', 'page', 'pages', 'db') . '
				</p>
				<p>
					<label for="ssuplinkpid">*' . $GLOBALS['LANG']->getLL('uplinkpid') . ':</label>
					<input type="text" id="ssuplinkpid" name="data[ssuplinkpid]" value="' . htmlspecialchars($post['ssuplinkpid']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('ssuplinkpid', 'page', 'pages', 'db') . '
				</p>
				<p>
					<label for="ssheaderimg">' . $GLOBALS['LANG']->getLL('ssheaderimg') . ':</label>
					<input type="file" id="ssheaderimg" name="data[ssheaderimg]" value="' . htmlspecialchars($post['ssheaderimg']) . '" size="60" />&nbsp;' . $this->browseLinksIcon('ssheaderimg', 'file', 'gif,jpg,jpeg,tif,bmp,pcx,tga,png') . '
				</p>
				<p>
					<label for="firstbeuser">' . $GLOBALS['LANG']->getLL('ssfirstuser') . ':</label>
					<input type="text" id="firstbeuser" name="data[firstbeuser]" value="' . htmlspecialchars($post['firstbeuser']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('firstbeuser', 'page', 'be_users', 'db') . '
				</p>
			</fieldset>
			<fieldset class="subsitewizard">
				<legend>' . $GLOBALS['LANG']->getLL('legend.contact') . '</legend>
				<p>
					<label for="sscontact">' . $GLOBALS['LANG']->getLL('contact.name') . ':</label>
					<input type="text" id="sscontact" name="data[sscontact]" value="' . htmlspecialchars($post['sscontact']) . '" size="60" />
				</p>
				<p>
					<label for="sscontactmail">' . $GLOBALS['LANG']->getLL('contact.email') . ':</label>
					<input type="text" id="sscontactmail" name="data[sscontactmail]" value="' . htmlspecialchars($post['sscontactmail']) . '" size="60" />
				</p>
				<p>
					<label for="sscontactphone">' . $GLOBALS['LANG']->getLL('contact.phone') . ':</label>
					<input type="text" id="sscontactphone" name="data[sscontactphone]" value="' . htmlspecialchars($post['sscontactphone']) . '" size="60" />
				</p>
				<p>
					<label for="sscomment">' . $GLOBALS['LANG']->getLL('comment') . ':</label>
					<textarea id="sscomment" name="data[sscomment]" rows="7" cols="60">' . htmlspecialchars($post['sscomment']) . '</textarea>
				</p>

			</fieldset>
			<fieldset class="subsitewizard">
				<legend>' . $GLOBALS['LANG']->getLL('legend.presence') . '</legend>
				<p>
					<label for="ssverantwortlicher">' . $GLOBALS['LANG']->getLL('presenceleader') . ':</label>
					<input type="text" id="ssverantwortlicher" name="data[ssverantwortlicher]" value="' . htmlspecialchars($post['ssverantwortlicher']) . '" size="60" />
				</p>
				<p>
					<label for="sslaufzeit">' . $GLOBALS['LANG']->getLL('runtime') . ':</label>
					<input type="text" id="sslaufzeit" name="data[sslaufzeit]" value="' . htmlspecialchars($post['sslaufzeit']) . '" size="60" />
				</p>
				<p>
					<label for="sskostenstelle">' . $GLOBALS['LANG']->getLL('accounts') . ':</label>
					<input type="text" id="sskostenstelle" name="data[sskostenstelle]" value="' . htmlspecialchars($post['sskostenstelle']) . '" size="60" />
				</p>
			</fieldset>
			<p>
				<input type="submit" name="data[sscreatesubsite]" id="sscreatesubsite" value="' . $GLOBALS['LANG']->getLL('sssubmit') . '" />
			</p>
		';

		return $form;
	}

	protected function processSubmit() {
		$post = t3lib_div::_POST('data');
		$data = $cmd = array ();
		$this->validated = FALSE;
		$errors = 0;

		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values = 0;
		$tce->reverseOrder = 1;
		$tce->copyTree = 10;

		// set default TCA values specific for the user
		$TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
		if (is_array($TCAdefaultOverride)) {
			$tce->setDefaultsFromUserTS($TCAdefaultOverride);
		}

		#$out .= t3lib_div::view_array($post);

		// validate
		if (!$post['sstitle']) {
			$errors ++;
			$errMsg[] = $GLOBALS['LANG']->getLL('errors.notitle');
		}
		if (!$post['ssptitle']) {
			$errors ++;
			$errMsg[] = $GLOBALS['LANG']->getLL('errors.nopagetitle');
		}
		if (!$post['ssnavtitle']) {
			$errors ++;
			$errMsg[] = $GLOBALS['LANG']->getLL('errors.nonavtitle');
		}
		if (!intval($post['ssparentpid'])) {
			$errors ++;
			$errMsg[] = $GLOBALS['LANG']->getLL('errors.noparentpid');
		}
		if (!intval($post['ssuplinkpid'])) {
			$errors ++;
			$errMsg[] = $GLOBALS['LANG']->getLL('errors.nouplinkpid');
		}

		//copy tree
		$cmd['pages'][$this->extConf['templPid']]['copy'] = intval($post['ssparentpid']);







		if ($errors === 0) {
			$this->validated = TRUE;
		} else {
			$out .= '<div class="sserror">
			<div class="typo3-message message-error">
				<div class="header-container">
					<div class="message-header message-left">' . $GLOBALS['LANG']->getLL('errors.submiterror') . '</div>
				</div>
				<div class="message-body">' . implode('<br />', $errMsg) . '</div>
			</div>
			</div>';
		}

		if ($this->validated) {
			// Real Data Creation

			$tce->start(array(), $cmd);
			$tce->process_cmdmap();

			$createdPages = $tce->copyMappingArray_merged['pages'];
				//reusable information
			$pageID = $createdPages[$this->extConf['templPid']];
			$alias = htmlspecialchars($post['ssnavtitle']);


			// Create Subsite Record
			$data['tx_subsitewizard_subsites']['NEW0'] = array (
				'pid' => 0,
				'title' => htmlspecialchars($post['sstitle']),
				'alias' => $alias,
				'parentpid' => trim(htmlspecialchars($post['ssparentpid'])),
				'startpid' => $pageID,
				'uplinkpid' => trim(htmlspecialchars($post['ssuplinkpid'])),
				'contact' => htmlspecialchars($post['sscontact']),
				'contactmail' => htmlspecialchars($post['sscontactmail']),
				'contactphone' => htmlspecialchars($post['sscontactphone']),
				'comment' => htmlspecialchars($post['sscomment']),
				'kostenstelle' => htmlspecialchars($post['sskostenstelle']),
				'praesenzverantwortlicher' => htmlspecialchars($post['ssverantwortlicher']),
				'laufzeit' => htmlspecialchars($post['sslaufzeit']),
				'headerimage' => htmlspecialchars($post['ssheaderimg']),
			);

				//create folders
			t3lib_div::mkdir_deep(PATH_site, 'fileadmin/media/' . $alias . '/');
			t3lib_div::mkdir_deep(PATH_site, 'fileadmin/public/' . $alias . '/');
			t3lib_div::mkdir_deep(PATH_site, 'fileadmin/secure/' . $alias . '/');

				//create filemounts
			$data['sys_filemounts']['NEW1'] = array (
				'pid' => 0,
				'base' => 1,
				'title' => $alias . ' (media ' . $pageID . ')',
				'path' => 'media/' . $alias,
			);
/*			$data['sys_filemounts']['NEW2'] = array (
				'pid' => 0,
				'base' => 1,
				'title' => $alias . ' (public ' . $pageID . ')',
				'path' => 'public/' . $alias,
			);
*/			$data['sys_filemounts']['NEW2'] = array (
				'pid' => 0,
				'base' => 1,
				'title' => $alias . ' (secure ' . $pageID . ')',
				'path' => 'secure/' . $alias,
			);

				// create beuser group
			$data['be_groups']['NEW3'] = array(
				'pid' => 0,
				'title' => $alias . ' (' . $pageID . ')',
				'subgroup' => intval($this->extConf['userSubGroup']),
				'db_mountpoints' => $pageID,
				'file_mountpoints' =>  'NEW1,NEW2',
			);

				// add usergroup to beuser
				//get userrecord
			$beuser = t3lib_BEfunc::getRecord('be_users', intval($post['firstbeuser']));
			if (is_array($beuser)) {
				$data['be_users'][$beuser['uid']] = array(
					'usergroup' =>  $beuser['usergroup']. ($beuser['usergroup'] ? ',' : '') . 'NEW3',
				);
			}

				// Get the mapping
			$temp = $GLOBALS['BE_USER']->getTSConfig('mod.subsitewizard');
			$modConf = isset($temp['properties']['pageMapping.']) ? $temp['properties']['pageMapping.'] : array();

			$defaultMapping = array(
				'title' => 'title',
				'subtitle' => 'subtitle',
				'navtitle' => 'alias',
				'media' => 'headerimage',
				'author' => 'contact',
				'author_email' => 'contactmail'
			);
			$mapping = array_merge($defaultMapping, $modConf);

			$data['pages'][$pageID] = array (
				'pid' => trim(htmlspecialchars($post['ssparentpid'])),
				'hidden' => 0,
			);

			foreach ($mapping as $key => $value) {
				$data['pages'][$pageID][$key] = $data['tx_subsitewizard_subsites']['NEW0'][$value];
			}

			$tce->start($data, array());
			$tce->process_datamap();

				// adjust unreplaced values
			$data = array();
			$newDBGroup = intval($tce->substNEWwithIDs['NEW3']);

				// Access
			if (!$this->extConf['useACL']) {
				$data['pages'][$pageID]['TSconfig'] = 'TCEMAIN.permissions.groupid = ' . $newDBGroup;
				foreach ($createdPages as $pageUid) {
					$data['pages'][$pageUid]['perms_userid'] = $beuser['uid'];
					$data['pages'][$pageUid]['perms_groupid'] = $newDBGroup;
					$data['pages'][$pageUid]['perms_user'] = 31;
					$data['pages'][$pageUid]['perms_group'] = 31;
				}
			}

			if ($this->extConf['useACL']) {
				$data['tx_beacl_acl']['NEW4'] = array(
					'pid' => $pageID,
					'permissions' => 31,
					'recursive' => 1,
					'object_id' => $newDBGroup,
					'type' => 1
				);
			}debug($data);
			$tce->start($data, array());
			$tce->process_datamap();

				//finished
			$out =  '<h4>' . $GLOBALS['LANG']->getLL('wizard.created') . '</h4>';
			$onClick = "top.loadEditId(" . $pageID . ");top.fsMod.recentIds['web']=" . $pageID . ";";
			$out .= '<p><a href="#" onclick="' . htmlspecialchars($onClick) . '">' . $GLOBALS['LANG']->getLL('wizard.gotopage') . '</a></p>';

				// update Pagetree
			t3lib_BEfunc::setUpdateSignal('updatePageTree');
		}

		return $out;
	}

	/**
	 *
	 */
	protected function getSubsitesTable() {
		$tableLayout = array (
			'table' => array (
				'<table border="0" cellspacing="1" cellpadding="2" style="width:auto;" id="typo3-filelist">', '</table>'),
				'0' => array (
					'tr' => array (
						'<tr class="c-headLine" valign="top">', '</tr>'
					),
					'defCol' => array ('<td class="cell">', '</td>')
				),
				'defRowOdd' => array (
					'tr' => array ('<tr class="bgColor6">', '</tr>'),
					'defCol' => array ('<td class="cell">', '</td>')
				),
				'defRowEven' => array (
					'tr' => array ('<tr class="bgColor4">', '</tr>'),
					'defCol' => array ('<td class="cell">', '</td>')
				),

		);
		$table = array ();
		$tr = 0;

		// Header row
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.title');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.alias');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.pids');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.headerimage');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.contact');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.comment');
		$table[$tr][] = $GLOBALS['LANG']->getLL('subsites.info');
		$tr ++;


		$subSites = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_subsitewizard_subsites', 'deleted=0', '', 'title');

		foreach ($subSites as $subSite) {
			$found = is_file($subSite['logfile']);
			$foundIcon = '<img' . t3lib_iconWorks::skinImg($this->backPath, 'gfx/' . ($found ? 'ok.png' : 'error.png')) . ' title="' . ($found ? $GLOBALS['LANG']->getLL('logfile.filefound') : $GLOBALS['LANG']->getLL('logfile.filenotfound')) . '" />';
			$params = '&edit[tx_subsitewizard_subsites][' . $subSite['uid'] . ']=edit';

			$table[$tr][] = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::editOnClick($params, $this->doc->backPath)) . '">
				<img' . t3lib_iconWorks::skinImg($this->backPath, 'gfx/edit2.gif') . ' title="' . $GLOBALS['LANG']->getLL('editRecord', 1) . '" alt="" />
			</a>';

			$table[$tr][] = '<strong>' . t3lib_BEfunc::getRecordTitle('tx_subsitewizard_subsites', $subSite) . '</strong>';
			$table[$tr][] = '<strong class="alias">' . htmlspecialchars($subSite['alias']) . '</strong>';

			$table[$tr][] = $this->pidCell($subSite['startpid']) .
				'<br />' .
				$this->pidCell($subSite['parentpid']) .
				'<br />' .
				$this->pidCell($subSite['uplinkpid']);

				$table[$tr][] = htmlspecialchars($subSite['headerimage']);
			$table[$tr][] = htmlspecialchars($subSite['contact']) . '<br />' .
				'<a href="mailto:' . $subSite['contactmail'] . '">' . htmlspecialchars($subSite['contactmail']) . '</a><br />' .
				'<span class="phone">' . htmlspecialchars($subSite['contactphone']) . '</span>';

			$table[$tr][] = '<div title="' . htmlspecialchars($subSite['comment']) . '">' . nl2br(htmlspecialchars(t3lib_div::fixed_lgd_cs($subSite['comment'], 50))) . '</div>';

			$table[$tr][] = htmlspecialchars($subSite['praesenzverantwortlicher']) . '<br />' . htmlspecialchars($subSite['kostenstelle']) . '<br />' . htmlspecialchars($subSite['laufzeit']);
			$tr ++;
		}


		return $this->doc->table($table, $tableLayout);
	}

	protected function getConfiguration() {
		$temp = $GLOBALS['BE_USER']->getTSConfig('mod.subsitewizard');
		$modConf = isset($temp['properties']) ? $temp['properties'] : array();
		$defaultMapping = array(
			'title' => 'title',
			'subtitle' => 'subtitle',
			'navtitle' => 'alias',
			'media' => 'headerimage',
			'author' => 'contact',
			'author_email' => 'contactmail'
		);
		$modConf['pageMapping.'] = array_merge($defaultMapping, $modConf['pageMapping.']);

		$out = '<p>
		For the mapping use pagerecordfield = subsitewiazardfield.<br />Following fields are available from subsitewizard:<br /><br />
		title,alias,parentpid,startpid,uplinkpid,contact,contactmail,contactphone,comment,kostenstelle,praesenzverantwortlicher,laufzeit,headerimage
		<br /><br /></p>';
		$out .=  t3lib_div::view_array(array_merge($this->extConf, $modConf));
		return $out;
	}

	protected function pidCell($pid) {

		$pageRecord = t3lib_BEfunc::getRecord('pages', intval($pid));
		if (!is_array($pageRecord)) {
			return '';
		}
		$view = $this->doc->viewPageIcon($pid, $this->doc->backPath);
		$alttext = t3lib_BEfunc::getRecordIconAltText($pageRecord, 'pages');
		$iconImg = t3lib_iconWorks::getIconImage('pages', $pageRecord, $this->backPath, 'title="'. htmlspecialchars($alttext) . '"');
			// Make Icon:
		$theIcon = $this->doc->wrapClickMenuOnIcon($iconImg, 'pages', $pageRecord['uid']);
		$title = t3lib_BEfunc::getRecordTitle('pages', $pageRecord);
		$pageInfo = $view . $theIcon . $title . ' <em>[pid: ' . $pid . ']</em>';
		return $pageInfo;
	}


	protected function browseLinksIcon($fieldName, $tabs = "page", $bParams = '', $mode = 'wizard') {
		$uid = uniqid('popUpID');
		$allTabs = 'page,file,folder,url,mail,spec';
		$blindLinkOptions = t3lib_div::rmFromList($tabs, $allTabs);
		if ($bParams) {
			$bp = '&bparams=' . $fieldName . '|||' . $bParams . '|';
		}
		$aOnClick = 'this.blur(); vHWin=window.open(\'../../../../typo3/browse_links.php?mode=' . $mode . $bp . '&P[field]=' . $fieldName . '&P[formName]=sswizardform&P[itemName]=' . $fieldName . '&P[params][blindLinkOptions]=' . $blindLinkOptions . '&P[fieldChangeFunc][typo3form.fieldGet]=null&P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=null\',\'' . $uid . '\',\'height=500,width=800,status=0,menubar=0,scrollbars=1\'); vHWin.focus(); return false;';
		$icon = '<a href="#" onclick="' . htmlspecialchars($aOnClick) . '">' . '<img' . t3lib_iconWorks::skinImg($this->backPath, 'gfx/insert3.gif', 'width="14" height="14"') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib('PID') . ' />' . '</a>';
		return $icon;
	}
	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	protected function getButtons() {

		$buttons = array ('csh' => '', 'shortcut' => '', 'save' => '');
		// CSH
		$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

		$headericon = '<img src="' . ($this->doc->backPath . t3lib_extMgm::extRelPath('subsitewizard') . 'ext_icon.gif') . '" alt="" width="16" height="16" />';
		// SAVE button
		$buttons['save'] = '<div class="ssheadertitle">' . $headericon . $GLOBALS['LANG']->getLL('headertitle') . '</div>';

		// Shortcut
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}

		return $buttons;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/subsitewizard/mod1/index.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/subsitewizard/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_subsitewizard_module1');
$SOBE->init();

// Include files?
foreach ($SOBE->include_once as $INC_FILE)
	include_once ($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
