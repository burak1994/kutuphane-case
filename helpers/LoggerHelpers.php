<?php 
 # helpers/LoggerHelpers.php
namespace Helpers;

class LoggerHelpers
{
    # Info Log
    public static function info($message)
    {
        self::writeLog($message, 'info.log');
    }

       # Warning Log
       public static function warning($message)
       {
           self::writeLog($message, 'warning.log');
       }
    
    # Error Log
    public static function error($message)
    {
        self::writeLog($message, 'error.log');
    }
    
    # Write Log to file
    private static function writeLog($message, $filename)
    {
        $date = date('Y-m-d H:i:s');
        $log = "[$date] $message" . PHP_EOL;
        
        $logDirectory = __DIR__ . '/../storage/';
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }
        $logFile = $logDirectory . $filename;
        
        
        file_put_contents($logFile, $log, FILE_APPEND);
    }
}




?>