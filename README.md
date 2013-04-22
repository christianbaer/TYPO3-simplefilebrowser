TYPO3-simplefilebrowser
=======================

This is a fork of Patrick Lobachers EXT:simplefilebrowser to add the functionality of multiple plugins on one page.

### The problem

Patricks extension is simple and brilliant, it does what it is supposed to. But in a recent project we wanted several simplefilebrowser-plugins on one TYPO3-page, which led to an error: Let's say you got three plugins, each is showing the content of a different folder. Now when you click on a file listed by the second or third plugin, the first plugin 'reacts' and denies access, becaue it says 'File is not in my folder'.

### The solution

1. via Typoscript you have to set a root-folder
2. the path is always added as a parameter to the links
3. the security-check is slightly adjusted: Is file in root-folder?


### Usage

You have to set the root of the directory via Typoscript: 

plugin.tx_simplefilebrowser_pi1.rootDirectory = /path/to/typo3-installation/fileadmin/root-folder/

Now you can use several plugins on one page, which point to another folder (within rootDirectory).
