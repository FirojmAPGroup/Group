<?php
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade\File;

// COMMON FUNCTIONS --------------------------------------------------------------------------------------------------- //
function _p($data, $exit = 0)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	if ($exit) die('');
}
function putNA($val)
{
	if (is_numeric($val)) return $val;
	return $val ? $val : '-';
}
function _lvl()
{
	return '<span class="lvl">&nbsp; &raquo; &nbsp;</span>';
}
function siteName($title = NULL)
{
	return $title ? env('APP_NAME') . ' | ' . $title : env('APP_NAME');
}
function siteLogo()
{
	return pathAssets('images/trust-wave-logo.png');
}
function uniqueCode()
{
	return uniqid();
}
function seoText($str)
{
	for ($i = 0; $i < 5; $i++) {
		$str = str_replace('&', 'and', trim($str));
		$str = str_replace(['#', '$', '\'', '"', '?', '&', ':', '!', '%', '&reg;', '&trade;', '(', ')', '/', ',', '_'], '-', trim($str));
		$str = str_replace([' '], '-', trim($str));
		$str = str_replace(['--', '--'], '-', trim($str));
	}
	return $str;
}
function siteConfig($key, $default = NULL)
{
	return config('site-config.' . $key, $default);
}
function makeDropdown($options = [], $selected = [])
{
	$return = [];
	if (count($options)) {
		foreach ($options as $k => $v) {
			$sel = '';
			if (is_array($selected)) {
				if (in_array($k, $selected))
					$sel = 'selected';
			} else if ($selected === $k) {
				$sel = 'selected';
			}
			$return[] = '<option value="' . $k . '" ' . $sel . '>' . ucfirst(trim(strip_tags($v))) . '</option>';
		}
	}

	return implode('', $return);
}
function printPrice($price = 0)
{
	return '$' . round($price, 2);
}
function _star()
{
	return '<span class="text-danger">*</span>';
}
function labelInfo($text)
{
	return $text ? '<span class="text-info" >' . $text . '</span>' : '';
}

function pathPublic($path)
{
	return url($path);
}
function pathAssets($path)
{
	return url('assets/' . $path) . '?ts=' . time();
}
function validate($request, $rules = [], $messages = [])
{
	$validator = Validator::make($request, $rules, $messages);
	return $validator->errors()->all();
}
function exMessage($e)
{
	return 'Error on line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
}
function encId($id)
{
	return encrypt($id);
}
function useId($id)
{
	return decrypt($id);
}


// DATE FUNCTIONS --------------------------------------------------------------------------------------------------- //
function dbDate($date, $datetime = true)
{
	$date = $date ? $date : date('Y-m-d H:i:s');
	if ($datetime) {
		return date('Y-m-d H:i:s', strtotime($date));
	} else {
		return date('Y-m-d');
	}
}
function frDate($date, $datetime = 0)
{
	if ($date) {
		$tz = 'Africa/Johannesburg';
		$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
		$dt->setTimestamp(is_numeric($date) ? $date : strtotime($date)); //adjust the object to correct timestamp
		return $datetime ? $dt->format('d-m-Y H:i') : $dt->format('d-m-Y');
	}
	return '';
}


function dayCalc($date)
{
	$now = time(); // or your date as well
	$date_diff = $now - strtotime($date);
	return round($date_diff / (60 * 60 * 24));
}
function totalHoursCalc($date)
{
	$origin = now();
	$target = date_create($date);
	$interval = date_diff($origin, $target);
	return $interval->format('%a days, %h hours, %i minutes, %s seconds');
}
// ROUTE FUNCTIONS --------------------------------------------------------------------------------------------------- //
function routePut($name, $args = [])
{
	return $name && Route::has($name) ? route($name, $args) : '';
}
function routeCurrName()
{
	return Route::getCurrentRoute() ? Route::getCurrentRoute()->getName() : '';
}
function routeActive($name)
{
	return routeCurrName() == $name ? 1 : '';
}


// REQUEST FUNCTIONS --------------------------------------------------------------------------------------------------- //
function isReq($key)
{
	return request()->exists($key);
}
function isPost()
{
	return request()->isMethod('POST') ? 1 : 0;
}
function addReq($key, $val)
{
	request()->merge([$key => $val]);
}


// TRANSLATION FUNCTIONS --------------------------------------------------------------------------------------------------- //
function getMsg($key, $args = [])
{
	return trans('messages.' . $key, $args);
}
// User Auth --------------------------------------------------------------------------------------------------- //
function userGuard()
{
	return 'web';
}
function userLogin()
{
	if (!auth()->guard(userGuard())->check()) return 0;
	return auth()->guard(userGuard())->user();
}
function logId()
{
	return userLogin() ? userLogin()->getId() : 0;
}
function logName()
{
	return userLogin() ? userLogin()->getName() : '';
}
function logEmail()
{
	return userLogin() ? userLogin()->email : '';
}
function logRole()
{
	return userLogin() ? userLogin()->getRole() : '';
}
function logRolePrint()
{
	return userLogin() ? userLogin()->printRole() : '';
}
function logUserImage()
{
	return userLogin() ? userLogin()->getProfilepic() : '';
}
function logUserName()
{
	return userLogin() ? userLogin()->getUsername() : '';
}
function logFullName()
{
	return userLogin() ? userLogin()->printFullName() : '';
}
function logPrintRole()
{
	return userLogin() ? userLogin()->printRole() : '';
}
function isSuperAdmin()
{
	return userLogin() ? userLogin()->isSuperAdmin() : '';
}
function isAdmin()
{
	return userLogin() ? userLogin()->isAdmin() : '';
}

