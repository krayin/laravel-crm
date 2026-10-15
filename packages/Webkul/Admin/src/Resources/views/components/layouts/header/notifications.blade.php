{{--
    The notification bell.

    Notifications are written per user when something happens, so this only ever reads the acting
    user's own feed — the scoping decision was already made server-side and is not repeated here.
--}}
@php
    use Webkul\Admin\Services\NotificationDriver;

    $notificationsEnabled = (bool) core()->getConfigData('general.settings.notifications.enabled');

    /**
     * Resolved rather than read straight from configuration: a socket driver that is named but
     * not fully set up falls back to polling, so the bell is never left waiting on a connection
     * that was never going to arrive.
     */
    $driver = NotificationDriver::resolve();

    $pollInterval = NotificationDriver::pollInterval();

    $sound = (bool) core()->getConfigData('general.settings.notifications.sound');
@endphp

@if ($notificationsEnabled)
    <v-notifications
        driver="{{ $driver }}"
        poll-interval="{{ $pollInterval }}"
        :sound="{{ $sound ? 'true' : 'false' }}"
        user-id="{{ auth()->guard('user')->id() }}"
    >
        <!-- Shimmer -->
        <div class="flex">
            <span class="icon-notification cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"></span>
        </div>
    </v-notifications>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-notifications-template"
        >
            <div class="relative">
                <!-- The bell, carrying the unread count. -->
                <div class="flex">
                    <span
                        class="icon-notification relative cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                        :class="{ 'bg-gray-100 dark:bg-gray-950': isOpen }"
                        @click.stop="toggle"
                    >
                        <!--
                            The count sits on the bell rather than inside the panel, so the number of
                            unread notifications is readable without opening anything.
                        -->
                        <span
                            class="absolute right-0 top-0 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold leading-none text-white"
                            v-if="unreadCount"
                            v-text="unreadCount > 99 ? '99+' : unreadCount"
                        ></span>
                    </span>
                </div>

                <!-- The panel. -->
                <!--
                    Arriving notifications announce themselves briefly, so one that lands while the
                    user is working elsewhere is noticed rather than only changing a count.

                    Teleported to the body: the header establishes a stacking context, and a toast
                    rendered inside it was painted beneath the page's own overlays.
                -->
                <Teleport to="body">
                    <div
                        class="fixed flex flex-col gap-3"
                        style="top: 70px; z-index: 10005; width: 340px; max-width: calc(100vw - 32px)"
                        :style="toastAnchor"
                    >
                        <transition-group
                            enter-from-class="opacity-0"
                            enter-active-class="transition duration-300 ease-out"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-from-class="opacity-100"
                            leave-active-class="transition duration-200 ease-out"
                            leave-to-class="opacity-0"
                        >
                            <div
                                class="flex cursor-pointer items-center gap-3 rounded-md px-4 py-3 shadow-lg"
                                style="background-color: #4CAF27"
                                v-for="toast in toasts"
                                :key="toast.id"
                                @click="openToast(toast)"
                            >
                                <!-- A filled mark, so the toast reads as an event rather than a warning. -->
                                <span
                                    class="icon-tick flex shrink-0 items-center justify-center rounded-full bg-white text-xl"
                                    style="width: 24px; height: 24px; color: #4CAF27"
                                ></span>

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <p
                                        class="truncate text-sm font-semibold text-white"
                                        v-text="toast.title"
                                    ></p>

                                    <p
                                        class="truncate text-sm text-white"
                                        style="opacity: 0.9"
                                        v-if="toast.description"
                                        v-text="toast.description"
                                    ></p>
                                </div>

                                <span
                                    class="icon-cross-large shrink-0 cursor-pointer text-xl text-white"
                                    @click.stop="dismiss(toast)"
                                ></span>
                            </div>
                        </transition-group>
                    </div>
                </Teleport>

                <!--
                    On a narrow screen the panel is pinned to the viewport rather than to the bell.
                    Anchored to the bell it kept its own width and ran off the left edge, cutting
                    off the start of every line. The positioning is inline because Krayin's CSS is
                    pre-built and carries no responsive position utilities.
                -->
                <div
                    class="absolute top-full z-[1000] mt-1 flex max-h-[420px] w-[360px] flex-col overflow-hidden rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 ltr:right-0 rtl:left-0"
                    :style="panelStyle"
                    v-if="isOpen"
                    @click.stop
                >
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
                        <p class="truncate text-base font-semibold dark:text-white">
                            @lang('admin::app.notifications.title')
                        </p>

                        <button
                            type="button"
                            class="shrink-0 cursor-pointer text-sm text-brandColor transition-all hover:underline"
                            v-if="unreadCount"
                            @click="markAllAsRead"
                        >
                            @lang('admin::app.notifications.mark-all-as-read')
                        </button>
                    </div>

                    <!-- The feed. -->
                    <div
                        class="flex flex-col overflow-y-auto"
                        v-if="notifications.length"
                    >
                        <!--
                            An unread notification is marked read as it is opened, so the count
                            reflects what has actually been looked at.
                        -->
                        <component
                            class="flex cursor-pointer gap-3 border-b border-gray-200 px-4 py-3 last:border-none hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                            :is="notification.route ? 'a' : 'div'"
                            :href="notification.route"
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click="open(notification)"
                        >
                            <!--
                                `min-w-0` lets this column shrink below its content. A flex item
                                defaults to `min-width: auto`, so a long record name held the row
                                open past the panel's width and `truncate` never took effect,
                                leaving the panel scrolling sideways.
                            -->
                            <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                                <p
                                    class="truncate text-sm font-semibold text-gray-800 dark:text-white"
                                    v-text="notification.title"
                                ></p>

                                <p
                                    class="truncate text-sm text-gray-600 dark:text-gray-300"
                                    v-if="notification.description"
                                    v-text="notification.description"
                                ></p>

                                <p class="truncate text-xs text-gray-400">
                                    <span v-if="notification.causer" v-text="notification.causer + ' · '"></span>
                                    <span v-text="notification.created_at"></span>
                                </p>
                            </div>

                            <!--
                                Unread is marked with a dot rather than a background tint, so the
                                rows that still need attention are countable at a glance. The size
                                is inline because Krayin's CSS is pre-built and carries no utility
                                at this scale.
                            -->
                            <span
                                class="shrink-0 rounded-full bg-brandColor"
                                style="width: 8px; height: 8px; margin-top: 6px"
                                v-if="! notification.is_read"
                            ></span>
                        </component>
                    </div>

                    <!-- Nothing yet. -->
                    <div
                        class="flex flex-col items-center gap-2 px-4 py-10"
                        v-else
                    >
                        <span class="icon-notification text-4xl text-gray-300 dark:text-gray-600"></span>

                        <p class="text-gray-400">
                            @lang('admin::app.notifications.empty')
                        </p>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-notifications', {
                template: '#v-notifications-template',

                props: {
                    driver: {
                        type: String,
                        default: 'polling',
                    },

                    pollInterval: {
                        type: [Number, String],
                        default: 30,
                    },

                    sound: {
                        type: Boolean,
                        default: false,
                    },

                    userId: {
                        type: [Number, String],
                        default: null,
                    },
                },

                data() {
                    return {
                        isOpen: false,

                        notifications: [],

                        unreadCount: 0,

                        timer: null,

                        viewportWidth: window.innerWidth,

                        /**
                         * Notifications currently announcing themselves on screen. Kept apart from
                         * the feed so dismissing a toast does not mark anything read.
                         */
                        toasts: [],

                        /**
                         * Ids already announced, so a notification seen again on a later poll does
                         * not pop a second time.
                         */
                        announced: new Set(),
                    };
                },

                computed: {
                    /**
                     * Toasts sit against the side the interface reads towards, so they appear where
                     * the eye already is rather than across the page from it.
                     */
                    toastAnchor() {
                        return document.documentElement.dir === 'rtl'
                            ? { left: '16px' }
                            : { right: '16px' };
                    },

                    /**
                     * Below Tailwind's `sm` breakpoint the panel spans the viewport with a small
                     * gutter instead of hanging off the bell, which is near the right edge and
                     * would otherwise push the panel past the left of the screen.
                     */
                    panelStyle() {
                        if (this.viewportWidth >= 640) {
                            return {};
                        }

                        return {
                            position: 'fixed',
                            top: '60px',
                            left: '8px',
                            right: '8px',
                            width: 'auto',
                        };
                    },
                },

                mounted() {
                    this.fetch();

                    this.listen();

                    window.addEventListener('click', this.handleClickOutside);

                    window.addEventListener('resize', this.handleResize);
                },

                beforeUnmount() {
                    window.removeEventListener('click', this.handleClickOutside);

                    window.removeEventListener('resize', this.handleResize);

                    if (this.timer) {
                        clearInterval(this.timer);
                    }
                },

                methods: {
                    /**
                     * Start receiving notifications by whichever means the admin has configured.
                     *
                     * Polling asks on a timer and needs nothing else running. Reverb and Pusher
                     * both speak through Laravel Echo, so they are started the same way and differ
                     * only in the connection the application was built with.
                     */
                    listen() {
                        if (this.driver === 'polling') {
                            this.timer = setInterval(this.fetch, Math.max(5, Number(this.pollInterval)) * 1000);

                            return;
                        }

                        /**
                         * Echo is only present once the admin has installed and built one of the
                         * socket drivers. Without it the bell falls back to asking on a timer
                         * rather than silently never updating.
                         */
                        if (! window.Echo) {
                            this.timer = setInterval(this.fetch, Math.max(5, Number(this.pollInterval)) * 1000);

                            return;
                        }

                        window.Echo.private(`notifications.${this.userId}`)
                            .listen('.notification.created', (payload) => {
                                this.notifications.unshift({ ...payload, is_read: false });

                                this.unreadCount += 1;

                                this.announce();

                                this.toast(payload);
                            });
                    },

                    fetch() {
                        this.$axios.get("{{ route('admin.notifications.index') }}")
                            .then(response => {
                                const previous = this.unreadCount;

                                const firstLoad = ! this.announced.size;

                                this.notifications = response.data.data;

                                this.unreadCount = response.data.meta.unread_count;

                                /**
                                 * Only a rise in the count is worth announcing; re-reading the same
                                 * unread notifications on the next poll is not new.
                                 */
                                if (this.unreadCount > previous) {
                                    this.announce();
                                }

                                /**
                                 * What is already waiting when the page opens is not news, so the
                                 * first response seeds the record of what has been seen without
                                 * announcing any of it. Afterwards, anything unseen is shown.
                                 */
                                this.notifications.forEach(notification => {
                                    if (this.announced.has(notification.id)) {
                                        return;
                                    }

                                    this.announced.add(notification.id);

                                    if (! firstLoad && ! notification.is_read) {
                                        this.toast(notification);
                                    }
                                });
                            })
                            .catch(() => {});
                    },

                    /**
                     * Show a notification briefly, then let it go.
                     *
                     * Only a few are kept on screen at once: a burst of records created together
                     * would otherwise stack toasts down the whole page and cover the work beneath.
                     */
                    toast(notification) {
                        this.announced.add(notification.id);

                        this.toasts.push(notification);

                        if (this.toasts.length > 3) {
                            this.toasts.splice(0, this.toasts.length - 3);
                        }

                        setTimeout(() => this.dismiss(notification), 6000);
                    },

                    dismiss(toast) {
                        const index = this.toasts.findIndex(item => item.id === toast.id);

                        if (index !== -1) {
                            this.toasts.splice(index, 1);
                        }
                    },

                    /**
                     * Opening a toast does what opening the row in the panel does, so the two are
                     * not two different ways of reaching the same record.
                     */
                    openToast(toast) {
                        this.dismiss(toast);

                        const notification = this.notifications.find(item => item.id === toast.id);

                        if (notification && ! notification.is_read) {
                            this.markAsRead(notification);
                        }

                        if (toast.route) {
                            window.location.href = toast.route;
                        }
                    },

                    toggle() {
                        this.isOpen = ! this.isOpen;

                        if (this.isOpen) {
                            this.fetch();
                        }
                    },

                    open(notification) {
                        if (! notification.is_read) {
                            this.markAsRead(notification);
                        }
                    },

                    markAsRead(notification) {
                        notification.is_read = true;

                        this.unreadCount = Math.max(0, this.unreadCount - 1);

                        this.$axios.post("{{ route('admin.notifications.read', 'replace') }}".replace('replace', notification.id))
                            .catch(() => {});
                    },

                    markAllAsRead() {
                        this.notifications.forEach(notification => notification.is_read = true);

                        this.unreadCount = 0;

                        this.$axios.post("{{ route('admin.notifications.read_all') }}")
                            .catch(() => {});
                    },

                    /**
                     * The sound is a short tone built in the browser rather than an audio file, so
                     * the feature ships without an asset to serve and cache.
                     */
                    announce() {
                        if (! this.sound) {
                            return;
                        }

                        try {
                            const context = new (window.AudioContext || window.webkitAudioContext)();

                            const oscillator = context.createOscillator();

                            const gain = context.createGain();

                            oscillator.connect(gain);

                            gain.connect(context.destination);

                            oscillator.frequency.value = 880;

                            gain.gain.setValueAtTime(0.0001, context.currentTime);

                            gain.gain.exponentialRampToValueAtTime(0.08, context.currentTime + 0.01);

                            gain.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + 0.3);

                            oscillator.start();

                            oscillator.stop(context.currentTime + 0.3);
                        } catch (error) {
                            // A browser that blocks audio until the page is interacted with is not
                            // a reason for the notification itself to fail.
                        }
                    },

                    handleResize() {
                        this.viewportWidth = window.innerWidth;
                    },

                    handleClickOutside(e) {
                        if (! this.$el.contains(e.target)) {
                            this.isOpen = false;
                        }
                    },
                },
            });
        </script>
    @endPushOnce
@endif
