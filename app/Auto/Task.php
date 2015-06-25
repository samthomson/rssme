<?php

namespace App\Auto;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $table = 'schedule';


    public static function next()
    {
    	/* return next available item or null */

    	$oTask = Task::where('processFrom', '<', Carbon::now())->first();

    	return $oTask;
    }
}
