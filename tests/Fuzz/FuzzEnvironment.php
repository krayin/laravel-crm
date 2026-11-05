<?php

namespace Tests\Fuzz;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

class FuzzEnvironment
{
    /**
     * Keep fuzzed requests inside the test: no mail, jobs, notifications, outbound HTTP, workflow
     * listeners or files on real disks.
     */
    public static function isolate(): void
    {
        Event::fake();
        Mail::fake();
        Notification::fake();
        Queue::fake();
        Http::fake();
        Storage::fake('local');
        Storage::fake('public');
    }

    /**
     * "500 TypeError: message (File.php:12)" for a failed response, naming the exception behind it.
     */
    public static function failure(TestResponse $response): string
    {
        $exception = $response->exception;

        if (! $exception) {
            return (string) $response->getStatusCode();
        }

        $message = preg_replace('/\s+/', ' ', $exception->getMessage());

        return $response->getStatusCode().' '.class_basename($exception).': '
            .(strlen($message) > 110 ? substr($message, 0, 107).'...' : $message)
            .' ('.basename($exception->getFile()).':'.$exception->getLine().')';
    }

    /**
     * Fail with every collected "input -> status" line so the report shows all failing inputs at once.
     */
    public static function report(string $route, array $failures): string
    {
        return "{$route} (FUZZ_SEED=".FuzzInput::seed().")\n  ".implode("\n  ", array_unique($failures));
    }
}
