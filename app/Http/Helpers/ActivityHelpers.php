<?php 


namespace App\Http\Helpers;

use App\Jobs\LogActivityJob;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;
use Illuminate\Support\Facades\Request as Req;


class ActivityHelpers 
{
    public static function LogActivityHelpers($activity, $data = [], $status) 
    {
        $user_id = Auth::guard('sanctum')->id();
        $device = request()->header('User-Agent');
        dispatch(new LogActivityJob($activity, $data, $status, $device, $user_id))->withoutDelay();
    }

    private static function getIP()
    {
        $response = Http::get('https://ipapi.co/json/');
        $dataIP = $response->json();
        return $dataIP;
    }

    public static function ipNonAPI($ip = null)
    {
        if ($ip === '127.0.0.1') {
            return Position::make([
                'countryName' => 'Local Host',
                'countryCode' => 'Local',
                'regionCode' => 'Lc',
                'regionName' => 'Local Host',
                'cityName' => 'Your Computer',
                'zipCode' => '-',
                'isoCode' => '-',
                'postalCode' => '-',
                'latitude' => '-',
                'longitude' => '-',
                'metroCode' => '-',
                'areaCode' => '-',
                'isp' => 'Your ISP',
                'ip' => '127.0.0.1',
                'error' => false,
                'driver' => 'Stevebauman\Location',
            ]);
        }

        $publicIP = trim(shell_exec("curl ifconfig.co"));

        $location = Location::get($publicIP);
        if (!$location) {
            return Position::make([
                'countryName' => 'Private Server',
                'countryCode' => 'Private Server',
                'regionCode' => '-',
                'regionName' => 'Unknown Region',
                'cityName' => 'Unknown City',
                'zipCode' => '-',
                'isoCode' => '-',
                'postalCode' => '-',
                'latitude' => '0',
                'longitude' => '0',
                'metroCode' => '-',
                'areaCode' => '-',
                'isp' => 'Unknown ISP',
                'ip' => $ip,
                'error' => false,
                'driver' => 'Stevebauman\Location',
            ]);
        }

        return $location;
    }

}