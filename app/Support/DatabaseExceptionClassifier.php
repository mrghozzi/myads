<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Throwable;

final class DatabaseExceptionClassifier
{
    private const RECOVERABLE_MARKERS = [
        'marked as crashed and should be repaired',
        'no connection could be made because the target machine actively refused it',
        'connection refused',
        'server has gone away',
        'lost connection',
    ];

    private function __construct()
    {
    }

    public static function shouldRenderServiceUnavailable(Throwable $exception): bool
    {
        if (! self::containsQueryException($exception)) {
            return false;
        }

        $messageChain = strtolower(self::messageChain($exception));

        foreach (self::RECOVERABLE_MARKERS as $marker) {
            if (str_contains($messageChain, $marker)) {
                return true;
            }
        }

        return false;
    }

    private static function containsQueryException(Throwable $exception): bool
    {
        for ($current = $exception; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof QueryException) {
                return true;
            }
        }

        return false;
    }

    private static function messageChain(Throwable $exception): string
    {
        $messages = [];

        for ($current = $exception; $current !== null; $current = $current->getPrevious()) {
            $messages[] = $current->getMessage();
        }

        return implode(' | ', $messages);
    }
}
