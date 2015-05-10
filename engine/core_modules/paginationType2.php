<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Pagination
{
	
	private $LinkAdd = '?';
	
	public function addToLink($str)
	{
		$this->LinkAdd = $str;
	}
		
	public function calculate_pages($total_rows, $rows_per_page, $page_num)
	{
		$arr = array();
		
		//If we have no rows return empty array
		if ($total_rows < 1)
		  return array('limit' => '', 'current' => '', 'info' => '', 'first' => '', 'previous' => '', 'next' => '', 'last' => '');
		
		// calculate last page
		$last_page = ceil($total_rows / $rows_per_page);
		
		// make sure we are within limits
		$page_num = (int)$page_num;
		
		if ($page_num < 1)
		{
		   $page_num = 1;
		} 
		elseif ($page_num > $last_page)
		{
		   $page_num = $last_page;
		}
		
		$upto = ($page_num - 1) * $rows_per_page;
		$arr['limit'] = $upto.',' .$rows_per_page;

		$arr['current'] = $page_num;
        
		//Info panel
		if ($last_page > 1)
		{
			//if this is the last page ?
			if ($page_num == $last_page)
			{
				$arr['info'] = '<li id="pages"><p>|&nbsp;&nbsp;</p>'. (($page_num - 1) * $rows_per_page) .'-'. $total_rows .' of '. $total_rows .'<p>&nbsp;&nbsp;|</p></li>';
			}
			else
			{
				$arr['info'] = '<li id="pages"><p>|&nbsp;&nbsp;</p>'. (($page_num - 1) * $rows_per_page) .'-'. ($page_num * $rows_per_page) .' of '. $total_rows .'<p>&nbsp;&nbsp;|</p></li>';
			}
		}
		else
		{
		  	$arr['info'] = '';
		}
		
		//If we are at first page or second we dont need this btn (FIRST)
        if ($page_num == '1' or $page_num == '2')
		{
        	$first_page_btn_html = '';
        }
		else
		{
        	$first_page_btn_html = '<li id="pagination-nav-first"><a href="'.$this->LinkAdd.'&p=1">First</a></li>';
        }
		$arr['first'] = $first_page_btn_html;

		//If we are at first page we dont need prev btn
		if ($page_num == '1')
		{
			$prev_page_btn_html = '';
		}
		else
		{
			$prev_page_btn_html = '<li id="pagination-nav-prev"><a href="'.$this->LinkAdd.'&p='.($page_num - 1).'">Previous</a></li>';
		}				
		$arr['previous'] = $prev_page_btn_html;
			
		//If we are at last page we dont need NEXT btn
		if ($page_num == $last_page)
		{
			$next_page_btn_html = '';
		}
		else
		{
			$next_page_btn_html = '<li id="pagination-nav-next"><a href="'.$this->LinkAdd.'&p='.($page_num + 1).'">Next</a></li>';
		}
		$arr['next'] = $next_page_btn_html;

		//If we are at last page or the one before we dont need this btn (LAST PAGE)
		if ($page_num == $last_page or $page_num == ($last_page - 1))
		{
			$last_page_btn_html = '';
		}
		else
		{
			$last_page_btn_html = '<li id="pagination-nav-last"><a href="'.$this->LinkAdd.'&p='.$last_page.'">Last</a></li>';
		}
		$arr['last'] = $last_page_btn_html;
				
	  return $arr;
	}
}
