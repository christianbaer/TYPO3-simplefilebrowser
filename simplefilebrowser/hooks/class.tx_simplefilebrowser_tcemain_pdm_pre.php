<?php

// this hook determines if the user has admin rights
// if yes - nothing will changed
// if no - the flexform field "directory" will be deleted as only admins should have access to a path which will be given as absolute value

class tx_simplefilebrowser_tcemain_pdm_pre {
	function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj) {
		if (!$GLOBALS['BE_USER']->isAdmin()) {
			$fieldArray['pi_flexform']['data']['sDEF']['lDEF']['directory']['vDEF'] = "";
  		}
	}
}
?>