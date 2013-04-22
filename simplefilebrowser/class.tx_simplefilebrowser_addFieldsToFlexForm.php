<?php

 class tx_simplefilebrowser_addFieldsToFlexForm {
  function addFields ($config) {
    $optionList = array();
    // add first option
    //$optionList[0] = array(0 => 'option1', 1 => 'value1');
    // add second option
    //$optionList[1] = array(0 => 'option2', 1 => 'value2');
    //$config['items'] = array_merge($config['items'],$optionList);
	$counter = 0;
	$filemounts = $GLOBALS['BE_USER']->groupData['filemounts'];
	if (!is_array($filemounts)) $filemounts = array("12345"=>array("path"=>$GLOBALS['DOCUMENT_ROOT']."/fileadmin"));
	foreach ($filemounts as $mount) {
	   $dirs = $this->getDirs(array(),$mount['path']);
	   foreach($dirs as $key => $path) {
	   		$level = substr_count($path,"/") - substr_count($GLOBALS['DOCUMENT_ROOT'],"/") -2;
			//echo strrpos($path,"/",-1)."---".strlen($path)."###";
			$pathname = ereg_replace('\/$','',$path);
			//echo strrpos($path,"/")."#";
			$pathname = substr($pathname,strrpos($pathname,"/")+1);
			//echo $pathname."<br>";
			$pathname = str_repeat("&nbsp;&nbsp;&nbsp;", $level) . $pathname;
			$optionList[$counter] = array(0 => $pathname, 1 => $path);
			$counter++;
	   }
	 
	 /* echo "<pre>";
		print_r($this->getDirs(array(),$mount['path'])); */
	}
	$config['items'] = array_merge($config['items'],$optionList);
	
    return $config;
  }
  
  
	function getDirs($fileArr,$path,$recursivityLevels=99) {
		$fileArr[] = $path;
		$dirs = t3lib_div::get_dirs($path);
		if (is_array($dirs) && $recursivityLevels>0)	{
			foreach ($dirs as $subdirs)	{
				if ((string)$subdirs!='')	{
					$fileArr = $this->getDirs($fileArr,$path.$subdirs.'/',$recursivityLevels-1);
				}
			}
		}
		if (is_array($dirs)) {
			//asort($dirs);
		}
		return $fileArr;
  }
  
 }
 
 ?>