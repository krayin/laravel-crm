/**
 * We are defining all the global rules here and configuring
 * all the `vee-validate` settings.
 */
import { configure, defineRule, Field, Form, ErrorMessage } from "vee-validate";
import { localize, setLocale } from "@vee-validate/i18n";
import ar from "@vee-validate/i18n/dist/locale/ar.json";
import bn from "@vee-validate/i18n/dist/locale/bn.json";
import de from "@vee-validate/i18n/dist/locale/de.json";
import en from "@vee-validate/i18n/dist/locale/en.json";
import es from "@vee-validate/i18n/dist/locale/es.json";
import fa from "@vee-validate/i18n/dist/locale/fa.json";
import fr from "@vee-validate/i18n/dist/locale/fr.json";
import he from "@vee-validate/i18n/dist/locale/he.json";
import hi_IN from "../../locales/hi_IN.json";
import it from "@vee-validate/i18n/dist/locale/it.json";
import ja from "@vee-validate/i18n/dist/locale/ja.json";
import nl from "@vee-validate/i18n/dist/locale/nl.json";
import pl from "@vee-validate/i18n/dist/locale/pl.json";
import pt_BR from "@vee-validate/i18n/dist/locale/pt_BR.json";
import ru from "@vee-validate/i18n/dist/locale/ru.json";
import sin from "../../locales/sin.json";
import tr from "@vee-validate/i18n/dist/locale/tr.json";
import uk from "@vee-validate/i18n/dist/locale/uk.json";
import zh_CN from "@vee-validate/i18n/dist/locale/zh_CN.json";
import { all } from '@vee-validate/rules';

window.defineRule = defineRule;

export default {
    install: (app) => {
        /**
         * Global components registration;
         */
        app.component("VForm", Form);
        app.component("VField", Field);
        app.component("VErrorMessage", ErrorMessage);

        window.addEventListener("load", () => setLocale(document.documentElement.attributes.lang.value));

        /**
         * Registration of all global validators.
         */
        Object.entries(all).forEach(([name, rule]) => defineRule(name, rule));

        /**
         * This regular expression allows phone numbers with the following conditions:
         * - The phone number can start with an optional "+" sign.
         * - After the "+" sign, there should be one or more digits.
         *
         * This validation is sufficient for global-level phone number validation. If
         * someone wants to customize it, they can override this rule.
         */
        defineRule("phone", (value) => {
            if (! value || ! value.length) {
                return true;
            }

            if (! /^\+?\d+$/.test(value)) {
                return false;
            }

            return true;
        });
                
        defineRule("address", (value) => {
            if (!value || !value.length) {
                return true;
            }

            if (
                !/^[a-zA-Z0-9\s.\/*'\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\u0590-\u05FF\u3040-\u309F\u30A0-\u30FF\u0400-\u04FF\u0D80-\u0DFF\u3400-\u4DBF\u2000-\u2A6D\u00C0-\u017F\u0980-\u09FF\u0900-\u097F\u4E00-\u9FFF,\(\)-]{1,60}$/iu.test(
                    value
                )
            ) {
                return false;
            }

            return true;
        });

        defineRule("postcode", (value) => {
            if (! value || ! value.length) {
                return true;
            }

            if (! /^[a-zA-Z0-9][a-zA-Z0-9\s-]*[a-zA-Z0-9]$/.test(value)) {
                return false;
            }

            return true;
        });

        defineRule("decimal", (value, { decimals = '*', separator = '.' } = {}) => {
            if (value === null || value === undefined || value === '') {
                return true;
            }

            if (Number(decimals) === 0) {
                return /^-?\d*$/.test(value);
            }

            const regexPart = decimals === '*' ? '+' : `{1,${decimals}}`;
            const regex = new RegExp(`^[-+]?\\d*(\\${separator}\\d${regexPart})?([eE]{1}[-]?\\d+)?$`);

            return regex.test(value);
        });

        defineRule("required_if", (value, { condition = true } = {}) => {
            if (condition) {
                if (value === null || value === undefined || value === '') {
                    return false;
                }
            }

            return true;
        });

        defineRule("", () => true);

        defineRule("date_format", (value) => {
            const regex = /^\d{4}-\d{2}-\d{2}$/;
            
            return regex.test(value);
        });

        defineRule("after", (value) => {
            const today = new Date();
            const inputDate = new Date(value);
          
            today.setHours(0, 0, 0, 0);
            inputDate.setHours(0, 0, 0, 0);
          
            return inputDate >= today;
        });

        configure({
            /**
             * Built-in error messages and custom error messages are available. Multiple
             * locales can be added in the same way.
             */
            generateMessage: localize({
                ar: {
                    ...ar,
                    messages: {
                        ...ar.messages,
                        phone: "يجب أن يكون هذا {field} رقم هاتف صالحًا",
                        after: "يجب أن يكون {field} تاريخًا في المستقبل أو اليوم.",
                    },
                },
        
                en: {
                    ...en,
                    messages: {
                        ...en.messages,
                        phone: "This {field} must be a valid phone number",
                        after: "The {field} must be a date in the future or today.",
                    },
                },
        
                es: {
                    ...es,
                    messages: {
                        ...es.messages,
                        phone: "Este {field} debe ser un número de teléfono válido.",
                        after: "El {field} debe ser una fecha en el futuro o hoy.",
                    },
                },
        
                fa: {
                    ...fa,
                    messages: {
                        ...fa.messages,
                        phone: "این {field} باید یک شماره تلفن معتبر باشد.",
                        after: "{field} باید یک تاریخ در آینده یا امروز باشد.",
                    },
                },
        
                tr: {
                    ...tr,
                    messages: {
                        ...tr.messages,
                        phone: "Bu {field} geçerli bir telefon numarası olmalıdır.",
                        after: "{field} gelecekte veya bugün olmalıdır.",
                    },
                },
            }),

            validateOnBlur: true,
            validateOnInput: true,
            validateOnChange: true,
        });
    },
};
