<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/UserFactory.php';

/**
 * Description of UserFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingUserFactory implements UserFactory {
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		require_once realpath(__DIR__).'/../LazyLoadingUser.php';
	}
	
	public function getUser($userId){
		
		$sql = "SELECT u.id, u.firstname, u.lastname, u.email"
				. "	FROM {user} u"
				. "	WHERE id = :userid";
		$userData = $this->db->get_record_sql($sql,array('userid' => $userId));
		
		if ($userData === false){
			return null;
		}
		
		$courseFactory = new LazyLoadingCourseFactory($this->db);
		$user = new LazyLoadingUser($userId, $courseFactory);
		
		$user->setFirstName($userData->firstname);
		$user->setLastName($userData->lastname);
		$user->setEmail($userData->email);
		
		return $user;
	}
}
