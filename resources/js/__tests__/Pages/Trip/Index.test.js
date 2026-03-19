/**
 * @fileoverview Tests for the public trips overview page
 * Covers: rendering, country filter checkboxes, result count, empty state
 */

import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import TripIndex from '@/Pages/Trip/Index.vue';

// ========================================
// MOCK DATA
// ========================================

const mockTrips = [
    {
        id: 1,
        name: 'Parijs Express',
        slug: 'reis-naar-parijs',
        duration: 7,
        destinations: [{ country_code: 'FR' }],
        transport: ['train'],
        transport_formatted: [{ value: 'train', label: 'Trein' }],
        price_formatted: '1.200',
        hero_image: null,
    },
    {
        id: 2,
        name: 'Rome Avontuur',
        slug: 'reis-naar-rome',
        duration: 10,
        destinations: [{ country_code: 'IT' }],
        transport: ['train'],
        transport_formatted: [{ value: 'train', label: 'Trein' }],
        price_formatted: '1.500',
        hero_image: null,
    },
];

const mockCountries = [
    { code: 'FR', name: 'Frankrijk', en_name: 'France' },
    { code: 'IT', name: 'Italië', en_name: 'Italy' },
];

// ========================================
// MOUNT HELPER
// ========================================

function mountComponent(props = {}) {
    return mount(TripIndex, {
        props: {
            trips: mockTrips,
            countries: mockCountries,
            ...props,
        },
        global: {
            stubs: {
                Layout: { template: '<div><slot name="hero" /><slot /></div>' },
                TripCard: { template: '<div data-testid="trip-card" />', props: ['trip'] },
                DualRangeSlider: true,
                DecorativeLine: true,
                EnumIcon: true,
            },
        },
    });
}

// ========================================
// TESTS
// ========================================

describe('Trip/Index.vue', () => {
    describe('Basic rendering', () => {
        it('renders a TripCard for each trip', () => {
            const wrapper = mountComponent();

            expect(wrapper.findAll('[data-testid="trip-card"]')).toHaveLength(mockTrips.length);
        });

        it('shows the result count', () => {
            const wrapper = mountComponent();

            expect(wrapper.text()).toContain(String(mockTrips.length));
        });

        it('renders no TripCards when trips is empty', () => {
            const wrapper = mountComponent({ trips: [] });

            expect(wrapper.findAll('[data-testid="trip-card"]')).toHaveLength(0);
        });
    });

    describe('Country filter', () => {
        it('renders a checkbox for each country', () => {
            const wrapper = mountComponent();

            const countryCheckboxes = wrapper
                .findAll('input[type="checkbox"]')
                .filter(cb => mockCountries.some(c => cb.element.value === c.code));

            expect(countryCheckboxes).toHaveLength(mockCountries.length);
        });

        it('displays the translated country names', () => {
            const wrapper = mountComponent();

            expect(wrapper.text()).toContain('Frankrijk');
            expect(wrapper.text()).toContain('Italië');
        });

        it('renders no country checkboxes when countries prop is empty', () => {
            const wrapper = mountComponent({ countries: [] });

            const countryCheckboxes = wrapper
                .findAll('input[type="checkbox"]')
                .filter(cb => ['FR', 'IT'].includes(cb.element.value));

            expect(countryCheckboxes).toHaveLength(0);
        });
    });

    describe('Active filters', () => {
        it('hides the clear-all button when no filters are active', () => {
            const wrapper = mountComponent();

            // hasActiveFilters is false on initial render (all range sliders at bounds)
            // The result count clear-all button is only shown when hasActiveFilters is true
            const resultSection = wrapper.find('p.text-sm');
            expect(resultSection.text()).not.toContain('clear_all');
        });
    });
});
