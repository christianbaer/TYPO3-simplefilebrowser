<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_simplefilebrowser_pi1 = < plugin.tx_simplefilebrowser_pi1.CSS_editor
',43);

// Hook in TCEforms processing
//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:simplefilebrowser/hooks/class.tx_simplefilebrowser_tceforms_preprocsf.php:tx_simplefilebrowser_tceforms_preprocsf';

// Besser Hook in TCEmain verwenden
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:simplefilebrowser/hooks/class.tx_simplefilebrowser_tcemain_pdm_pre.php:tx_simplefilebrowser_tcemain_pdm_pre';

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_simplefilebrowser_pi1.php','_pi1','list_type',0);
?>