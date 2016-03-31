<?php

namespace mod_kom_peerla;

/**
 * 
 *
 * @author Christoph Bohr
 */
interface DbCourseTopicDataFetcher {
	/**
	 * Get all course topics visible to the given user.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @return CourseTopic[]
	 */
	function getCourseTopicsDbData($courseId, $userId);
}
