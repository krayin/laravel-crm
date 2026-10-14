/**
 * Laravel Echo subscribes to the events the application broadcasts.
 *
 * The connection settings are read from a meta tag the server renders rather than from build-time
 * environment variables, so nothing is baked into this bundle: rotating a key or switching driver
 * takes effect on the next page load, and a cached bundle can never carry a stale key.
 *
 * The tag is only rendered when a socket driver is fully configured, so its absence is the normal
 * case — the default delivery method is polling, which needs no socket at all. `window.Echo` is
 * then left undefined and the notification bell falls back to polling.
 */
import Echo from "laravel-echo";
import Pusher from "pusher-js";

const settings = document
    .querySelector('meta[name="broadcasting"]')
    ?.getAttribute("content");

if (settings) {
    const config = JSON.parse(settings);

    /**
     * Both drivers speak the Pusher protocol, so the client is the same either way; only the
     * connection details differ.
     */
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: config.connection,
        key: config.key,
        cluster: config.cluster ?? "mt1",
        wsHost: config.host,
        wsPort: config.port,
        wssPort: config.port,
        forceTLS: config.forceTLS,
        enabledTransports: ["ws", "wss"],
    });
}
