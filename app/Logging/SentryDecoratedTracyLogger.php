<?php declare (strict_types=1);

namespace App\Logging;

use Nette\Application\BadRequestException;
use Sentry\Severity;
use Throwable;
use Tracy\Debugger;
use Tracy\ILogger;
use function Sentry\captureException;
use function Sentry\captureMessage;
use function Sentry\init;

/**
 * Docs: https://www.lukashron.cz/nette-sentry.html
 * Class SentryDecoratedTracyLogger
 * @package App\Logging
 */
final class SentryDecoratedTracyLogger implements ILogger
{
    private ILogger $parentLogger;

    /**
     * SentryDecoratedTracyLogger constructor.
     * @param array $sentryOptions
     */
    public function __construct(array $sentryOptions)
    {
        $this->parentLogger = Debugger::getLogger();

        init($sentryOptions);
    }

    /**
     * @param string|Throwable $value
     * @param string $priority
     */
    public function log($value, $priority = self::INFO): void
    {
        // Logging to default Tracy logger
        $this->parentLogger->log($value, $priority);

        // Logging to Sentry
        $this->logToSentry($value, $priority);
    }

    /**
     * @param Throwable|string $value
     * @param $priority
     */
    private function logToSentry($value, $priority): void
    {
        $severity = $this->getSeverityFromPriority($priority);

        if (! $severity || $value instanceof BadRequestException) {
            // do not log 404s to sentry
            return;
        }
        if ($value instanceof Throwable) {
            captureException($value);
        } else {
            captureMessage($value, $severity);
        }
    }

    /**
     * @param string $priority
     * @return Severity|null
     */
    private function getSeverityFromPriority(string $priority): ?Severity
    {
        switch ($priority) {
            case ILogger::DEBUG:
                return Severity::debug();

            case ILogger::INFO:
                return Severity::info();

            case ILogger::WARNING:
                return Severity::warning();

            case ILogger::ERROR:
            case ILogger::EXCEPTION:
                return Severity::error();

            case ILogger::CRITICAL:
                return Severity::fatal();

            default:
                return null;
        }
    }
}