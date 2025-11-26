import { computed, unref } from 'vue';

/**
 * Composable for tracking character count with visual feedback
 *
 * @param {Ref<string>|string} value - The reactive value to track
 * @param {Ref<number>|number} maxLength - Maximum allowed length
 * @param {Object} options - Configuration options
 * @param {number} options.warningThreshold - Percentage at which warning starts (default: 75)
 * @param {number} options.criticalThreshold - Percentage at which critical warning starts (default: 90)
 * @returns {Object} Character counter utilities
 */
export function useCharacterCounter(value, maxLength, options = {}) {
    const {
        warningThreshold = 75,
        criticalThreshold = 90,
    } = options;

    const length = computed(() => {
        const val = unref(value);
        return val?.length ?? 0;
    });

    const max = computed(() => unref(maxLength));

    const charsLeft = computed(() => max.value - length.value);

    const percentage = computed(() => {
        if (max.value === 0) return 0;
        return (length.value / max.value) * 100;
    });

    const isOverLimit = computed(() => length.value > max.value);
    const isWarning = computed(() => percentage.value >= warningThreshold && !isOverLimit.value);
    const isCritical = computed(() => percentage.value >= criticalThreshold && !isOverLimit.value);

    const counterClass = computed(() => {
        if (isOverLimit.value) return 'text-status-error font-semibold';
        if (isCritical.value) return 'text-status-warning font-semibold';
        if (isWarning.value) return 'text-status-warning';
        return 'text-status-success';
    });

    return {
        length,
        charsLeft,
        percentage,
        isOverLimit,
        isWarning,
        isCritical,
        counterClass,
    };
}
