<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


//testing purposes only
/*
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    var_dump($_SERVER);
    $_POST = json_decode(file_get_contents('php://input'), true);
    $token = $_POST['token'];
}
else{
    $token = $argv[1];
}
*/
$token = $argv[1];


require('/home/jlxc106/Website/site_watch/user_auth.php');

//http://httpd.apache.org/docs/current/logs.html#combined
$today = date("Y-m-d");

$access_path = "/home/jlxc106/log/$today.log";
$OUTPUT = [];
$myfile = fopen($access_path, "r") or die("Unable to open file!");

while(!feof($myfile)){
    $file_line = fgets($myfile);
    parse_line($file_line, $OUTPUT);
}

function parse_line($file_line, &$output){
    //ip
    $position_end_ip = stripos($file_line," ");
    $ip = substr($file_line, 0, $position_end_ip);
    //access date
    $position_start_date = stripos($file_line,"[");
    $position_end_date = stripos($file_line,"]");
    $access_date = substr($file_line, $position_start_date+1, $position_end_date - $position_start_date -1);

    $file_line = substr($file_line, $position_end_date+3);
    //request
    $position_end_request = stripos($file_line,'"');
    $request = substr($file_line, 0, $position_end_request);

    $file_line = substr($file_line, $position_end_request + 2);
    //network status
    $position_end_status = stripos($file_line," ");
    $status = substr($file_line, 0, $position_end_status);

    $file_line = substr($file_line, $position_end_status + 1);
    //file size
    $position_end_size = stripos($file_line," ");
    $size = substr($file_line, 0, $position_end_size);

    $file_line = substr($file_line, $position_end_size + 1);
    //referrer
    $position_end_referrer = stripos($file_line," ");
    $referrer = substr($file_line, 1, $position_end_referrer-2);
    //user agent
    $user_agent = substr($file_line, $position_end_referrer + 2, -2);

    array_push($output , (object)["ip" => $ip, "access date" => $access_date, "request" => $request, "status" => $status, "size" => $size, "referrer"=> $referrer, "user agent" => $user_agent]);
}

$outputJSON = json_encode($OUTPUT);
$make_file = fopen("/home/jlxc106/Website/site_watch/logs/$today.log","w") or die("Unable to create new file");
fwrite($make_file, $outputJSON);
fclose($make_file);
fclose($myfile);
exit();

?>

