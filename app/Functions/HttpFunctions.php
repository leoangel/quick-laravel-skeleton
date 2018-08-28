<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/11
 * Time: 13:04
 */

namespace App\Functions;


use GuzzleHttp\Client;

class HttpFunctions
{
    public static function post($url, $data)
    {

    }

    public static function get($url, $data)
    {
        $client = new Client(['timeout' => 5.0]);

        return $client->request('GET', $url, ['query' => $data])->getBody()->getContents();
    }
}