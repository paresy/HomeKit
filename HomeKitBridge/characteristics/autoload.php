<?

foreach (glob(__DIR__ . "/*.php") as $filename)
{
    if($filename != "autoload.php")
        include_once $filename;
}