export default {
    mounted(el, binding) {
        initTooltip(el, binding);
    },
    updated(el, binding) {
        initTooltip(el, binding);
    }
};

const initTooltip = (el, binding) => {
    const defaultOptions = {
        placement: 'top',
        trigger: 'hover',
        html: false,
        content: '',
        delay: { show: 200, hide: 100 }
    };

    const options = {
        ...defaultOptions,
        ...(typeof binding.value === 'object' ? binding.value : { content: binding.value })
    };

    let tooltip = document.getElementById(`tooltip-${el.tooltipId}`);

    if (! tooltip) {
        el.tooltipId = Math.random().toString(36).substring(2, 9);
        tooltip = document.createElement('div');
        tooltip.id = `tooltip-${el.tooltipId}`;
        tooltip.className = 'max-w-[250px] break-words rounded-lg bg-gray-800 px-4 py-3 text-sm leading-snug text-white shadow-lg transition-opacity transition-transform duration-200';
        tooltip.style.display = 'none';
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '10000';

        const inner = document.createElement('div');
        inner.className = 'tooltip-inner';

        const arrow = document.createElement('div');
        arrow.className = 'absolute h-0 w-0 border-solid';

        tooltip.appendChild(inner);
        tooltip.appendChild(arrow);
        document.body.appendChild(tooltip);

        if (options.html) {
            inner.innerHTML = options.content;
        } else {
            inner.textContent = options.content;
        }

        el._tooltip = tooltip;

        const showTooltip = () => {
            tooltip.style.display = 'block';

            const rect = el.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            let top, left;

            switch (options.placement) {
                case 'top':
                    top = rect.top - tooltipRect.height - 10;
                    left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    arrow.style.top = 'auto';
                    arrow.style.bottom = '-5px';
                    arrow.style.left = '50%';
                    arrow.style.transform = 'translateX(-50%)';
                    break;
                case 'bottom':
                    top = rect.bottom + 10;
                    left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    arrow.style.bottom = 'auto';
                    arrow.style.top = '-5px';
                    arrow.style.left = '50%';
                    arrow.style.transform = 'translateX(-50%) rotate(180deg)';
                    break;
                case 'left':
                    top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                    left = rect.left - tooltipRect.width - 10;
                    arrow.style.top = '50%';
                    arrow.style.left = 'auto';
                    arrow.style.right = '-5px';
                    arrow.style.transform = 'translateY(-50%) rotate(-90deg)';
                    break;
                case 'right':
                    top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                    left = rect.right + 10;
                    arrow.style.top = '50%';
                    arrow.style.right = 'auto';
                    arrow.style.left = '-5px';
                    arrow.style.transform = 'translateY(-50%) rotate(90deg)';
                    break;
            }

            if (top < 0) {
                top = 0;
            }

            if (left < 0) {
                left = 0;
            }

            if (left + tooltipRect.width > window.innerWidth) {
                left = window.innerWidth - tooltipRect.width;
            }

            tooltip.style.top = `${top + window.scrollY}px`;
            tooltip.style.left = `${left + window.scrollX}px`;
        };

        const hideTooltip = () => {
            tooltip.style.display = 'none';
        };

        if (options.trigger === 'hover') {
            el.addEventListener('mouseenter', () => {
                el._showTimeout = setTimeout(showTooltip, options.delay.show);
            });

            el.addEventListener('mouseleave', () => {
                clearTimeout(el._showTimeout);
                el._hideTimeout = setTimeout(hideTooltip, options.delay.hide);
            });
        } else if (options.trigger === 'click') {
            el.addEventListener('click', showTooltip);

            document.addEventListener('click', (e) => {
                if (e.target !== el && !el.contains(e.target)) {
                    hideTooltip();
                }
            });
        }
    } else {
        const inner = tooltip.querySelector('.tooltip-inner');

        if (options.html) {
            inner.innerHTML = options.content;
        } else {
            inner.textContent = options.content;
        }
    }
}
