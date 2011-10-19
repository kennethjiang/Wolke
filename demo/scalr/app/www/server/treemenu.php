<?
    define("NO_TEMPLATES", true);
    require("../src/prepend.inc.php");
    
    switch($_GET["_cmd"])
    {            
        case "delete":
            
            $itemid = (int)$get_itemId;
            $info = $db->GetRow("SELECT * FROM inventory WHERE id='{$itemid}'");
            
            try 
            {
                $db->Execute("DELETE FROM inventory WHERE id='{$itemid}'");
                $db->Execute("DELETE FROM server_inventory WHERE inventoryitemid='{$itemid}'");
                
                if ($info["isfolder"] == 1)
                    DeleteItemRecursive($itemid);
                    
                SystemLog::Log(sprintf(_("Inventory item '{$info['title']}' removed")));
                    
                $retval = array("status" => 1);
            }
            catch (Exception $e)
            {
                $retval = array("status" => 0, "msg" => $e->getMessage());
            }
            
            break;
        
        case "rename":
            
            try {
            
                 $info = $db->GetRow("SELECT * FROM inventory WHERE id='{$req_itemId}'");
                
                $db->Execute("UPDATE inventory SET title=? WHERE id=?", array($req_newText, $req_itemId));
                $retval = array("status" => 1);
                
                SystemLog::Log(sprintf(_("Inventory item '{$info['title']}' renamed to '{$req_newText}'")));    
            }
            catch (Exception $e)
            {
                $retval = array("status" => 0, "msg" => $e->getMessage());
            }
            
            break;
        
        case "addItem":
            
            $pid = (int)$get_parentId;
            $tpid = (int)$get_tparentId;
            $text = $get_itemName;
            $isfolder = (int)$get_isFolder;
            
            $type = $db->GetOne("SELECT type FROM inventory WHERE id=?", array($tpid));
            
            $db->Execute("INSERT INTO inventory SET title=?, isfolder='{$isfolder}', parentid=?, type=?", array($text, $pid, $type));
            $id = $db->Insert_ID();
            
            SystemLog::Log(sprintf(_("New inventory item '{$text}' has been added")));    
            
            $retval = array("status" => 1, "pid" => $pid, "id" => $id, "title" => $text, "type" => $type, "tType" => ($isfolder) ? "Folder" : "Item");
            
            break;
            
        case "setServerInventory":
            
            switch ($req_type)
            {
                case "db":
                        $table = "dbservers";
                    break;
                
                case "web":
                        $table = "webservers";
                    break;
                    
                case "ns":
                        $table = "nameservers";
                    break;
            }

            if (!$table)
                $retval = array("status" => 0);
            else 
            {
                $sid = (int)$req_sid;
                $itemid = (int)$req_itemid;
                $state = (int)$req_state;
                
                $info = $db->GetRow("SELECT * FROM inventory WHERE id='{$itemid}'");
                
                if ($info["isfolder"] == 0)
                {
                    try
                    {
                        if ($state == 1)
                        {
                            $db->Execute("INSERT INTO 
                                                    server_inventory
                                              SET
                                                    inventoryitemid = ?,
                                                    serverid        = ?,
                                                    servertype      = ?,
                                                    coments        = ''
                                      ON DUPLICATE KEY UPDATE servertype = ?
                                     ", array($itemid, $sid, $req_type, $req_type));
                        }
                        else 
                        {
                            $db->Execute("DELETE FROM server_inventory WHERE 
                                          inventoryitemid = ? AND 
                                          serverid = ? AND 
                                          servertype = ?", array($itemid, $sid, $req_type));
                        }
                        
                        $retval = array("status" => 1, "state" => $state, "id" => $itemid);
                    }
                    catch (ADODB_Exception $e)
                    {
                        $retval = array("status" => 0, "msg" => $e->getMessage());
                    }
                }
                else
                {
                    SetStateRecursive($info["id"], $sid, $req_type, $state);
                    $retval = array("status" => 1, "state" => $state, "id" => $info["id"]);
                }
            }
                       
            break;
    }
    
    function DeleteItemRecursive($itemid)
    {
        global $db;
        
        $childs = $db->GetAll("SELECT * FROM inventory WHERE parentid='{$itemid}'");
        
        foreach ($childs as $child)
        {
            if ($child["isfolder"] == 0)
            {
                $db->Execute("DELETE FROM inventory WHERE id='{$child["id"]}'");
                $db->Execute("DELETE FROM server_inventory WHERE inventoryitemid='{$child["id"]}'");
            }
            else 
                DeleteItemRecursive($child["id"]);
        }
    }
    
    function SetStateRecursive($itemid, $sid, $req_type, $state)
    {
        global $db;
        
        $childs = $db->GetAll("SELECT * FROM inventory WHERE parentid='{$itemid}'");
        
        foreach ($childs as $child)
        {
            if ($child["isfolder"] == 0)
            {
                try
                {
                    if ($state == 1)
                    {
                        $db->Execute("INSERT INTO 
                                                    server_inventory
                                              SET
                                                    inventoryitemid = ?,
                                                    serverid        = ?,
                                                    servertype      = ?,
                                                    coments        = ''
                                      ON DUPLICATE KEY UPDATE servertype = ?
                                     ", array($child["id"], $sid, $req_type, $req_type));
                    }
                    else 
                    {
                        $db->Execute("DELETE FROM server_inventory WHERE 
                                      inventoryitemid = ? AND 
                                      serverid = ? AND 
                                      servertype = ?", array($child["id"], $sid, $req_type));
                    }
                }
                catch (ADODB_Exception $e)
                {
                    //
                }
            }
            else 
                SetStateRecursive($child["id"], $sid, $req_type, $state);
        }
    }
    
    print json_encode($retval);
?>