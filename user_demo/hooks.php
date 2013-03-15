<?php

/**
 * ownCloud
 *
 * @author Lukas Reschke
 * @copyright 2013 Lukas Reschke lukas@statuscode.ch
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\demo_auth;

class Data  {

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @param       string   $permissions New folder creation permissions
	 * @return      bool     Returns true on success, false on failure
	 */
	static public function xcopy($source, $dest, $permissions = 0755)
	{
	    	// Check for symlinks
		if (is_link($source)) {
			return symlink(readlink($source), $dest);
		}

	    	// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

	    	// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest, $permissions);
		}

	    	// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
	        	// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

	 	       // Deep copy directories
			Data::xcopy("$source/$entry", "$dest/$entry");
		}

	    	// Clean up
		$dir->close();
		return true;
	}


	static public function createData($params) {
		$uid = str_replace(array('/', '\\'), '',  $params['uid']);
                @mkdir(\OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" . $uid .'/');

		// Import files
		$src = \OC_App::getAppPath('user_demo')."/data/files/"; 
		$dst = \OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" . $uid .'/files/';
		Data::xcopy($src, $dst);

		// Import bookmarks
		\OC_Bookmarks_Bookmarks::importFile(\OC_App::getAppPath('user_demo')."/data/bookmarks.html");

		// Contacts
		// Delete all existing adressbooks 
		$deleteAdressbooks = \OCP\DB::prepare( 'DELETE FROM `*PREFIX*contacts_addressbooks` WHERE `userid` LIKE ?' );
		$deleteAdressbooks->execute(array( $uid ) );
		// Copy the VCF
		@mkdir(\OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" . $uid .'/imports/');
		copy(\OC_App::getAppPath('user_demo')."/data/contacts.vcf", \OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" . $uid .'/imports/contacts.vcf');	
		require_once \OC_App::getAppPath('user_demo').'/contacts_import.php';

		// Set the login timestamp
		$query = \OCP\DB::prepare('
			INSERT INTO *PREFIX*user_demo
			(userid, login)
			VALUES (?, ?)
			');
		$query->execute(array($uid, time()));
	}

	static public  function deleteData() {
		$uid = str_replace(array('/', '\\'), '',  $_SESSION['user_id']);

		if($uid != "demo") {
			\OC_Helper::rmdirr(\OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" .$uid .'/');
			\OC_Preferences::deleteUser($uid);
		}
	}

	static public function cleanOldUsers() {
		$lastTime = \OCP\DB::prepare('
			SELECT * FROM `*PREFIX*user_demo`
			WHERE `login` > ?
			');
		$lastTime->execute(array(time()+86400));
		while( $row = $results->fetchRow() ) {
			\OC_Helper::rmdirr(\OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" .$row['userid'] .'/');
			\OC_Preferences::deleteUser($row['userid']);
		}
	}
}
