<?php

namespace mod_kom_peerla;

/**
 * 
 * @author Christoph Bohr
 */
interface User {
	
	/**
	 * Get the unique user id.
	 * 
	 * @return int User id
	 */
	function getUserId();
	
	/**
	 * Get all courses of this user.
	 * 
	 * @return Course[] Array of Course objects
	 */
	function getCourses();
	
	/**
	 * Get all currently active courses of the user.
	 * 
	 * @return Course[] Array of Course objects
	 */
	function getCurrentCourses();
	
	/**
	 * Get the first name of the user
	 * 
	 * @return string First name
	 */
	function getFirstName();
	
	/**
	 * Get the last name of the user
	 * 
	 * @return string Last name
	 */
	function getLastName();
	
	/**
	 * Get the email address of the user
	 * 
	 * @return string Email address
	 */
	function getEmail();
}
