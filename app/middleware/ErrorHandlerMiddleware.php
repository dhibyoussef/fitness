<?php
class ErrorHandlerMiddleware {
    public function handle(array $request): void {
        // Register custom error and exception handlers
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void {
        // Log the error details with a structured format
        $this->logError("Error", $errno, $errstr, $errfile, $errline);
        // Display a user-friendly error page
        $this->displayErrorPage();
    }

    public function handleException(Throwable $exception): void {
        // Log the exception details with a structured format
        $this->logError("Exception", $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
        // Display a user-friendly error page
        $this->displayErrorPage();
    }

    private function logError(string $type, int $code, string $message, string $file, int $line): void {
        $logMessage = $this->formatLogMessage($type, $code, $message, $file, $line);
        error_log($logMessage);
    }

    private function formatLogMessage(string $type, int $code, string $message, string $file, int $line): string {
        return sprintf("%s [%d]: %s in %s on line %d", $type, $code, $message, $file, $line);
    }

    private function displayErrorPage(): void {
        // Ensure the correct path to the error view and handle potential path issues
        $errorViewPath = realpath(dirname(__FILE__) . '/../views/error.php');
        if (file_exists($errorViewPath)) {
            include $errorViewPath;
        } else {
            echo "An error occurred, but the error page could not be displayed.";
        }
        exit();
    }
}
?>