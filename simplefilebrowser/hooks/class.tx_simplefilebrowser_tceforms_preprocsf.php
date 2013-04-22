<?php

// this hook determines if the user has admin rights
// if yes - nothing will changed
// if no - the flexform field "directory" will be deleted as only admins should have access to a path which will be given as absolute value

class tx_simplefilebrowser_tceforms_preprocsf {

	function getSingleField_preProcess($table, $field, &$row, $altName, $palette, $extra, $pal, $pObj) {
		if ($GLOBALS['BE_USER']->isAdmin()) {
		
			$fieldTSconfig = $pObj->setTSconfig($table,$row,$field);
			debug($TCA);
			$flex = $row['pi_flexform'];
			$flexarray = t3lib_div::xml2array($flex);
			unset ($flexarray['data']['sDEF']['lDEF']['directory']);
			$xmlData = t3lib_div::array2xml($flexarray, '', 0, 'T3FlexForms');
			$row['pi_flexform'] = $xmlData;
			//debug($row['pi_flexform'],"Debug");
			//debug($out,"Debug");
		}
	}
	
}
?>