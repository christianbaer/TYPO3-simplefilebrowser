<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:simplefilebrowser/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Simple File Browser");

// Insert Code to dynamically generate flexform values
include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_simplefilebrowser_addFieldsToFlexForm.php');
//echo $BE_USER."#";
// Insert Flexform-Definition
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexformValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_simplefilebrowser_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_simplefilebrowser_pi1_wizicon.php';
?>