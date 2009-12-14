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
		$this->MOD_MENU = Array ('function' => Array ('1' => $LANG->getLL('function1'), '2' => $LANG->getLL('function2')));
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

			$this->doc->inDocStyles = '
							.subsitewizard label {float:left; width: 120px;}
							.subsitewizard p {clear: left;padding: 4px 10px;}
						';
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
				$this->content .= $this->doc->section('New Subsite:', $content, 0, 1);
				break;
			case 2 :
				$content = '<div align=center><strong>Menu item #2...</strong></div>';
				$this->content .= $this->doc->section('Message #2:', $content, 0, 1);
				break;
			case 3 :
				$content = '<div align=center><strong>Menu item #3...</strong></div>';
				$this->content .= $this->doc->section('Message #3:', $content, 0, 1);
				break;
		}
	}

	/**
	 * Hets the form
	 */
	protected function getSubsiteWizardForm() {
		$post = t3lib_div::_POST('data');
		$form = '';
		if ($post['sscreatesubsite']) {
			$form = $this->processSubmit();
		}

		$form .= '
					<fieldset class="subsitewizard">
						<legend>General Informations</legend>
						<p>
							<label for="sstitle">*Subsite Title:</label>
							<input type="text" id="sstitle" name="data[sstitle]" value="' . htmlspecialchars($post['sstitle']) . '" size="60" />
						</p>
						<p>
							<label for="ssptitle">*Page Title:</label>
							<input type="text" id="ssptitle" name="data[ssptitle]" value="' . htmlspecialchars($post['ssptitle']) . '" size="60" />
						</p>
						<p>
							<label for="ssnavtitle">*Navigation Title:</label>
							<input type="text" id="ssnavtitle" name="data[ssnavtitle]" value="' . htmlspecialchars($post['ssnavtitle']) . '" size="60" />
						</p>
						<p>
							<label for="ssparentpid">*Parent PID:</label>
							<input type="text" id="ssparentpid" name="data[ssparentpid]" value="' . htmlspecialchars($post['ssparentpid']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('ssparentpid') . '
						</p>
						<p>
							<label for="ssuplinkpid">*Uplink PID:</label>
							<input type="text" id="ssuplinkpid" name="data[ssuplinkpid]" value="' . htmlspecialchars($post['ssuplinkpid']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('ssuplinkpid') . '
						</p>
						<p>
							<label for="ssheaderimg">Header Image (optional):</label>
							<input type="text" id="ssheaderimg" name="data[ssheaderimg]" value="' . htmlspecialchars($post['ssheaderimg']) . '" size="60" />&nbsp;' . $this->browseLinksIcon('ssheaderimg', 'file', 'gif,jpg,jpeg,tif,bmp,pcx,tga,png') . '
						</p>
						<p>
							<label for="firstbeuser">Erster BE Benutzer:</label>
							<input type="text" id="firstbeuser" name="data[firstbeuser]" value="' . htmlspecialchars($post['firstbeuser']) . '" size="10" />&nbsp;' . $this->browseLinksIcon('firstbeuser', 'page', 'be_users', 'db') . '
						</p>
					</fieldset>
					<fieldset class="subsitewizard">
						<legend>Kontakt Informationen</legend>
						<p>
							<label for="sscontact">Kontakt Name:</label>
							<input type="text" id="sscontact" name="data[sscontact]" value="' . htmlspecialchars($post['sscontact']) . '" size="60" />
						</p>
						<p>
							<label for="sscontactmail">Kontakt E-Mail:</label>
							<input type="text" id="sscontactmail" name="data[sscontactmail]" value="' . htmlspecialchars($post['sscontactmail']) . '" size="60" />
						</p>
						<p>
							<label for="sscontactphone">Kontakt Telefon:</label>
							<input type="text" id="sscontactphone" name="data[sscontactphone]" value="' . htmlspecialchars($post['sscontactphone']) . '" size="60" />
						</p>
						<p>
							<label for="sscomment">Kommentar:</label>
							<textarea id="sscomment" name="data[sscomment]" rows="7" cols="60">' . htmlspecialchars($post['sscomment']) . '</textarea>
						</p>

					</fieldset>
					<fieldset class="subsitewizard">
						<legend>Praesenz Informationen</legend>
						<p>
							<label for="ssverantwortlicher">Verantwortlicher:</label>
							<input type="text" id="ssverantwortlicher" name="data[ssverantwortlicher]" value="' . htmlspecialchars($post['ssverantwortlicher']) . '" size="60" />
						</p>
						<p>
							<label for="sslaufzeit">Laufzeit:</label>
							<input type="text" id="sslaufzeit" name="data[sslaufzeit]" value="' . htmlspecialchars($post['sslaufzeit']) . '" size="60" />
						</p>
						<p>
							<label for="sskostenstelle">Kostenstelle:</label>
							<input type="text" id="sskostenstelle" name="data[sskostenstelle]" value="' . htmlspecialchars($post['sskostenstelle']) . '" size="60" />
						</p>
					</fieldset>
					<p>
						<input type="submit" name="data[sscreatesubsite]" id="sscreatesubsite" value="Subsite anlegen" />
					</p>
					';

		return $form;
	}

	protected function processSubmit() {
		$post = t3lib_div::_POST('data');
		$data = array ();

		#return t3lib_div::view_array($post);

		// Create Subsite Record
		$data['tx_subsitewizard_subsites']['NEW0'] = array (
			'title' => htmlspecialchars($post['sstitle']),
			'parentpid' => trim(htmlspecialchars($post['ssparentpid'])),
			'uplinkpid' => trim(htmlspecialchars($post['ssparentpid'])),
			'contact' => htmlspecialchars($post['sscontact']),
			'contactmail' => htmlspecialchars($post['sscontactmail']),
			'contactphone' => htmlspecialchars($post['sscontactphone']),
			'comment' => htmlspecialchars($post['sscomment']),
			'kostenstelle' => htmlspecialchars($post['sskostenstelle']),
			'praesenzverantwortlicher' => htmlspecialchars($post['ssverantwortlicher']),
			'laufzeit' => htmlspecialchars($post['sslaufzeit']),
		);

		// Create Subsite Startpage
		$data['pages']['NEW1'] = array (
			'title' => htmlspecialchars($post['sstitle']),
			'pid' => trim(htmlspecialchars($post['ssparentpid'])),
			'navtitle' => htmlspecialchars($post['ssnavtitle']),
		);

		return t3lib_div::view_array($data);

		// Real Data Creation
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values = 0;
		$tce->reverseOrder = 1;

		// set default TCA values specific for the user
		$TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
		if (is_array($TCAdefaultOverride)) {
			$tce->setDefaultsFromUserTS($TCAdefaultOverride);
		}

		$data = array ();

		$tce->start($data, array ());
		$tce->process_datamap();

	}
	protected function browseLinksIcon($fieldName, $tabs = "page", $bParams = '', $mode = 'wizard') {
		$uid = uniqid('popUpID');
		$allTabs = 'page,file,folder,url,mail,spec';
		$blindLinkOptions = t3lib_div::rmFromList($tabs, $allTabs);
		if ($bParams) {
			$bp = '&bparams=' . $fieldName . '|||' . $bParams . '|';
		}
		$aOnClick = 'this.blur(); vHWin=window.open(\'../../../../typo3/browse_links.php?mode=' . $mode . $bp . '&P[field]=' . $fieldName . '&P[formName]=sswizardform&P[itemName]=' . $fieldName . '&P[params][blindLinkOptions]=' . $blindLinkOptions . '&P[fieldChangeFunc][typo3form.fieldGet]=null&P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=null\',\'' . $uid . '\',\'height=300,width=500,status=0,menubar=0,scrollbars=1\'); vHWin.focus(); return false;';
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

		// SAVE button
		$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';

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