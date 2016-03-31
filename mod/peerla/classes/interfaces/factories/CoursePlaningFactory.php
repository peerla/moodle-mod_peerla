<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CoursePlaningFactory {
	
	/**
	 * Get the course planing objects for all participants.
	 * 
	 * @return \mod_kom_peerla\CoursePlaning[] Course planing
	 */
	function getCurrentCoursePlanings($courseId);
	
	/**
	 * Get the course planing for a specific course and user.
	 * 
	 * @return \mod_kom_peerla\CoursePlaning|null Course planing
	 */
	function getParticipantCoursePlaning($courseId, $userId);
	
}
