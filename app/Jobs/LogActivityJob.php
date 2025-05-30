<?php

namespace App\Jobs;

use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\LogActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as Req;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activity, $data, $status, $device, $user_id;

    public function __construct($activity, $data, $status, $device, $user_id)
    {
        $this->device = $device;
        $this->user_id = $user_id;
        $this->activity = $activity;
        $this->data = $data;
        $this->status = $status;
    }

    public function handle()
    {
        Log::info('Job executed with data:', [
            'user_id' => $this->user_id,
            'device' => $this->device,
            'activity' => $this->activity,
            'status' => $this->status
        ]);

        LogActivity::create([
            'user_id' => 1,
            'ip_detail' => json_encode(ActivityHelpers::ipNonAPI(), true),
            'device' => $this->device,
            'activity' => $this->activity,
            'status' => $this->status,
            'detail' => json_encode($this->data, true),
        ]);
        
        Log::info('LogActivity created successfully');
    }
}
