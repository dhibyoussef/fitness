<?php
// app/controllers/ErrorHandlerMiddleware.php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ErrorHandlerMiddleware {
    private Logger $logger;

    public function __construct() {
        $this->logger = new Logger('ErrorHandler');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::ERROR));
    }

    public function handle(array $request): void {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void {
        $this->logError("Error", $errno, $errstr, $errfile, $errline);
        $this->displayErrorPage("Error [$errno]: $errstr");
    }

    public function handleException(Throwable $exception): void {
        $this->logError("Exception", $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
        $this->displayErrorPage("Exception: " . $exception->getMessage());
    }

    private function logError(string $type, int $code, string $message, string $file, int $line): void {
        $this->logger->error("$type [$code]: $message in $file on line $line", [
            'trace' => debug_backtrace()
        ]);
    }

    private function displayErrorPage(string $message): void {
        http_response_code(500);
        require_once __DIR__ . '/../views/error/error.php';
        exit;
    }
}