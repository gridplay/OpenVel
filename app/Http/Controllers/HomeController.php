<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Core;
use App\Models\Robust;
use App\Models\Reports;
use Storage;
use Log;
class HomeController extends Controller {
	public function missingMethod($params = array()) {
		return view('errors.404');
	}
	// GET Requests
	public function index() {
		return view('index');
	}
	public function show($id) {
		if (empty($id)) {
			return view('index');
		}else{
			if (view()->exists($id)) {
				return view($id);
			}else{
				return view('index');
			}
		}
	}
	public function showPage($id = "") {
		if (empty($id)) {
			return view('index');
		}else{
			if (view()->exists($id)) {
				return view($id);
			}else{
				return view('index');
			}
		}
	}
	public function getTexture($uuid = '') {
		if (!empty($uuid)) {
			$img = Core::getTexture($uuid);
			return redirect($img);
		}
		return null;
	}
	public function siminfo() {
		$r = Robust::getSimInfo();
		return response()->json($r, 200)->header('Content-Type', 'application/json')->header('charset', 'utf-8');
	}
	public function getWelcome() {
		$s = Storage::get('welcome.json');
		$j = json_decode($s, true);
		shuffle($j);
		$c = count($j);
		$n = rand(0, ($c - 1));
		$r = $j[$n]['msg'];
		return response($r, 200)->header('Content-Type', 'text/text')->header('charset', 'utf-8');
	}
	public function AbuseReport() {
		Reports::savereport();
		return response("OK", 200)->header('Content-Type', 'text/text')->header('charset', 'utf-8');
	}
}
