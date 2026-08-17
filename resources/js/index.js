function registerCountUpAlpineComponent() {
    window.Alpine.data('countUp', ({
        value,
        decimals = 0,
        duration = 1000,
        thousandsSeparator = ',',
        decimalSeparator = '.',
        prefix = '',
        suffix = '',
    }) => ({
        displayValue: '',
        observer: null,

        format(number) {
            const sign = number < 0 ? '-' : ''
            const [integerPart, decimalPart] = Math.abs(number).toFixed(decimals).split('.')
            const groupedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator)

            return prefix + sign + groupedIntegerPart + (decimalPart ? decimalSeparator + decimalPart : '') + suffix
        },

        // Runs immediately (element already visible, e.g. above the fold) or
        // the first time the element scrolls into the viewport.
        init() {
            const target = Number.isFinite(value) ? value : 0

            this.displayValue = this.format(0)

            if (typeof IntersectionObserver === 'undefined') {
                this.animate(target)

                return
            }

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return
                    }

                    this.animate(target)
                    this.observer.disconnect()
                })
            }, { threshold: 0.1 })

            this.observer.observe(this.$el)

            this.$cleanup(() => this.observer?.disconnect())
        },

        animate(target) {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.displayValue = this.format(target)

                return
            }

            const startTime = performance.now()

            const step = (now) => {
                const progress = Math.min((now - startTime) / duration, 1)
                const eased = 1 - Math.pow(1 - progress, 3)

                this.displayValue = this.format(target * eased)

                if (progress < 1) {
                    requestAnimationFrame(step)
                } else {
                    this.displayValue = this.format(target)
                }
            }

            requestAnimationFrame(step)
        },
    }))
}

if (window.Alpine) {
    registerCountUpAlpineComponent()
} else {
    document.addEventListener('alpine:init', registerCountUpAlpineComponent)
}
