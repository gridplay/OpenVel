<?php
namespace App\Models;
use DB;
use Log;
use App\Models\Robust;
use App\Models\Xmlhelpers;
use App\Models\Users;
use App\Models\Sqms;
use App\Models\Tier;
class Xml extends Core {
	public static function process() {
		$xmlrpc_server = xmlrpc_server_create();
		try {
			$callback = array (__CLASS__, "buy_land_prep");
			xmlrpc_server_register_method($xmlrpc_server, "preflightBuyLandPrep", $callback);
			$callback = array (__CLASS__, "buy_land");
			xmlrpc_server_register_method($xmlrpc_server, "buyLandPrep", $callback);

			$request_xml = file_get_contents("php://input");
			$r = xmlrpc_server_call_method($xmlrpc_server, $request_xml, '');
			return $r;
		}finally {
			xmlrpc_server_destroy($xmlrpc_server);
		}
	}
	public static function buy_land_prep($method_name, $params, $app_data)
	{

	#	$confirmvalue = "password"; # Use this to request password re-entry
		$req          = $params[0];

		$agentid      = $req['agentId'];
		$sessionid    = $req['secureSessionId'];
		$amount       = $req['currencyBuy'];
		$billableArea = $req['billableArea'];
		$confirmvalue = $amount;

	    #
	    # Validate Requesting user has a session
	    #

	    if($user = Robust::tbl('Presence')->where('UserID', $agentid)->where('SecureSessionID', $sessionid)->first())
		{		
			$success = false;
			$upgrade = false;
			$action = "You need to log into the website to get land";
			$desc = "You need to have a website account first";
			$sqms = 0;
			$maxsqms = 0;
			if ($lu = User::where('uuid', $agentid)->first()) {
				$usqms = Sqms::countsqms($lu->uuid);
				$tier = Tier::find($lu->tier);
				if ($tier->sqms <= $usqms) {
					$upgrade = true;
					$action = "You need to increase your tier to get more land";
					$desc = "Your at or over your tier usage";
					$success = false;
				}else if ($tier->sqms > $usqms) {
					$success = true;
					$action = "You can buy this parcel";
					$desc = "Your using ".number_format($usqms)." out of ".number_format($tier->sqms);
				}
				$sqms = $usqms;
				$maxsqms = $tier->sqms;
			}

			$landUse = array(
					'upgrade' => $upgrade,
					'action' => $action,
					'inc' => $desc);

			$currency = array('estimatedCost' => ($amount / 250));

			$membership = array(
					'upgrade' => false,
					'action'  => "Premium is NOT required on this grid",
					'levels'  => []);

			if ($success) {
				return array(
						'success'    => True,
						'currency'   => $currency,
						'membership' => $membership,
						'landUse'    => $landUse,
						'confirm'    => "");
			}else{
				return array(
					'success'      => False,
					'errorMessage' => "\n\nNot enough sqms available for your tier\n".$action,
					'errorURI'     => "");
			}
		}
		else
		{
			return array(
					'success'      => False,
					'errorMessage' => "\n\nUnable to Authenticate\n\nClick URL for more info.",
					'errorURI'     => "");
		}
	}
	public static function buy_land($method_name, $params, $app_data)
	{
		//global $economy_source_account;
		$req          = $params[0];
		$agentid      = $req['agentId'];
		$sessionid    = $req['secureSessionId'];
		$amount       = $req['currencyBuy'];
		$real         = $req['estimatedCost'];
		$billableArea = $req['billableArea'];
		$ipAddress    = $_SERVER['REMOTE_ADDR'];

	    #
	    # Validate Requesting user has a session
	    #

	    if($user = Robust::tbl('Presence')->where('UserID', $agentid)->where('SecureSessionID', $sessionid)->first())
		{
			if($amount > 0)
			{
				/*if(!Xmlhelpers::move_money($economy_source_account, $agentid, $amount, 0, 0, 0, 0, "Land purchase",$user->RegionID,$ipAddress))
				{
					header("Content-type: text/xml");
					$response_xml = xmlrpc_encode(array(
							'success'      => False,
							'errorMessage' => "\n\nThe gateway has declined your transaction. Please update your payment method and try again later.",
							'errorURI'     => ""));
					return $response_xml;
				}

				*/
			}
			
			return array('success' => True);
		}
		else
		{
			return array(
					'success'      => False,
					'errorMessage' => "\n\nUnable to Authenticate\n\nClick URL for more info.",
					'errorURI'     => "");

		}
	}
}
