<?php
if (!defined('ABSPATH')) {
    exit;
}

class GmsLogger {

    private $log_file;
    private static $instance;

    public function __construct() {
        $this->log_file = plugin_dir_path(__FILE__) . 'gms.log';
        $this->ensure_log_file_exists();
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function ensure_log_file_exists() {
        try {
            if (!file_exists($this->log_file)) {
                file_put_contents($this->log_file, '');
                chmod($this->log_file, 0664);
            }
        } catch (\Throwable $e) {
            // Ignore Error
        }
    }

    public function log($logInput, $title = '') {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $log_message = $title ? $title . ': ' : '';

            if (is_string($logInput) || is_numeric($logInput)) {
                $log_message .= var_export($logInput, true);
            } elseif (is_bool($logInput)) {
                $log_message .= $logInput ? 'bool:true' : 'bool:false';
            } elseif (is_array($logInput) || is_object($logInput)) {
                $log_message .= PHP_EOL . json_encode($logInput, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            $log_entry = $timestamp . ' - ' . $log_message . PHP_EOL;

            $this->write_log($log_entry);
        } catch (\Throwable $e) {
            // Ignore Error
        }
    }

    private function write_log($log_entry) {
        try {
            if (is_writable($this->log_file)) {
                file_put_contents($this->log_file, $log_entry, FILE_APPEND);
            }
        } catch (\Throwable $e) {
            // Ignore Error
        }
    }

}

/**
 * Wrapper function
 *
 * @param mixed  $logInput
 * @param string $title
 */
function ___($logInput, $title = '') {
    try {
        $logger = GmsLogger::getInstance();
        $logger->log($logInput, $title);
    } catch (\Throwable $e) {
        // Ignore Error
    }
}
