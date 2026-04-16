/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Main vue bundler.
 */
import { createApp } from "vue/dist/vue.esm-bundler";

/**
 * Main root application registry.
 */
window.app = createApp({
    data() {
        return {
            isMenuActive: false,

            hoveringMenu: '',

            lastScrollY: 0,
        };
    },

    created() {
        window.addEventListener('click', this.handleFocusOut);
        this.lastScrollY = Math.max((window.scrollY ?? window.pageYOffset ?? 0), 0);
        document.body.classList.remove('is-scrolling-down');
        window.addEventListener('scroll', this.handleScrollDirection, { passive: true });

        // Keep initial load in normal state when page is at/near top.
        this.handleScrollDirection();
    },

    beforeDestroy() {
        window.removeEventListener('click', this.handleFocusOut);
        window.removeEventListener('scroll', this.handleScrollDirection);
        document.body.classList.remove('is-scrolling-down');
    },

    beforeUnmount() {
        window.removeEventListener('click', this.handleFocusOut);
        window.removeEventListener('scroll', this.handleScrollDirection);
        document.body.classList.remove('is-scrolling-down');
    },

    methods: {
        onSubmit() {},

        onInvalidSubmit({ values, errors, results }) {
            setTimeout(() => {
                const errorKeys = Object.entries(errors)
                    .map(([key, value]) => ({ key, value }))
                    .filter(error => error["value"].length);

                let firstErrorElement = document.querySelector('[name="' + errorKeys[0]["key"] + '"]');

                firstErrorElement.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }, 100);
        },

        handleMouseOver(event) {
            if (this.isMenuActive) {
                return;
            }

            const parentElement = event.currentTarget.parentElement;

            if (parentElement.classList.contains('sidebar-collapsed')) {
                parentElement.classList.remove('sidebar-collapsed');

                parentElement.classList.add('sidebar-not-collapsed');
            }

        },

        handleMouseLeave(event) {
            if (this.isMenuActive) {
                return;
            }

            const parentElement = event.currentTarget.parentElement;

            if (parentElement.classList.contains('sidebar-not-collapsed')) {
                parentElement.classList.remove('sidebar-not-collapsed');

                parentElement.classList.add('sidebar-collapsed');
            }
        },

        handleFocusOut(event) {
            const sidebar = this.$refs.sidebar;

            if (
                sidebar &&
                !sidebar.contains(event.target)
            ) {
                this.isMenuActive = false;

                const parentElement = sidebar.parentElement;

                if (parentElement.classList.contains('sidebar-not-collapsed')) {
                    parentElement.classList.remove('sidebar-not-collapsed');

                    parentElement.classList.add('sidebar-collapsed');
                }
            }
        },

        handleScrollDirection() {
            const topThreshold = 0;
            const currentScrollY = Math.max((window.scrollY ?? window.pageYOffset ?? 0), 0);
            const scrollDelta = currentScrollY - this.lastScrollY;

            if (currentScrollY == topThreshold) {
                document.body.classList.remove('is-scrolling-down');
                this.lastScrollY = currentScrollY;

                return;
            }

            if (Math.abs(scrollDelta) < 2) {
                return;
            }

            if (scrollDelta > 0) {
                document.body.classList.add('is-scrolling-down');
            }

            this.lastScrollY = currentScrollY;
        },
    },
});

/**
 * Global plugins registration.
 */
import Admin from "./plugins/admin";
import Axios from "./plugins/axios";
import Emitter from "./plugins/emitter";
import Flatpickr from "./plugins/flatpickr";
import VeeValidate from "./plugins/vee-validate";
import CreateElement from "./plugins/createElement";
import Draggable from "./plugins/draggable";
import VueCal from "./plugins/vue-cal";

[
    Admin,
    Axios,
    Emitter,
    CreateElement,
    Draggable,
    Flatpickr,
    VeeValidate,
    VueCal,
].forEach((plugin) => app.use(plugin));

/**
 * Global directives.
 */
import Debounce from "./directives/debounce";
import DOMPurify from "./directives/dompurify";
import ToolTip from "./directives/tooltip";

app.directive("debounce", Debounce);
app.directive("safe-html", DOMPurify);
app.directive("tooltip", ToolTip);

export default app;

