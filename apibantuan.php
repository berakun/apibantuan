<?php

date_default_timezone_set('Asia/Jakarta');

$json = json_decode(file_get_contents('php://input'), true);

function writeLog($content, $title = 'Log', $filename = 'logs')
{
    static $pid;

    $name = dirname(__FILE__) . "/logs/$filename";
    $name .= date('.Ymd.') . 'log';
    $f    = fopen($name, 'a');

    if (is_null($pid)) {
        $pid  = uniqid();
    }

    if (is_array($content) || is_object($content)) $content = json_encode($content, JSON_UNESCAPED_SLASHES);

    $time = microtime();
    $time = substr($time, 1, 7);
    $time = date('H:i:s') . $time;
    $content  = "$time $pid $title: $content\r\n";

    flock($f, LOCK_EX);
    fwrite($f, $content);
    flock($f, LOCK_UN);
}

function getData($data, $url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => []
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}


function postData($data, $url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => []
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

$baseUrl = "http://localhost:8000/api";

if ($json["accessToken"] == "6a8becf3-1c0n-4d5b-8523-ab30a1b5a3ae") {
    if ($json["action"] == "getdata") {
        $data = "";
        $url = "$baseUrl/post/books";
        $result = getData($data, $url);
        writeLog("result send to : $url | data : $data | result : $result", $json["action"]);
        echo $result;
    } elseif ($json["action"] == "postdata") {
        $data = "";
        $url = "$baseUrl/post/books";
        $result = PostData($data, $url);
        writeLog("result send to : $url | data : $data | result : $result", $json["action"]);
        echo $result;
    } else {
        $response = array(
            "status" => "failed",
            "message" => "Please input valid action"
        );
        echo json_encode($response);
    }
} else {
    $response = array(
        "status" => "failed",
        "message" => "Please input valid accessToken"
    );
    echo json_encode($response);
}
