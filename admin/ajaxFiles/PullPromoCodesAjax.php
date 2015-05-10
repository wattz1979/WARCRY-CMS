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
	if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_PROMO_CODES))
	{
		echo json_encode(array('error' => 'You dont have the required permissions.'));
		die;
	}
		
	function FormatCode($token, $format)
	{
		//split into markers
		$markers = str_split($format);
		$keyChar = str_split($token);
		
		$reduce = 0;
		$key = '';
		//let's put up our key
		foreach ($markers as $index => $marker)
		{
			if (strtolower($marker) == 'x')
			{
				$key .= $keyChar[$index - $reduce];
			}
			else
			{
				$key .= $markers[$index];
				$reduce++;
			}
		}
		unset($markers, $keyChar, $index, $marker, $reduce);
		
		return $key;
	}
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array('id', 'token', 'usage', 'reward_type', 'reward_value', 'format', 'added');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */

	$sTable = 'promo_codes';
	
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
		
		//Resolve the usage type
		switch ((int)$aRow['usage'])
		{
			case PCODE_USAGE_ONCE:
				$usage = 'Unique';
				break;
			case PCODE_USAGE_PER_ACC:
				$usage = 'Per Account';
				break;
			default:
				$usage = 'Unknown';
				break;
		}
		
		//Resolve the reward
		switch ((int)$aRow['reward_type'])
		{
			case PCODE_REWARD_CURRENCY_S:
				$reward = $aRow['reward_value'] . ' Silver Coins';
				break;
			case PCODE_REWARD_CURRENCY_G:
				$reward = $aRow['reward_value'] . ' Gold Coins';
				break;
			case PCODE_REWARD_ITEM:
				$reward = 'Item: ' . $aRow['reward_value'];
				break;
		}
		
		//Set the first two columns
		$row[0] = $aRow['id'];
		$row[1] = FormatCode($aRow['token'], $aRow['format']);
		$row[2] = $usage;
		$row[3] = $reward;
		$row[4] = $aRow['added'];
		$row[5] = '<a href="execute.php?take=delete&action=pcode&id='.$aRow['id'].'" onclick="return deletecheck(\'Are you sure you want to delete this code?\');" class="button icon remove danger">Remove</a>';
		
		//Now we have to pull 
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>