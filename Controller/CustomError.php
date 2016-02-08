<?php namespace Controller;

class CustomError
{
    public static function handleError(\Exception $e)
    {
        echo "<div style='color:red;'>Oops! Looks Like Something Went Wrong, Please Contact Administrator</div>";
        $file = fopen('xml.log', 'w');
        $error = $e->getMessage() . " ". date("Y-M-D H:i:s");
        fwrite($file, $error);
        fclose($file);
    }
}
