<?php

namespace App\Helpers;

class Logger
{
    protected static $logFile;

    public static function init($logFile = null)
    {
        self::$logFile = $logFile ?: base_path('debug.log');
    }

    public static function log($message, $type = 'INFO')
    {
        if (!self::$logFile) {
            self::init();
        }

        $timestamp = date('[Y-m-d H:i:s]');
        $logMessage = "{$timestamp} [{$type}] {$message}\n";
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function error($message)
    {
        self::log($message, 'ERROR');
    }

    public static function info($message)
    {
        self::log($message, 'INFO');
    }
} 