<?php

// this hook determines if the user has admin rights
// if yes - nothing will changed
// if no - the flexform field "directory" will be deleted as only admins should have access to a path which will be given as absolute value

class tx_simplefilebrowser_tcemain_pdm_post {

	function processDatamap_postProcessFieldArray(&$fieldArray, $table, $id, &$pObj) {
		debug($fieldArray);
		if (!$GLOBALS['BE_USER']->isAdmin()) {
			if ($status == 'update' && $table == 'tt_content') {
				$row = t3lib_BEfunc::getRecord($table, $id);
				if (is_array($row)) {
					$flex = $row['pi_flexform'];
					
					$flexarray = t3lib_div::xml2array($flex);
					//unset ($flexarray['data']['sDEF']['lDEF']['directory']);
					//debug($flexarray);
					//$flexarray['data']['sDEF']['lDEF']['directory'] = '<vDEF></vDEF>';
					$xmlData = t3lib_div::array2xml($flexarray, '', 0, 'T3FlexForms');
					$xmlData = '<?xml version="1.0" encoding="iso-8859-1" standalone="yes" ?>' . $xmlData;
					//$row['pi_flexform'] = $xmlData;
					
					//$dataArr = array ();
					//$dataArr[$table][$id]['pi_flexform'] = $xmlData;
					//debug($dataArr,"dataArr");
					/* $tce = t3lib_div::makeInstance('t3lib_TCEmain');
        			$tce->start($dataArr, array());
        			$tce->process_datamap(); */
					$fieldArray['pi_flexform'] = $xmlData;
					//debug($fieldArray,"Data");
				}
			}
		
  		}
		
	}
	
}
?>