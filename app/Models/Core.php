<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Log;
use Mail;
use Storage;
use App\Models\Robust;
use Carbon\Carbon;
class Core extends Eloquent {
	//
	public static function senddata($meth, $url, $data = [], $head = []) {
		$headers = self::httpheaders($head, $data);
		$http = Http::withHeaders($headers);
		$json = [];
		if (array_key_exists('json', $data)) {
			$json = $data['json'];
		}
		if (array_key_exists('body', $data)) {
			$http = $http->withBody($data['body']);
		}
		if (array_key_exists('form', $data)) {
			$http = $http->withQueryParameters($data['form']);
		}
		$response = null;
		if (strtolower($meth) == "post") {
			$response = $http->post($url, $json);
		}
		if (strtolower($meth) == "get") {
			$response = $http->get($url, $json);
		}
		if (strtolower($meth) == "put") {
			$response = $http->put($url, $json);
		}
		if ($response->ok()) {
			return $response->body();
		}else{
			Log::debug($response->throw());
		}
		return null;
	}
	private static function httpheaders($h = [], $data = []) {
        /* application/x-www-form-urlencoded */
		$h['verify'] = "false";
		$h['timeout'] = 3.14;
		$h['content-type'] = 'application/x-www-form-urlencoded';
		if (array_key_exists('json', $data)) {
			$h['content-type'] = 'application/json';
		}
        if (isset($data['content-type'])) {
            $h['content-type'] = $data['content-type'];
        }
		return $h;
	}
	public static function checkTexture($uuid) {
		if (Storage::disk('public')->exists('/imgs/'.$uuid.'.png')) {
			return true;
		}
		return false;
	}
	public static function getTexture($uuid) {
		if ($uuid == Robust::$NULL_KEY) {
			return url('imgs/null.png');
		}else if (self::checkTexture($uuid)) {
			return url('imgs/'.$uuid.'.png');
		}else{
			$asset_url = 'http://'.env('GRID_URL').':8003/assets/'.$uuid;
			$h = fopen($asset_url, "rb");
			$file_content = stream_get_contents($h);
			fclose($h);
			$xml = new \SimpleXMLElement($file_content);
			$datas = base64_decode($xml->Data);
			$_img = new \Imagick();
			$_img->readImageBlob($datas);
			$_img->setImageFormat('png');
			$dump = $_img->getImageBlob();
			if (!$dump) {
				return '';
			}else{
				return self::saveTexture($dump, $uuid);
			}
		}
	}
	public static function saveTexture($img, $uuid) {
		Storage::disk('public')->put('/imgs/'.$uuid.'.png', $img);
		return url('imgs/'.$uuid.'.png');
	}
	/*
	| easy API to send emails
	| Core::sendemail($toWho, $subj, $msg)
	*/
	public static function sendemail($toWho, $subj, $msg) {
        Mail::send(['email.html', 'email.text'], ['subj' => $subj, 'addy' => $toWho, 'msg' => $msg], function ($message) use ($toWho, $subj) {
            $conf = config('mail.from');
            $message->from($conf['address'], $conf['name']);
            $message->to($toWho);
            $message->subject($subj);
        });
        return "sent";
	}

	public static function time2date($time = "", $dater = 'M d Y g:iA') {
		if ($time != "") {
			if (!is_numeric($time)) {
				$time = strtotime($time);
			}
			if ($time != 0) {
				$d = date($dater, $time);
				if (strpos($d, "484") !== false) {
					$timer = $time / 1000;
					$d = date($dater, $timer);
				}
				return $d;
			}else{
				return "";
			}
		}else{
			return "";
		}
	}
	// this converts readable time to UNIX time
	public static function date2time($date = "") {
		if ($date != "") {
			if (!is_numeric($date)) {
				return strtotime($date);
			}else{
				return time();
			}
		}else{
			return time();
		}
	}

	public static function readabletimestamp($date = null) {
		if (!is_null($date)) {
			if (!is_numeric($date)) {
				return self::time2date(self::date2time($date));
			}else{
				return self::time2date($date);
			}
		}else{
			return self::time2date(time());
		}
	}
	public static function UTC2Local($date = null) {
		if (!is_null($date)) {
			$time = date('M d Y g:i.sA', strtotime($date));
			$c = Carbon::createFromFormat('M d Y g:i.sA', $time, 'UTC')->setTimezone(config('app.timezone'));
			return self::readabletimestamp($c);
		}
	}
}