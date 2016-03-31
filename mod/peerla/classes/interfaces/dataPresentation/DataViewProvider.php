<?php

namespace mod_kom_peerla;

/**
 * A data provider for NominalOrinalDataPoints.
 * 
 * @author Christoph Bohr
 */
interface DataViewProvider {
	
	/**
	 * Get a data view generated by this provider.
	 * 
	 * @return DataViewTreeNode Root node of the data view.
	 */
	function getDataView();
	
}
