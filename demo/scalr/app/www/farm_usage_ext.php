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
                
	$display["title"] = _("Farm&nbsp;&raquo;&nbsp;Statistics");
	
	$info = $db->GetRow("SELECT *, bw_out/1024 as bw_out, bw_in/1024 as bw_in FROM farm_stats WHERE farmid=? AND month=? AND year=?",
		array($req_farmid, $req_month, $req_year)
	);
	if (!$info)
		UI::Redirect("farm_usage_stats.php?farmid={$req_farmid}");
	
	$total = (int)($info["bw_out"]+$info["bw_in"]);
	$info["bw_total"] = ($total > 1024) ? round($total/1024, 2)."GB" : round($total, 2)."MB";
	$info["bw_in"] = ($info["bw_in"] > 1024) ? round($info["bw_in"]/1024, 2)."GB" : round($info["bw_in"], 2)."MB";
	$info["bw_out"] = ($info["bw_out"] > 1024) ? round($info["bw_out"]/1024, 2)."GB" : round($info["bw_out"], 2)."MB";
	
	$Reflect = new ReflectionClass("INSTANCE_FLAVOR");
	$ReflectCost = new ReflectionClass("INSTANCE_COST");
	foreach ($Reflect->getConstants() as $n=>$v)
	{
		$name = str_replace(".", "_", $v);		
		$info[$name] = round($info[$name]/60/60, 1);		
		$info["{$name}_cost"] = ceil($info[$name])*$ReflectCost->getConstant(strtoupper($name));
		
		$info["total"] += $info[$name];
		$info["total_cost"] += $info["{$name}_cost"];
	}
	
	$info["start_date"] = date("d F Y", mktime(0,0,0,$info["month"],1,$info["year"]));
	
	if ((int)date("m") == (int)$info["month"])
		$info["end_date"] = date("d F Y", mktime(0,0,0,$info["month"],date("d"),$info["year"]));
	else
		$info["end_date"] = date("d F Y", mktime(0,0,0,$info["month"],date("t"),$info["year"]));
		
	$display["title"] = sprintf(_("'%s' farm statistics"), $farminfo['name']);
	$display["stats"] = $info;
	$display["farminfo"] = 	$farminfo;
	
	require_once("src/append.inc.php");
?>