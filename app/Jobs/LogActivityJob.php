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
use Illuminate\Support\Facades\Request as Req;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activity, $data, $status;

    public function __construct($activity, $data, $status)
    {
        $this->activity = $activity;
        $this->data = $data;
        $this->status = $status;
    }

    public function handle()
    {
        LogActivity::create([
            'user_id' => Auth::id() ?? null,
            'ip_detail' => json_encode(ActivityHelpers::ipNonAPI(), true),
            'device' => Req::header('User-Agent'),
            'activity' => $this->activity,
            'status' => $this->status,
            'detail' => json_encode($this->data, true),
        ]);
    }
}
