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

class OC_USER_DEMOAUTH extends OC_User_Backend {

	public function deleteUser($uid) {
		// Can't delete user
		return false;
	}

	public function setPassword ( $uid, $password ) {
		// We can't change user password
		return false;
	}

	// Return an uniqid
	public function checkPassword( $uid, $password ) {
		return(\OC_Util::generate_random_bytes(30));
	}


	public function getDisplayName($uid) {
		return "Demo User";
	}


	// Always return true
	public function userExists( $uid ){
		return true;
	}

	// User don't get listed
	public function hasUserListings() {
		return false;
	}
}
