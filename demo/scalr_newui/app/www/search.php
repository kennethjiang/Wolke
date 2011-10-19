<?php
	require("src/prepend.inc.php"); 
	
	function collect_search_queries ($MenuItem, $parents, &$queries)
	{
		$search = (string)$MenuItem->attributes()->search;
		if ($search)
		{
			$title = "";
			array_shift($parents);
			foreach ($parents as $Parent)
				$title .= "{$Parent->attributes()->title} &raquo; ";
			$title .= "{$MenuItem}";
				
			$queries[] = array
			(
				"title" => $title,
				"href" => "{$MenuItem->attributes()->href}",
				"sql" => $search
			);
		}
	}
	
	function rows_sorter ($r1, $r2)
	{
		return $r1["count"] > $r2["count"] ? 1 : ($r1["count"] < $r2["count"] ? -1 : 0);
	}
	
	$search_queries = array();
	$Menu->Walk($Menu->GetXml(), "collect_search_queries", $search_queries);

	$rows = array();
	foreach ($search_queries as $sq)
	{
		$sql = str_replace('{CLIENT_ID}', Scalr_Session::getInstance()->getClientId(), $sq["sql"]);
		$sql = str_replace('{ENV_ID}', Scalr_Session::getInstance()->getEnvironmentId(), $sql);
		$sql = str_replace('%s', '%' . addslashes($req_search) . '%', $sql);
		
		$count = $db->GetOne($sql);
		if ($count)
		{
			$rows[] = array
			(
				"title" => $sq["title"],
				"href" => $sq["href"] . (strpos($sq["href"], "?") === false ? "?" : "&") . "query={$req_search}", 
				"count" => $count
			);
		}
	}
	usort($rows, "rows_sorter");
	$rows = array_reverse($rows);
	
	$display["grid_data"] = json_encode(array("total" => count($rows), "data" => $rows));

	$display["title"] = _("Dashboard") . " &raquo; " . _("Search");
	$display['load_extjs'] = true;
	
	require("src/append.inc.php");
?>