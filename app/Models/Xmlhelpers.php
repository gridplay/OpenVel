<?php
namespace App\Models;
use App\Models\Xml;
use App\Models\Money;
use App\Models\Robust;
class Xmlhelpers extends Core {
	public static function process_transaction($avatarId, $amount, $ipAddress)
	{
		# Do Credit Card Processing here!  Return False if it fails!
		# Remember, $amount is stored without decimal places, however it's assumed
		# that the transaction amount is in Cents and has two decimal places
		# 5 dollars will be 500
		# 15 dollars will be 1500

		return True;
	}
	public static function update_simulator_balance($agentId)
	{
		/*$db = new DB;
		$sql = "select serverIP, serverHttpPort from Presence ".
				"inner join regions on regions.uuid = Presence.RegionID ".
				"where Presence.UserID = '".$db->escape($agentId)."'";

		$db->query($sql);
		$results = $db->next_record();
		if ($results)
		{
			$serverIp = $results["serverIP"];
			$httpport = $results["serverHttpPort"];
		

			$req      = array('agentId'=>$agentId);
			$params   = array($req);

			$request  = xmlrpc_encode_request('balanceUpdateRequest', $params);
			$response = self::do_call($serverIp, $httpport, $request); 
		}*/
	}
	public static function user_alert($agentId, $soundId, $text)
	{
	    /*$db = new DB;
	    $sql = "select serverIP, serverHttpPort, regionSecret from Presence ".
				"inner join regions on regions.uuid = Presence.RegionID ".
				"where Presence.UserID = '".$db->escape($agentId)."'";
	    
	    $db->query($sql);

	    $results = $db->next_record();
	    if ($results)
	    {
	        $serverIp = $results["serverIP"];
	        $httpport = $results["serverHttpPort"];
			$secret   = $results["regionSecret"];
	        
	        
	        $req = array('agentId'=>$agentId, 'soundID'=>$soundId,
					'text'=>$text, 'secret'=>$secret);

	        $params = array($req);

	        $request = xmlrpc_encode_request('userAlert', $params);
	        $response = self::do_call($serverIp, $httpport, $request);
	    }*/
	}
	public static function move_money($sourceId, $destId, $amount, $aggregatePermInventory,
			$aggregatePermNextOwner, $flags, $transactionType, $description,
			$regionGenerated,$ipGenerated)
	{
		return Money::pay($sourceId, $destId, $amount, $description, $regionGenerated);
	}
	public static function do_call($host, $port, $request)
	{
	    $url = "http://$host:$port/";
	    $header[] = "Content-type: text/xml";
	    $header[] = "Content-length: ".strlen($request);
	    
	    $data = parent::senddata('post', $url, ['body' => $request], $header);
	    return $data;
	}
}