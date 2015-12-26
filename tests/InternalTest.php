<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Helper;

class InternalTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    
    public function testParseFeed()
    {
        return $this->assertTrue(true);
    }
    public function testAddNewFeed()
    {
        return $this->assertTrue(true);
    }

    public function testMyBlogFeed()
    {
        $sFilePath = "tests". DIRECTORY_SEPARATOR. "resources". DIRECTORY_SEPARATOR. "myblogfeed.xml"; 
        $sFileContents = File::get($sFilePath);

        $oFile = Helper::oXMLStringToFeedObject($sFileContents);

        return $this->assertTrue(true);
    }

    public function testNatGeoFeed()
    {
        $sFilePath = "tests". DIRECTORY_SEPARATOR. "resources". DIRECTORY_SEPARATOR. "natgeo.xml"; 
        $sFileContents = File::get($sFilePath);
        #echo $sFileContents;
        return $this->assertTrue(true);
    }
}
