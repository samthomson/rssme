<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Carbon\Carbon;
use App\Auto\Task;
use App\Http\Controllers\Feeds;

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
        $iSecondsCutOff = 40;
        $bJobsRemain = true;

        // while less than a minute (or part there of) has past, keep pulling a task to process

        while($cdStarted->diffInSeconds(Carbon::now()) < $iSecondsCutOff && $bJobsRemain){
            //echo $cdStarted->diffInSeconds(Carbon::now()), "<br/>";

            $tJobToProcess = Task::next();
            // if no more jobs, escape loop
            if(!isset($tJobToProcess)){
                $bJobsRemain = false;
            }else{
                echo "process item: ", $tJobToProcess->id, "<br/>";

                switch($tJobToProcess->job)
                {   
                    case "pull-feed":
                        $iFeedId = (int)$tJobToProcess->detail;
                        Feeds::pullFeed($iFeedId);
                        $tJobToProcess->delete();
                        // and reschedule for fifteen minbutes
                        Feeds::scheduleFeedPull($iFeedId, 15);
                        break;
                    case "scrape-feed-item-image":
                        Feeds::scrapeThumbFromFeedItem((int)$tJobToProcess->detail);
                        $tJobToProcess->delete();
                        break;
                }
            }
        }
        echo "no more tasks available", "<br/>";
    }
}
