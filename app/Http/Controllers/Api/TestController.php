<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiBaseController;


class TestController extends ApiBaseController
{
    public function indexAction()
    {
        echo 'This is Api Test Page';
    }
}