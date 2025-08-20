<?php

namespace app\Helpers;

use App\Models\AppointmentModel;
use App\Models\CrudModel;
// use App\Models\OrderModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use NumberFormatter;
use Illuminate\Support\Facades\Mail;

class login
{
    public static function checkForDash()
    {
        if (request()->cookie('Role') && request()->path() == '/') {
            abort(redirect('/dashboard'));
        }
    }
}
