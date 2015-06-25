<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Carbon\Carbon;
use App\Auto\Task;

class Auto extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function process()
    {
        /* hit every minute via laravel cron proxy */
        
        // start timer
        $cdStarted = Carbon::now();
        $iSecondsCutOff = 20;
        $bJobsRemain = true;

        // while less than a minute (or part there of) has past, keep pulling a task to process

        while($cdStarted->diffInSeconds(Carbon::now()) < $iSecondsCutOff && $bJobsRemain){
            echo $cdStarted->diffInSeconds(Carbon::now()), "<br/>";

            $tJobToProcess = Task::next();
            // if no more jobs, escape loop
            if(!isset($tJobToProcess)){
                $bJobsRemain = false;
            }else{
                echo "process item: ", $tJobToProcess->id, "<br/>";
            }
        }
        echo "no more tasks available", "<br/>";
    }
}
