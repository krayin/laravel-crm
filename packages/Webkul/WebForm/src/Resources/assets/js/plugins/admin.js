export default {
    install(app) {
        app.config.globalProperties.$admin = {
            /**
             * Generates a formatted price.
             *
             * @param {number} price - The price value to be formatted.
             * @returns {string} - The formatted price string.
             */
            formatPrice: (price) => {
                let locale = document.querySelector('meta[http-equiv="content-language"]').content;

                locale = locale.replace(/([a-z]{2})_([A-Z]{2})/g, '$1-$2');

                const currency = JSON.parse(document.querySelector('meta[name="currency"]').content);

                const symbol = currency.symbol !== '' ? currency.symbol : currency.code;

                if (! currency.currency_position) {
                    return new Intl.NumberFormat(locale, {
                        style: "currency",
                        currency: currency.code,
                    }).format(price);
                }

                const formatter = new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency.code,
                    minimumFractionDigits: currency.decimal ?? 2
                });

                const formattedCurrency = formatter.formatToParts(price)
                    .map(part => {
                        switch (part.type) {
                            case 'currency':
                                return '';

                            case 'group':
                                return currency.group_separator === ''
                                    ? part.value
                                    : currency.group_separator;

                            case 'decimal':
                                return currency.decimal_separator === ''
                                    ? part.value
                                    : currency.decimal_separator;

                            default:
                                return part.value;
                        }
                    })
                    .join('');

                switch (currency.currency_position) {
                    case 'left':
                        return symbol + formattedCurrency;

                    case 'left_with_space':
                        return symbol + ' ' + formattedCurrency;

                    case 'right':
                        return formattedCurrency + symbol;

                    case 'right_with_space':
                        return formattedCurrency + ' ' + symbol;

                    default:
                        return formattedCurrency;
                }
            },

            /**
             * Generates a formatted date.
             *
             * @param {string} dateString - The date value to be formatted.
             * @param {string} format - The format to be used for formatting the date.
             * @returns {string} - The formatted date string.
             */
            formatDate: (dateString, format) => {
                const date = new Date(dateString);

                const formatters = {
                    d: date.getUTCDate(),
                    DD: date.getUTCDate().toString().padStart(2, '0'),
                    M: date.getUTCMonth() + 1,
                    MM: (date.getUTCMonth() + 1).toString().padStart(2, '0'),
                    MMM: date.toLocaleString('en-US', { month: 'short' }),
                    MMMM: date.toLocaleString('en-US', { month: 'long' }),
                    yy: date.getUTCFullYear().toString().slice(-2),
                    yyyy: date.getUTCFullYear(),
                    H: date.getUTCHours(),
                    HH: date.getUTCHours().toString().padStart(2, '0'),
                    h: (date.getUTCHours() % 12 || 12),
                    hh: (date.getUTCHours() % 12 || 12).toString().padStart(2, '0'),
                    m: date.getUTCMinutes(),
                    mm: date.getUTCMinutes().toString().padStart(2, '0'),
                    A: date.getUTCHours() < 12 ? 'AM' : 'PM'
                };

                return format.replace(/\b(?:d|DD|M|MM|MMM|MMMM|yy|yyyy|H|HH|h|hh|m|mm|A)\b/g, match => formatters[match]);
            }
        };
    },
};
