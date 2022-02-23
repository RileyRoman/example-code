<?php
	
function dump($data) {
    if(is_array($data)) { //If the given variable is an array, print using the print_r function.
        print "<pre>-----------------------\n";
        print_r($data);
        print "-----------------------</pre>";
    } elseif (is_object($data)) {
        print "<pre>==========================\n";
        var_dump($data);
        print "===========================</pre>";
    } else {
        print "=========&gt; ";
        var_dump($data);
        print " &lt;=========";
    }
}

function dd($data){
    dump($data);
    die();
}

function containString($string, $array_match)
{
    foreach ($array_match as $compare_string) {
        if (is_numeric(strpos($string, $compare_string))) {
            return $compare_string;
        }
    }

    return false;
}

function containAllString($string, $array_match)
{
    foreach ($array_match as $compare_string) {
        if (!is_numeric(strpos($string, $compare_string))) {
            return false;
        }
    }

    return true;
}

function asset($path){

    if($path[0] == '/'){
        $path = substr($path, 1);
    }

    return APP_DOMAIN.$path;
}

function dateFormat($stringDate, $format='d-m-Y'){
    $date=date_create($stringDate);
    return date_format($date,$format);
}