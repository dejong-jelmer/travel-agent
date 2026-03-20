import { ref, computed, watch } from 'vue';

const RANGE_DEBOUNCE_MS = 400;

function tripPrice(trip) {
    if (!trip.price_formatted) return null;
    return parseInt(trip.price_formatted.replace(/\./g, ''), 10);
}

export function useTripFilters(trips) {
    // --- Filter state (UI — update immediately for responsive slider feel) ---

    // Initialise selectedCountries from URL ?land= param (e.g. /reizen?land=FR)
    const urlParams = new URLSearchParams(window.location.search);
    const landParam = urlParams.get('land');
    const selectedCountries = ref(landParam ? landParam.split(',') : []);

    const selectedTransports = ref([]);
    const durationMin = ref(null);
    const durationMax = ref(null);
    const priceMin = ref(null);
    const priceMax = ref(null);

    // --- Debounced range state (used for actual filtering) ---
    const debouncedDurationMin = ref(null);
    const debouncedDurationMax = ref(null);
    const debouncedPriceMin = ref(null);
    const debouncedPriceMax = ref(null);

    // isFiltering is true while the debounce timer is pending
    const isFiltering = ref(false);
    let debounceTimer = null;

    function scheduleRangeFilter() {
        isFiltering.value = true;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            debouncedDurationMin.value = durationMin.value;
            debouncedDurationMax.value = durationMax.value;
            debouncedPriceMin.value = priceMin.value;
            debouncedPriceMax.value = priceMax.value;
            isFiltering.value = false;
        }, RANGE_DEBOUNCE_MS);
    }

    watch([durationMin, durationMax, priceMin, priceMax], scheduleRangeFilter);

    // --- Bounds ---
    const allDurations = computed(() =>
        trips.value.map(t => t.duration).filter(d => d > 0)
    );
    const minDurationBound = computed(() =>
        allDurations.value.length ? Math.min(...allDurations.value) : 1
    );
    const maxDurationBound = computed(() =>
        allDurations.value.length ? Math.max(...allDurations.value) : 30
    );

    const allPrices = computed(() =>
        trips.value.map(tripPrice).filter(p => p !== null)
    );
    const minPriceBound = computed(() =>
        allPrices.value.length ? Math.min(...allPrices.value) : 0
    );
    const maxPriceBound = computed(() =>
        allPrices.value.length ? Math.max(...allPrices.value) : 5000
    );

    // Initialise range values once bounds are known
    watch(minDurationBound, (val) => {
        if (durationMin.value === null) { durationMin.value = val; debouncedDurationMin.value = val; }
    }, { immediate: true });
    watch(maxDurationBound, (val) => {
        if (durationMax.value === null) { durationMax.value = val; debouncedDurationMax.value = val; }
    }, { immediate: true });
    watch(minPriceBound, (val) => {
        if (priceMin.value === null) { priceMin.value = val; debouncedPriceMin.value = val; }
    }, { immediate: true });
    watch(maxPriceBound, (val) => {
        if (priceMax.value === null) { priceMax.value = val; debouncedPriceMax.value = val; }
    }, { immediate: true });

    // --- Transport options derived from trips ---
    const allTransports = computed(() => {
        const seen = new Set();
        return trips.value
            .flatMap(t => t.transport_formatted || [])
            .filter(mode => {
                if (seen.has(mode.value)) return false;
                seen.add(mode.value);
                return true;
            })
            .sort((a, b) => a.label.localeCompare(b.label));
    });

    // --- Count helpers ---
    function tripCountForCountry(code) {
        return trips.value.filter(trip =>
            trip.destinations.some(dest => dest.country_code === code)
        ).length;
    }

    function tripCountForTransport(value) {
        return trips.value.filter(t => t.transport?.includes(value)).length;
    }

    // --- Filtered trips (uses debounced range values) ---
    const filteredTrips = computed(() => {
        return trips.value.filter(trip => {
            // Land (OR binnen selectie)
            if (selectedCountries.value.length &&
                !trip.destinations.some(d => selectedCountries.value.includes(d.country_code)))
                return false;

            // Vervoer (OR binnen selectie)
            if (selectedTransports.value.length &&
                !selectedTransports.value.some(t => trip.transport?.includes(t)))
                return false;

            // Reisdagen — uses debounced value
            const dur = trip.duration;
            if (dur > 0) {
                if (debouncedDurationMin.value !== null && dur < debouncedDurationMin.value) return false;
                if (debouncedDurationMax.value !== null && dur > debouncedDurationMax.value) return false;
            }

            // Prijs — uses debounced value
            const price = tripPrice(trip);
            if (price !== null) {
                if (debouncedPriceMin.value !== null && price < debouncedPriceMin.value) return false;
                if (debouncedPriceMax.value !== null && price > debouncedPriceMax.value) return false;
            }

            return true;
        });
    });

    // --- Active state ---
    const hasActiveFilters = computed(() =>
        selectedCountries.value.length > 0 ||
        selectedTransports.value.length > 0 ||
        durationMin.value !== minDurationBound.value ||
        durationMax.value !== maxDurationBound.value ||
        priceMin.value !== minPriceBound.value ||
        priceMax.value !== maxPriceBound.value
    );

    function clearAll() {
        clearTimeout(debounceTimer);
        isFiltering.value = false;
        selectedCountries.value = [];
        selectedTransports.value = [];
        durationMin.value = minDurationBound.value;
        durationMax.value = maxDurationBound.value;
        priceMin.value = minPriceBound.value;
        priceMax.value = maxPriceBound.value;
        // Apply immediately — no debounce on clear
        debouncedDurationMin.value = minDurationBound.value;
        debouncedDurationMax.value = maxDurationBound.value;
        debouncedPriceMin.value = minPriceBound.value;
        debouncedPriceMax.value = maxPriceBound.value;
    }

    return {
        // State
        selectedCountries,
        selectedTransports,
        durationMin,
        durationMax,
        priceMin,
        priceMax,
        // Bounds
        minDurationBound,
        maxDurationBound,
        minPriceBound,
        maxPriceBound,
        // Derived
        allTransports,
        filteredTrips,
        hasActiveFilters,
        isFiltering,
        // Helpers
        tripCountForCountry,
        tripCountForTransport,
        clearAll,
    };
}
