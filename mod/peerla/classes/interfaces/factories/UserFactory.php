<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface UserFactory {
	
	/**
	 * The user object for the given id is created and returned
	 * 
	 * @param int $userId Unique id of the user
	 * @return \mod_kom_peerla\User Description
	 */
	function getUser($userId);
	
	
	
}
