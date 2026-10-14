<?php

namespace Webkul\Admin\Services;

/**
 * Which delivery method is actually usable.
 *
 * The driver is named in `.env`, but naming one is not the same as having set it up. A socket
 * driver whose credentials are missing, or whose broadcast connection does not match, would
 * publish into the void and leave the bell silently dead — so the choice is resolved here once,
 * and anything not fully configured falls back to polling.
 *
 * Both the browser and the server read the driver through this class, so they cannot disagree
 * about which one is in use.
 */
class NotificationDriver
{
    const POLLING = 'polling';

    /**
     * The driver that will actually be used.
     */
    public static function resolve(): string
    {
        $driver = strtolower((string) config('notifications.driver', self::POLLING));

        if ($driver === self::POLLING || $driver === '') {
            return self::POLLING;
        }

        if (! self::isConfigured($driver)) {
            return self::POLLING;
        }

        return $driver;
    }

    /**
     * Whether a socket driver has everything it needs.
     */
    public static function isConfigured(string $driver): bool
    {
        $required = config('notifications.requires.'.$driver);

        /**
         * A driver nobody has described requirements for is not assumed to be ready.
         */
        if (! is_array($required)) {
            return false;
        }

        /**
         * Read through configuration rather than `env()`: once `config:cache` has been run — as it
         * is in production — `env()` returns null for everything, which would have forced a
         * correctly set up install back to polling.
         */
        foreach ($required as $path) {
            if (blank(config($path))) {
                return false;
            }
        }

        /**
         * The application must publish to the same service the browser is listening on. A socket
         * driver selected while broadcasting still points at `log` or `null` would record the
         * event locally and send nothing.
         */
        return config('broadcasting.default') === $driver;
    }

    /**
     * What the browser needs in order to open a socket, or null when it needs nothing.
     *
     * This is the only path by which a broadcasting value reaches the page. Nothing is baked into
     * the javascript bundle: the settings are rendered per request, so rotating a key or switching
     * driver takes effect on the next page load rather than requiring the assets to be rebuilt,
     * and a bundle served from a cache can never carry a stale or foreign key.
     *
     * Only what a client must present to subscribe is included. The application secret and id
     * authenticate the *server* when it publishes and are never needed to listen, so they are
     * absent by construction — whatever is returned here is readable by anyone who opens the page.
     */
    public static function clientConfig(): ?array
    {
        $driver = self::resolve();

        if ($driver === self::POLLING) {
            return null;
        }

        $key = config('broadcasting.connections.'.$driver.'.key');

        $options = config('broadcasting.connections.'.$driver.'.options', []);

        /**
         * A client needs the key, and then an address to reach: Reverb is given a host of its own,
         * while Pusher derives one from its cluster. Lacking both there is nothing to connect to,
         * so the page is given nothing and the bell polls rather than retrying a socket that was
         * never going to answer.
         */
        if (
            empty($key)
            || (empty($options['host']) && empty($options['cluster']))
        ) {
            return null;
        }

        return [
            'connection' => $driver,
            'key' => $key,
            'host' => $options['host'] ?? null,
            'port' => (int) ($options['port'] ?? 443),
            /**
             * Pusher describes its transport as `useTLS` and is always served over TLS; Reverb
             * carries a scheme instead, which is plain `http` on a local setup.
             */
            'forceTLS' => $options['useTLS'] ?? (($options['scheme'] ?? 'https') === 'https'),
            'cluster' => $options['cluster'] ?? null,
        ];
    }

    /**
     * Seconds between checks when polling, with the same floor the browser applies.
     */
    public static function pollInterval(): int
    {
        return max(5, (int) config('notifications.poll_interval', 30));
    }

    /**
     * Why a chosen driver is not being used, for the configuration screen. Null when the chosen
     * driver is the one in use.
     */
    public static function fallbackReason(): ?string
    {
        $chosen = strtolower((string) config('notifications.driver', self::POLLING));

        if ($chosen === self::POLLING || $chosen === '' || self::resolve() === $chosen) {
            return null;
        }

        $missing = [];

        foreach ((array) config('notifications.requires.'.$chosen, []) as $key => $path) {
            if (blank(config($path))) {
                $missing[] = $key;
            }
        }

        if ($missing) {
            return trans('admin::app.notifications.fallback.missing-keys', [
                'driver' => $chosen,
                'keys' => implode(', ', $missing),
            ]);
        }

        if (config('broadcasting.default') !== $chosen) {
            return trans('admin::app.notifications.fallback.broadcast-mismatch', [
                'driver' => $chosen,
                'broadcast' => (string) config('broadcasting.default'),
            ]);
        }

        return null;
    }
}
