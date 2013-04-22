<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Lobacher Patrick <plobacher@net-o-graphic.com>
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Simple File Browser' for the 'simplefilebrowser' extension.
 *
 * @author	Lobacher Patrick <plobacher@net-o-graphic.com>
 * @package	TYPO3
 * @subpackage	tx_simplefilebrowser
 */
class tx_simplefilebrowser_pi1 extends tslib_pibase {
	var $prefixId = 'tx_simplefilebrowser_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_simplefilebrowser_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'simplefilebrowser';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;
		
		// get path from Flexform where the filebrowser should start from
		$this->pi_initPIflexform();
		$pathFromFlexform1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'directory', 'sDEF');
		$pathFromFlexform2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'directoryFromTree', 'sDEF');
		
		$pathFromFlexform = !empty($pathFromFlexform1) ? $pathFromFlexform1 : $pathFromFlexform2;

		$pageID = $GLOBALS['TSFE']->id;

		$origPath = $pathFromFlexform;
		if (empty($origPath)) {
			// get path from TypoScript where the filebrowser should start from
			$origPath =  $this->conf['directory'];
		}
		$origPath = ereg_replace('\/$','',$origPath);
		
		if (!is_dir($origPath)) {
			$content .= '<div class="simplefilebrowser-error">'.$this->pi_getLL('nodata')."</div>";
		} else {
			// check if there is a flexform entry for the basic path
			// if yes - it will have precedence over TS entry
			$getFile = t3lib_div::_GET('tx_simplefilebrowser_pi1');
			$getFile = $getFile['file'];
			
			// is there a path information in the GET-Var?
			$getPath = t3lib_div::_GET('tx_simplefilebrowser_pi1');
			$getPath = $getPath['path'];
			if (!empty($getPath)) {
				$path = $this->checkAndCleanPath($getPath,$origPath);
			}
			$path = !(empty($path)) ? $path : $origPath;
	              
                        // read root dir via typoscript
                        $rootPath = $this->conf['rootDirectory'];

                        if (!empty($getDownloadPath)) {
                            if($rootPath){
                                $downloadPath = $this->checkAndCleanPathForDownload($getDownloadPath,$origPath,$rootPath);
                            }else{
                                $downloadPath = $this->checkAndCleanPath($path,$origPath);
                            }
                        }
			// is there any directory info?
			if (!empty($getFile)) {
				$filearray = t3lib_div::getAllFilesAndFoldersInPath(array(),$downloadPath);
				$pathToFile = $filearray[$getFile];
				$filename = $this->getFileName($pathToFile);
				$mimetype = $this->getMimeType($filename);
				header('Content-type: '.$mimetype['mimetype']);
				header('Content-Disposition: attachment; filename="'.$filename.'"');
	     		header('Expires: 0');
	     		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    	header('Pragma: public');
				readfile($pathToFile);
				die();
			}
                        
			// get directory content
			$dir = $this->getDir($path);
				
			// display directory content
			$content .= $this->displayDir($dir,$path,$origPath);
			
		}
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Extraxt filename from complete path
	 *
	 * @param	string		$path: complete path incl. filename
	 * @return	just the plain filename
	 */
	function getFileName($path) {
		$path = ereg_replace('\/$','',$path);
		$len = strlen($path);
		$file = substr($path,strrpos($path,"/")+1,$len);
		return $file;
	}
	
	/**
	 * Get MIME-Information from filename
	 *
	 * @param	string		$file: filename
	 * @return	array with 1.) Extension, 2.) Description, 3.) MIME-Type
	 */
	function getMimeType($file) {
		$len = strlen($file);
		$ext = substr($file,strrpos($file,".")+1,$len);
		$extArray['pdf'] = array(	extension 	=> 	'pdf',
									desc	 	=> 	'Adobe PDF File',
									mimetype	=>	'application/pdf');
		$extArray['zip'] = array(	extension 	=> 	'zip',
									desc	 	=> 	'ZIP-File',
									mimetype	=>	'application/zip');
		$extArray['xls'] = array(	extension 	=> 	'xls',
									desc	 	=> 	'Microsoft Excel File',
									mimetype	=>	'application/vnd.ms-excel');
		$extArray['xlt'] = array(	extension 	=> 	'xlt',
									desc	 	=> 	'Microsoft Excel Vorlagen File',
									mimetype	=>	'application/vnd.ms-excel');
		$extArray['doc'] = array(	extension 	=> 	'doc',
									desc	 	=> 	'Microsoft Word File',
									mimetype	=>	'application/word');
		$extArray['dot'] = array(	extension 	=> 	'dot',
									desc	 	=> 	'Microsoft Word Vorlagen File',
									mimetype	=>	'application/word');
		$extArray['jpg'] = array(	extension 	=> 	'jpg',
									desc	 	=> 	'JPEG Picture File',
									mimetype	=>	'image/jpg');
		$extArray['vsd'] = array(	extension 	=> 	'vsd',
									desc	 	=> 	'Microsoft Visio File',
									mimetype	=>	'application/x-msdownload');
		$extArray['ppt'] = array(	extension 	=> 	'ppt',
									desc	 	=> 	'Microsoft Powerpoint File',
									mimetype	=>	'application/x-msdownload');
		$extArray['pot'] = array(	extension 	=> 	'pot',
									desc	 	=> 	'Microsoft Powerpoint Template File',
									mimetype	=>	'application/x-msdownload');
		$extArray['tif'] = array(	extension 	=> 	'tif',
									desc	 	=> 	'Tagged Image File - TIF',
									mimetype	=>	'image/tiff');
		$extArray['eps'] = array(	extension 	=> 	'eps',
									desc	 	=> 	'Encasulated Postscript - EPS',
									mimetype	=>	'application/postscript');
		$extArray['txt'] = array(	extension 	=> 	'txt',
									desc	 	=> 	'Plaintext',
									mimetype	=>	'text/pain');
		$extArray['swf'] = array(	extension 	=> 	'swf',
									desc	 	=> 	'ShockWave Flash',
									mimetype	=>	'application/x-shockwave-flash');
		$extArray['gif'] = array(	extension 	=> 	'gif',
									desc	 	=> 	'Compuserve Graphics Interchange Format',
									mimetype	=>	'image/gif');
		$extArray['png'] = array(	extension 	=> 	'png',
									desc	 	=> 	'Portable Network Graphics',
									mimetype	=>	'image/png');
		$extArray['flv'] = array(	extension 	=> 	'flv',
									desc	 	=> 	'Flash Video',
									mimetype	=>	'video/mp4');
		$extArray['mp3'] = array(	extension 	=> 	'mp3',
									desc	 	=> 	'MPEG-1 Audio Layer 3',
									mimetype	=>	'audio/mpeg');
		$extArray['unknown'] = array(	extension 	=> 	'unknown',
									desc	 	=> 	'Unknown Filetype',
									mimetype	=>	'application/x-msdownload');
		if (array_key_exists($ext,$extArray)) {
			return $extArray[$ext];
		} else {
			return $extArray['unknown'];
		}
	}
	
	/**
	 * Display directories and files
	 *
	 * @param	array		$dir: contains subdirectories and files of given path
	 * @param	string		$path: contains current path
	 * @param	string		$origPath: contains original path (set by TypoScript)
	 * @return	the HTML-Code containing a filebrowser
	 */
	function displayDir($dir,$path,$origPath) {
		
		// Get the template
		$templateUrl = $conf['templateFile'];
		if (empty($templateUrl)) $templateUrl = "EXT:".$this->extKey."/pi1/simplefilebrowser_pi1_template.html";
 		$this->templateCode = $this->cObj->fileResource($templateUrl);
		
		$template = $this->cObj->getSubpart($this->templateCode,'###SIMPLEFILEBROWSER_ENTRY###');

		if ($path != $origPath) {
			$content .= '<a href="'.$this->pi_linkTP_keepPIvars_url(array(path=>substr($path,0,strrpos($path,"/",1)))).'">';
			$content .= '<img src="'.t3lib_extMgm::siteRelPath('simplefilebrowser').'res/icon_folder.gif" border="0" class="dpIcon">&nbsp;'.'<strong>..</strong>'."<br>";
			$content .= '</a>';
		} 
		
		if (is_array($dir['dir'])) {
			foreach ($dir['dir'] as $key => $elem) {
				$content .= '<a href="'.$this->pi_linkTP_keepPIvars_url(array(path=>$path."/".$elem)).'">';
				$content .= '<img src="'.t3lib_extMgm::siteRelPath('simplefilebrowser').'res/icon_folder.gif" border="0" class="dpIcon">&nbsp;'.$elem."<br>";
				$content .= '</a>';
			}
		}
		if (is_array($dir['files'])) {
		
			// get additional information like modification date
			$dateFormat = (!empty($this->conf['dateFormat'])) ? $this->conf['dateFormat'] : "d.m.Y H:i";
			$fullDir = t3lib_div::getAllFilesAndFoldersInPath(array(),$path);
			foreach ($fullDir as $key => $elem) {
				$info = stat($elem);
				$addinfo[$key]['modifydate'] = date($dateFormat,$info['mtime']);
			}
			
			// Specify the last entry
			$last = end($dir['files']);

			foreach ($dir['files'] as $key => $elem) {
				$filename = $this->getFileName($elem);
				$mimetype = $this->getMimeType($filename);
				$ext = $mimetype['extension'];
				
				if ($elem == $last) {
					$markers['###JOIN###'] = '<img src="'.t3lib_extMgm::siteRelPath('simplefilebrowser').'res/join_end.gif" border="0" class="dpIcon">';
				} else {
					$markers['###JOIN###'] = '<img src="'.t3lib_extMgm::siteRelPath('simplefilebrowser').'res/join.gif" border="0" class="dpIcon">';
				}
				//echo $key;
				$link = '<a href="'.$this->pi_linkTP_keepPIvars_url(array(file=>$key, path=>$path)).'">';
				$link .= '<img src="'.t3lib_extMgm::siteRelPath('simplefilebrowser').'res/icon_'.$ext.'.gif" border="0" class="dpIcon">&nbsp;'.$elem;
				$link .= '</a>';
				$markers['###LINK###'] = $link;
				
				$subTemplate = $this->cObj->getSubpart($template,'###SHOWDATEWRAP###');
				
				if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'showdate', 'sDEF')) {
					$subMarkerContent = $this->cObj->substituteMarker($subTemplate,'###SHOWDATE###',$addinfo[$key]['modifydate']);
				}
				
				$subMarkers['###SHOWDATEWRAP###'] = $subMarkerContent;
				
				//$content .=  $this->cObj->substituteMarkerArrayCached($template,$markers,$subMarkers);
				// code to replace substituteMarkerArrayCache
				$content .= $this->cObj->substituteMarkerArray($template, $markers);
				foreach ($subMarkers as $subPart => $subContent) {
    				$content = $this->cObj->substituteSubpart($content, $subPart, $subContent);
				}
				
			}
		}
		return $content;
	}
	
	/**
	 * Cleans up the path and checks, if new path ist within given original path (for security reasons)
	 *
	 * @param	string		$path: contains current path
	 * @param	string		$origPath: contains original path (set by TypoScript)
	 * @return	the cleaned path (or FALSE otherwise)
	 */
	function checkAndCleanPath($path,$origPath) {
		//echo "|".$path."|".$origPath."|".strpos($path,$origPath)."|";
		$path = str_replace("..","",$path);
		$path = str_replace(".","",$path);
		$path = str_replace("%2E","",$path);
		$path = str_replace("'","",$path);
		$path = str_replace('"',"",$path);
		$path = str_replace("\\","",$path);
		if (strpos($path,$origPath) === 0) {
			return $path;
		} else {
			return false;
		}
	}
        /**
         * Cleans up the path and checks, if new path ist within given original path (for security reasons)
         *
         * @param	string		$path: contains current path
         * @param	string		$origPath: contains original path (set by TypoScript)
         * @return	the cleaned path (or FALSE otherwise)
         */
        function checkAndCleanPathForDownload($path,$origPath,$rootPath) {

            $path = str_replace("..","",$path);
            $path = str_replace(".","",$path);
            $path = str_replace("%2E","",$path);
            $path = str_replace("'","",$path);
            $path = str_replace('"',"",$path);
            $path = str_replace("\\","",$path);

            if (strpos($path,$rootPath) === 0) {
                if (strpos($path,$origPath) === 0) {
                    return $origPath;
                } else {
                    return $path;
                }
            } else {
                return false;
            }
        }
	/**
	 * Build of array containing subdirectories and files of given path
	 *
	 * @param	string		$path: contains current path
	 * @return	the array of subdirectories and files
	 */
	function getDir($path) {
		$dirs = t3lib_div::get_dirs($path);
		if (is_array($dirs)) {
			asort($dirs);
		}
		$dir['dir'] = $dirs;
		$files = t3lib_div::getFilesInDir($path);
		//echo "<pre>".print_r(t3lib_div::getAllFilesAndFoldersInPath(array(),$path),1);
		if (is_array($files)) {
			asort($files);
		}
		$dir['files'] = $files;
		//echo "<pre>".print_r($dir['files'],1);
		return $dir;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplefilebrowser/pi1/class.tx_simplefilebrowser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplefilebrowser/pi1/class.tx_simplefilebrowser_pi1.php']);
}

?>