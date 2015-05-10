<?php
	if (!defined('init_ajax'))
	{	
		header('HTTP/1.0 404 not found');
		exit;
	}
	
	if (!$CURUSER->isOnline())
	{
		echo json_encode(array('error' => 'You must be logged in.'));
		die;
	}
	
	//check for permissions
	if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_STORE))
	{
		echo json_encode(array('error' => 'You dont have the required permissions.'));
		die;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array('entry', 'name', 'ItemLevel', 'realm', 'gold', 'silver', 'class', 'subclass', 'displayid', 'InventoryType', 'Quality', 'description', 'id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	//Logs Source
	$sSource = ($_GET['realm'] == '-1') ? false : (int)$_GET['realm'];
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */

	$sTable = 'store_items';
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
			intval( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
					($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ($sSource !== false)
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' AND `realm` LIKE CONCAT('%', '".$sSource."', '%') OR ";
			else
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ( $sWhere == "" && $sSource !== false)
	{
		$sWhere = "WHERE `realm` LIKE CONCAT('%', '".$sSource."', '%') ";
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";
	$rResult = $DB->query( $sQuery);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $DB->query( $sQuery);
	$aResultFilterTotal = $rResultFilterTotal->fetch(PDO::FETCH_NUM);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $DB->query( $sQuery);
	$aResultTotal = $rResultTotal->fetch(PDO::FETCH_NUM);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 0,
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = $rResult->fetch() )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
		}
		
		//Set the first two columns
		$row[0] = $aRow['entry'];
		$row[1] = '<a href="" class="q'.$aRow['Quality'].' item-link" rel="item='.$aRow['entry'].'">' . $aRow['name'] . '</a>';
		$row[2] = $aRow['ItemLevel'];
		$row[3] = $aRow['realm'];
		$row[4] = $aRow['gold'];
		$row[5] = $aRow['silver'];
		$row[6] = Item_FindClass($aRow['class']) . ' [' . $aRow['class'] . ']';
		$row[7] = Item_FindSubclass($aRow['class'], $aRow['subclass']) . ' [' . $aRow['subclass'] . ']';
		$row[8] = '<span class="button-group">
					<a href="#" onclick="return ConstructEdit('.$aRow['id'].');" class="button icon edit">Edit</a>
					<a href="#" onclick="return DeleteItem(this, '.$aRow['id'].');" class="button icon remove danger">Remove</a>
				  </span>';

		//Now we have to pull 
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>