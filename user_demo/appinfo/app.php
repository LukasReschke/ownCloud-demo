<?php

/**
* ownCloud - user_demo
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

require_once OC_App::getAppPath('user_demo').'/user_demo.php';
require_once OC_App::getAppPath('user_demo').'/group_demo.php';
require_once OC_App::getAppPath('user_demo').'/hooks.php';

OC_User::useBackend("DEMOAUTH");
OC_Group::useBackend(new OC_GROUP_DEMOAUTH());

OCP\Util::connectHook( 'OC_User', 'post_login', 'OCA\demo_auth\Data', 'createData' );
OCP\Util::connectHook( 'OC_User', 'logout', 'OCA\demo_auth\Data', 'deleteData' );