function getMultipleDropdownName($options = [], $selected = [])
{

	$return = [];
	if (count($options)) {
		foreach ($options as $k => $v) {
			if (is_array($selected)) {
				if (in_array($k, $selected))
					$return[] = ucfirst(trim(strip_tags($v)));
			}
		}
	}
	return implode(',', $return);
}


function createdOrUpdatedBy($id)
{
	$full_name = \App\Models\User::find($id);
	return $full_name ? $full_name->printFullName() : '';
}
function createdOrUpdatedByWithRole($id)
{
	$full_name = \App\Models\User::find($id);
	return $full_name->printFullName() . ' (' . $full_name->printRole() . ')';
}
function _curl($url, $fields = array(), $method = 'GET')
{
	if ($method == 'GET') {
		try {
			if (count($fields)) $url = $url . '?' . http_build_query($fields);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
			$retValue = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
            dd($e->getMessage());
		}
	} else {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
		$retValue = curl_exec($ch);
		curl_close($ch);
	}
	return $retValue;
}

function SentMail($option = [])
{
	config(['mail.mailers.smtp.host' => $option['host']]);
	config(['mail.mailers.smtp.encryption' => $option['protocol']]);
	config(['mail.mailers.smtp.username' => $option['username']]);
	config(['mail.mailers.smtp.password' => $option['password']]);
	config(['mail.mailers.smtp.port' => $option['port']]);
	config(['mail.mailers.smtp.from' => $option['from_email']]);

	try {
		Mail::send([], [], function ($message) use ($option) {

			$message->to($option['sent_to'])
				->subject($option['subject'])
				->setBody($option['body'], 'text/html');
			$message->from($option['username'], $option['from_email']);
		});
		return "success";
	} catch (\Throwable $th) {
		return $th;
	}

}

function generateRandomPassword($length = 12) {
    // Define the characters that can be used in the password
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-';

    // Shuffle the characters to create randomness
    $shuffledChars = str_shuffle($characters);

    // Take the first $length characters to form the password
    $password = substr($shuffledChars, 0, $length);

    return $password;
}

function totalVisit(){
    return \App\Models\Leads::TotalVisits();
}
function completedVisit(){
    return \App\Models\Leads::completedVisit();
}

function pendingVisit(){
    return \App\Models\Leads::pendingVisit();
}
function totalTeam(){
    return \App\Models\User::totalTeam();
}

function pieChart(){
    $labels = ['Total Visit', 'completed Visit', 'Pending Visit'];
        $series = [
            [
                'value' => totalVisit(),
                'className' => 'bg-facebook',
            ],
            [
                'value' => completedVisit(),
                'className' => 'bg-twitter',
            ],
            [
                'value' => pendingVisit(),
                'className' => 'bg-google-plus',
            ],
        ];

    $data = [
        'labels' => $labels,
        'series' => $series,
    ];

    // Now you can use $data as needed, for example, convert it to JSON for JavaScript.
    return json_encode($data);

}

function barChart(){
    $months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    $records = \App\Models\Leads::select(
        DB::raw("DATE_FORMAT(created_at, '%b') as month"),

        DB::raw('SUM(CASE WHEN ti_status = "1" THEN 1 ELSE 0 END) as done'),
        DB::raw('SUM(CASE WHEN ti_status = "2" THEN 1 ELSE 0 END) as reject'),
        DB::raw('SUM(CASE WHEN ti_status = "0" THEN 1 ELSE 0 END) as pending'),
        DB::raw('COUNT(*) as total_records')
    )
            ->groupBy('month')
            ->orderBy(DB::raw("FIELD(month, '" . implode("','", $months) . "')"))
            ->get();
    // dd($records);
    $chartData = [
        'labels' => $records->pluck('month')->toArray(),
        'series' => [
            $records->pluck('done')->toArray(),
            $records->pluck('reject')->toArray(),
            $records->pluck('pending')->toArray(),
            $records->pluck('total_records')->toArray()
        ],
    ];
    // dd($chartData);
    return json_encode($chartData);
}

function isPublicRoutes(){
    $excludedRoutes = [
        'app.login',
        'app.loginsubmit',
        'app.logout',
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
        'api.login',
        'api.register'
    ];

    return in_array(routeCurrName(), $excludedRoutes);
}

function getEloquentSqlWithBindings($query)
{
    return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
}

function generateOTP(){
    return random_int(1000, 9999);
}
