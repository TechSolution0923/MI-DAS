<?php
	class quotation_model extends Model
	{
		public function getAllQuotations($repWhere)
		{
			header('Content-Type: application/json');

			$limit = 10;
			$searchKeys = array('s.account', 'c.name', 's.orderno', 's.custorderno', 's.quotereason', 's.quotefailurereason', 's.quotevalue', 's.quotefolldate');

			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]) ? $_POST["search"] : array();
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$order = $this->getDatatableSearchOrder($searchKeys);
			$columnSearch = $this->getDataTableColumnSearch($searchKeys);
			$searchKey = strtolower($search['value']);

			if (!empty($repWhere))
			{
				$repCodeArray = explode(",", $repWhere);

				if (!empty($repCodeArray))
				{
					$repClause = "AND s.repcode IN ('".implode("','", $repCodeArray)."')";
				}
			}

			$queryParams = array
			(
				'searchKey'    => $searchKey,
				'columnSearch' => $columnSearch,
				'order'        => $order,
				'limit'        => $length,
				'offset'       => $start,
				'totalQuery'   => true,
				'allRecords'   => true,
				'export'       => false,
				'repClause'    => $repClause,
			);

			$allRecords = $this->allQuotationsQuery($queryParams);

			$queryParams['allRecords'] = false;
			$total = $this->allQuotationsQuery($queryParams);

			$queryParams['totalQuery'] = false;
			$result = $this->allQuotationsQuery($queryParams);

			$returnResult = array();

			foreach ($result as $item)
			{
				$item['name'] = '<a href="'.base_url().'customer/customerDetails/'.base64_encode($item['account']).'">'.$item['name'].'</a>';
				$item['orderno'] = '<a href="'.base_url().'quotation/detail/'.$item['orderno'].'">'.$item['orderno'].'</a>';
				$returnResult[] = array_values($item);
			}

			$return_array = array
			(
				'draw'            => $draw,
				'recordsTotal'    => $allRecords,
				'recordsFiltered' => $total,
				'data'            => $returnResult,
			);

			echo json_encode($return_array);
			exit();
		}

		public function getAllQuotationsForExport($repWhere)
		{
			header("Content-type: text/x-csv");
			header('Content-Disposition: attachment;filename="quotations.csv"');
			header("Cache-Control: max-age=0");

			$searchKeys = array('s.account', 'c.name', 's.orderno', 's.custorderno', 's.quotereason', 's.quotevalue', 's.datein', 's.quotefolldate', 's.quoteexpidate');
			$search = isset($_POST["search"]) ? $_POST["search"] : array();

			$order = $this->getDatatableSearchOrder($searchKeys);
			$columnSearch = $this->getDataTableColumnSearch($searchKeys);
			$searchKey = strtolower($search['value']);

			if (!empty($repWhere))
			{
				$repCodeArray = explode(",", $repWhere);

				if (!empty($repCodeArray))
				{
					$repClause = "AND s.repcode IN ('".implode("','", $repCodeArray)."')";
				}
			}

			$queryParams = array
			(
				'searchKey'    => $searchKey,
				'columnSearch' => $columnSearch,
				'order'        => $order,
				'limit'        => null,
				'offset'       => null,
				'totalQuery'   => false,
				'allRecords'   => false,
				'export'       => true,
				'repClause'    => $repClause,
			);

			$query = $this->allQuotationsQuery($queryParams);

			$this->load->dbutil();

			$csvResult = $this->dbutil->csv_from_result($query);

			echo $csvResult;
			exit();
		}

		public function getDatatableSearchOrder($searchKeys)
		{
			$posted_order = $_POST['order'];
			$column_index = -1;

			$order = array
			(
				'by'  => "s.orderno",
				'dir' => "desc",
			);

			if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir']))
			{
				$column_index = $posted_order[0]['column'];
			}

			if ($column_index >= 0)
			{
				$order = array
				(
					'by'  => $searchKeys[$column_index],
					'dir' => $posted_order[0]['dir'],
				);
			}

			return $order;
		}

		public function getDataTableColumnSearch($searchKeys)
		{
			$postedColumns = $_POST['columns'];

			$search = array();

			foreach ($postedColumns as $key => $col)
			{
				$colSearchValue = trim($col['search']['value']);

				if (!empty($colSearchValue))
				{
					$search[$searchKeys[$key]] = strtolower(trim($col['search']['value']));
				}
			}

			return $search;
		}

		public function allQuotationsQuery($queryParams)
		{
			extract($queryParams);

			$sql = "SELECT s.account, c.name, s.orderno, s.custorderno, s.quotereason, s.quotefailurereason, s.quotevalue, DATE_FORMAT(s.quotefolldate,'%d-%m-%Y') FROM salesorders s LEFT JOIN customer c ON (s.account = c.account) WHERE s.ordtype = 'QT' ".$repClause;

			if (!$allRecords)
			{
				if (!empty($searchKey))
				{
					$sql.= " AND (LOWER(s.account) LIKE '%".$searchKey."%' OR LOWER(c.name) LIKE '%".$searchKey."%' OR LOWER(s.orderno) LIKE '%".$searchKey."%' OR LOWER(s.custorderno) LIKE '%".$searchKey."%' OR LOWER(s.quotereason) LIKE '%".$searchKey."%' OR LOWER(s.quotefailurereason) LIKE '%".$searchKey."%' OR LOWER(s.quotevalue) LIKE '%".$searchKey."%' OR LOWER(s.quotefolldate) LIKE '%".$searchKey."%')";
				}

				if (!empty($columnSearch))
				{
					foreach ($columnSearch as $key => $specific)
					{
						$sql.= " AND LOWER(".$key.") LIKE '%".$specific."%'";
					}
				}
			}

			$sql.= " GROUP BY s.orderno";

			if (!$totalQuery)
			{
				$sql.= " ORDER BY ".$order['by']." ".$order['dir'];

				if (!$export)
				{
					$sql.= " LIMIT ".$offset.", ".$limit;
				}
			}

			$query = $this->db->query($sql);

			if ($export)
			{
				return $query;
			}

			if ($totalQuery)
			{
				return $query->num_rows();
			}

			return $query->result_array();
		}

		/* NOT CURRENTLY USED - BUT MAY BE NEEDED IF /quotation/detail/{orderno} converts to serverSide DataTable */
		// public function getQuotationDetail($orderNumber)
		// {
		// 	header('Content-Type: application/json');

		// 	$start = isset($_POST["start"]) ? $_POST["start"] : 0;
		// 	$length = isset($_POST["length"]) ? $_POST["length"] : 10;
		// 	$search = isset($_POST["search"]) ? $_POST["search"] : array();
		// 	$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

		// 	$searchKeys = array("s.orderno", "s.prodcode", "p.description", "s.quantity", "s.unitprice", "s.discount1", "s.discount2", "s.sales");

		// 	$return_array = array
		// 	(
		// 		'draw'            => $draw,
		// 		'recordsTotal'    => 0,
		// 		'recordsFiltered' => 0,
		// 		'data'            => array(),
		// 		'with'            => array('columns' => $_POST['columns']),
		// 	);

		// 	$orderNumber = intval($orderNumber);

		// 	$order = $this->getDatatableSearchOrder($searchKeys);
		// 	$columnSearch = $this->getDataTableColumnSearch($searchKeys);
		// 	$searchKey = $search['value'];

		// 	$total = $this->quotationDetailQuery($orderNumber, $searchKey, $columnSearch, $order, $length, $start, true);
		// 	$result = $this->quotationDetailQuery($orderNumber, $searchKey, $columnSearch, $order, $length, $start);

		// 	$returnResult = array();

		// 	foreach ($result as $item)
		// 	{
		// 		$item['description'] = "<a href='".base_url()."products/details/".$item['prodcode']."'>".$item['description']."</a>";
		// 		$returnResult[] = array_values($item);
		// 	}

		// 	$return_array = array
		// 	(
		// 		'draw'            => $draw,
		// 		'recordsTotal'    => $total,
		// 		'recordsFiltered' => $total,
		// 		'data'            => $returnResult,
		// 		'with'            => array('columns' => $_POST['columns']),
		// 	);

		// 	echo json_encode($return_array);
		// 	exit();
		// }

		public function getQuotationDetailsForExport()
		{
			header("Content-type: text/x-csv");
			header('Content-Disposition: attachment;filename="quotation-details-'.intval($_POST['salesOrderNumber']).'.csv"');
			header("Cache-Control: max-age=0");

			$searchKeys = array("s.prodcode", "s.fulldesc", "s.quantity", "s.unitprice", "s.discount1", "s.discount2", "nettprice", "s.sales");
			$search = isset($_POST["search"]) ? $_POST["search"] : array();

			$order = $this->getDatatableSearchOrder($searchKeys);
			$columnSearch = $this->getDataTableColumnSearch($searchKeys);
			$searchKey = strtolower($search['value']);

			$queryParams = array
			(
				'orderNumber'  => intval($_POST['salesOrderNumber']),
				'searchKey'    => $searchKey,
				'columnSearch' => $columnSearch,
				'order'        => $order,
				'limit'        => null,
				'offset'       => null,
				'totalQuery'   => false,
				'allRecords'   => false,
				'export'       => true,
			);

			$query = $this->quotationDetailQuery($queryParams);

			$this->load->dbutil();

			$csvResult = $this->dbutil->csv_from_result($query);

			echo $csvResult;
			exit();
		}

		// public function quotationDetailQuery($orderNumber, $searchKey = "", $columnSearch = "", $order = null, $limit = null, $offset = null, $totalQuery = false)
		public function quotationDetailQuery($queryParams)
		{
			extract($queryParams);

			$sql = "SELECT s.prodcode, s.fulldesc, s.quantity, s.unitprice, s.discount1, s.discount2, ROUND(s.unitprice * (100 - s.discount1) * (100 - s.discount2) / 10000, 2) AS nettprice, s.sales FROM salesorders s WHERE s.orderno = '".$orderNumber."' AND s.ordtype = 'QT'";

			if (!$allRecords)
			{
				if (!empty($searchKey))
				{
					$sql.= " AND (LOWER(s.prodcode) LIKE '%".$searchKey."%' OR LOWER(s.fulldesc) LIKE '%".$searchKey."%' OR LOWER(s.quantity) LIKE '%".$searchKey."%' OR LOWER(s.unitprice) LIKE '%".$searchKey."%' OR LOWER(s.discount1) LIKE '%".$searchKey."%' OR LOWER(s.discount2) LIKE '%".$searchKey."%' OR ROUND(s.unitprice * (100 - s.discount1) * (100 - s.discount2) / 10000, 2) LIKE '%".$searchKey."%' OR LOWER(s.sales) LIKE '%".$searchKey."%')";
				}

				if (!empty($columnSearch))
				{
					foreach ($columnSearch as $key => $specific)
					{
						$sql.= " AND LOWER(".$key.") LIKE '%".$specific."%'";
					}
				}
			}

			if (!$totalQuery)
			{
				if (isset($order['by']) && isset($order['dir']))
				{
					$sql.= " ORDER BY ".$order['by']." ".$order['dir'];
				}

				if (!$export && is_integer($offset) && is_integer($limit))
				{
					$sql.= " LIMIT ".$offset.", ".$limit;
				}
			}

			$query = $this->db->query($sql);

			if ($export)
			{
				return $query;
			}

			if ($totalQuery)
			{
				return $query->num_rows();
			}

			return $query->result_array();
		}
	}
