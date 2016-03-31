<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface DataPresentation {
	
	
	/**
	 * Set the data view which should be displayed.
	 * 
	 * @param DataViewTreeNode $dataView Data view object
	 */
	function setData(DataViewTreeNode $dataView);
	
	/**
	 * Get the html/JavaScript string needed to display the data presentation.
	 * 
	 * @return string Html string
	 */
	function getHtmlString($containerSelector='');
	
}
