<?
    require("src/prepend.inc.php"); 
    
    if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=?", array($req_farmid));
    else 
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=? AND env_id=?", 
        	array($req_farmid, Scalr_Session::getInstance()->getEnvironmentId())
        );

    if (!$farminfo)
        UI::Redirect("/#/farms/view");
                
	$display["title"] = _("服务器组&nbsp;&raquo;&nbsp;统计");
	
	$display["rows"] = $db->GetAll("SELECT *, bw_out/1024 as bw_out, bw_in/1024 as bw_in FROM farm_stats WHERE farmid=? ORDER BY id DESC", array($req_farmid));
	foreach ($display["rows"] as &$row)
	{
		$total = (int)($row["bw_out"]+$row["bw_in"]);
		$row["bw_total"] = ($total > 1024) ? round($total/1024, 2)."GB" : round($total, 2)."MB";
		$row["bw_in"] = ($row["bw_in"] > 1024) ? round($row["bw_in"]/1024, 2)."GB" : round($row["bw_in"], 2)."MB";
		$row["bw_out"] = ($row["bw_out"] > 1024) ? round($row["bw_out"]/1024, 2)."GB" : round($row["bw_out"], 2)."MB";
		
		$Reflect = new ReflectionClass("INSTANCE_FLAVOR");
		foreach ($Reflect->getConstants() as $n=>$v)
		{
			$name = str_replace(".", "_", $v);
			$row[$name] = round($row[$name]/60/60, 1);
		}
		
		$row["date"] = date("F Y", mktime(0,0,0,$row["month"],1,$row["year"]));
	}
	
	require_once("src/append.inc.php");
?>