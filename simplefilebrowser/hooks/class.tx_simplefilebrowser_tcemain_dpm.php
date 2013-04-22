<?php

// this hook determines if the user has admin rights
// if yes - nothing will changed
// if no - the flexform field "directory" will be deleted as only admins should have access to a path which will be given as absolute value

class tx_simplefilebrowser_tcemain_pdm_post {

	function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$reference) {
		debug($status);
		if (!$GLOBALS['BE_USER']->isAdmin()) {
			$flexarray = $incomingFieldArray['pi_flexform'];
			$flexarray['data']['sDEF']['lDEF']['directory'] = "none";
			$incomingFieldArray['pi_flexform'] = $flexarray;
		}
	}
	
}
?>