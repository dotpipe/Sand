<?php
    putenv("GNUPGHOME=/home/xiv/.gnupg");

    require_once("./wisephp/src/oauth2/crud.php");

    $decrypt_len = 512;
    $time = $timed = time();
    $decryptkey = "";
    srand($timed);

    for ($i = 0 ; $i < $decrypt_len ; $i++)
    {
        $next = rand(0,64);
        $enum_sleep[] = $next;
    }

    for ($i = 0 ; strlen($decryptkey) < $decrypt_len ; $i++)
    {
        $in = rand(hexdec("45"),hexdec("79"));
        $decryptkey .= decbin($in);
        //time_nanosleep(0,$enum_sleep[$i]);
        $in = srand($enum_sleep[$i]);
    }

    $first_key = $decryptkey;
    $decryptkey = "";
    $timed = $time;
    srand($timed);
    for ($i = 0 ; strlen($decryptkey) < $decrypt_len ; $i++)
    {
        $in = rand(hexdec("45"),hexdec("79"));
        $decryptkey .= decbin($in);
        //time_nanosleep(0,$enum_sleep[$i]);
        $in = srand($enum_sleep[$i]);
    }

    while ($decryptkey != $first_key)
    {
        $decryptkey = substr($decryptkey,1);
        $first_key = substr($first_key,1);
    }

    $bytestring = "";
    $i = 0;
    $dictionary = "0987654321abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+";

    while (strlen($decryptkey) > $i)
    {
        $bytestring .= $dictionary[bindec(substr($decryptkey,$i,6))-1];
        $i+=6;
    }

    echo $bytestring . " ";// . ($decryptkey == $first_key) . "<br> " . $first_key;
    echo "<br>";
    echo $decryptkey . " " . ($decryptkey == $first_key) . "<br> " . $first_key;

    $res = gnupg_init(["file_name" => "/usr/bin/gpg-agent", "home_dir" => "/home/xiv/.gnupg"]);
    gnupg_addencryptkey($res,'4D9A14863066ADBC53A7A3FC34B8078EA4A5C0C9');
    gnupg_adddecryptkey($res,"$bytestring",'UserEnteredPassword');
    $example = gnupg_encrypt($res,'JSON CONTAINING ALL DETAILS OF INFORMATION');
    echo $example;

    file_put_contents("configchain.json",["host"=>"https://db5010176958.hosting-data.io","database"=>"dbs8626206","port"=>3306,"username"=>"root","password"=>"RTYfGhVbN!3$"]);
    
    $transid = "";
    while (strlen($decryptkey) > $i)
    {
        $transid .= $dictionary[bindec(substr($decryptkey,$i,6))-1];
        $i+=6;
    }

    $json_string = [
        "transid" => $transid,
        "time" => time(),
        "amount" => $_COOKIE['amount'],
        "currency" => $_COOKIE['currency'],
        "creator" => $json["id"],
        "fee" => $json["fee"]
    ];

    $weight = 0;

    for ($json_string as $key => $val)
    {
        $weight += strlen($key) + strlen($val);
    }

    $crud = new CRUD("configchain.json");

    $crud->create([
        "id" => time(),
        "username" => $_COOKIE['user'],
        "password" => $_POST['pass'],
        "version" => "1.0",
        "weight" => $weight,
        "transid" => $transid,
        "time" => time(),
        "PGP" => $example,
        "amount" => $_COOKIE['amount'],
        "currency" => $_COOKIE['currency'],
        "creator" => $json["id"],
        "fee" => $json["fee"],
        "tax" => "1"
    ], "pgp_item");
?>
