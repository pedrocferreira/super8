<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Helpers\Logger;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            try {
                Logger::error($e->getMessage());
                Logger::error("File: " . $e->getFile() . ":" . $e->getLine());
                Logger::error("Stack trace: " . $e->getTraceAsString());
            } catch (\Exception $logException) {
                // Silenciosamente falha se não conseguir logar
            }
        });
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function report(Throwable $e)
    {
        if ($this->shouldReport($e)) {
            Logger::error($e->getMessage());
            Logger::error("File: " . $e->getFile() . ":" . $e->getLine());
            Logger::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
