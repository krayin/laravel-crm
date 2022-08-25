import Vue from 'vue/dist/vue.js';
import draggable from 'vuedraggable';
import VueTimeago from 'vue-timeago';
import VeeValidate from 'vee-validate';
import VueKanban from 'vue-kanban';
import VueCal from 'vue-cal';

import 'vue-cal/dist/vuecal.css'

import './bootstrap';

/**
 * Lang imports.
 */
 import ar from 'vee-validate/dist/locale/ar';
 import de from 'vee-validate/dist/locale/de';
 import es from 'vee-validate/dist/locale/es';
 import fa from 'vee-validate/dist/locale/fa';
 import fr from 'vee-validate/dist/locale/fr';
 import nl from 'vee-validate/dist/locale/nl';
 import tr from 'vee-validate/dist/locale/tr';
 import hi_IN from 'vee-validate/dist/locale/hi';
 import zh_CN from 'vee-validate/dist/locale/zh_CN';

 import 'vue-cal/dist/i18n/ar.js';
 import 'vue-cal/dist/i18n/tr.js';
 

window.moment = require('moment');

window.Vue = Vue;
window.VeeValidate = VeeValidate;

Vue.use(VeeValidate, {
    dictionary: {
        ar: ar,
        de: de,
        es: es,
        fa: fa,
        fr: fr,
        nl: nl,
        tr: tr,
        hi_IN: hi_IN,
        zh_CN: zh_CN
    },
    events: 'input|change|blur'
});

Vue.prototype.$http = axios;

window.eventBus = new Vue();

Vue.use(VueKanban);

Vue.use(VueTimeago, {
    name: 'Timeago',
    locale: 'en',
    locales: {
        'ar': require('date-fns/locale/ar'),
        'tr': require('date-fns/locale/tr')
    }
})

Vue.component('draggable', draggable);

Vue.component('vue-cal', VueCal);

$(function() {
    let app = new Vue({
        el: "#app",

        data: function () {
            return {
                pageLoaded: false,

                modalIds: {},

                isMenuOpen: localStorage.getItem('crm-sidebar') == 'true',
            }
        },

        mounted() {
            this.$validator.localize(document.documentElement.lang);
            setTimeout(() => {
                this.pageLoaded = true;

                this.disableAutoComplete();
            });

            this.addServerErrors();

            this.addFlashMessages();

            window.addFlashMessages = flash => {
                const flashes = this.$refs.flashes;

                flashes.addFlash(flash);
            }
        },

        methods: {
            onSubmit: function (e, formScope = '') {
                this.toggleButtonDisable(true);

                if (typeof tinyMCE !== 'undefined') {
                    tinyMCE.triggerSave();
                }

                this.$validator.validateAll(formScope || null)
                    .then(result => {
                        if (result) {
                            e.target.submit();
                        } else {
                            this.toggleButtonDisable(false);

                            eventBus.$emit('onFormError')
                        }
                    });
            },

            toggleButtonDisable (value) {
                var buttons = document.getElementsByTagName("button");

                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].disabled = value;
                }
            },

            addServerErrors(scope = null) {
                for (var key in serverErrors) {
                    var inputNames = [];
                    
                    key.split('.').forEach(function(chunk, index) {
                        if(index) {
                            inputNames.push('[' + chunk + ']')
                        } else {
                            inputNames.push(chunk)
                        }
                    })

                    var inputName = inputNames.join('');

                    const field = this.$validator.fields.find({
                        name: inputName,
                        scope: scope
                    });

                    if (field) {
                        this.$validator.errors.add({
                            id: field.id,
                            field: inputName,
                            msg: serverErrors[key][0],
                            scope: scope
                        });
                    }
                }
            },

            addFlashMessages() {
                if (typeof flashMessages == 'undefined') {
                    return;
                };

                const flashes = this.$refs.flashes;

                flashMessages.forEach(function(flash) {
                    flashes.addFlash(flash);
                }, this);
            },

            openModal(id) {
                this.$set(this.modalIds, id, true);

                this.disableAutoComplete();
            },

            closeModal(id) {
                this.$set(this.modalIds, id, false);
            },

            toggleMenu() {
                this.isMenuOpen = ! this.isMenuOpen;

                localStorage.setItem('crm-sidebar', this.isMenuOpen);
            },

            disableAutoComplete: function () {
                queueMicrotask(() => {
                    $('.date-container input').attr('autocomplete', 'off');
                    $('.datetime-container input').attr('autocomplete', 'off');
                });
            }
        }
    });

    window.app = app;
});
