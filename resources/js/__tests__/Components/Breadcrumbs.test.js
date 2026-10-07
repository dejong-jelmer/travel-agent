import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import Breadcrumbs from "@/Components/Molecules/Breadcrumbs.vue";
import i18n from '@/plugins/i18n.js';

const mountBreadcrumbs = (breadcrumbs) => mount(Breadcrumbs, {
    props: { breadcrumbs },
    global: {
        mocks: {
            route: (name, params = []) => ['', name, ...params].join('/'),
        },
        stubs: {
            Link: { props: ['href'], template: '<a :href="href"><slot /></a>' },
        },
    },
});

const dashboard = { label: 'Dashboard', route: 'admin.dashboard' };
const trips = { label: 'Trips', route: 'admin.trips.index' };
const trip = { label: 'Verona', route: 'admin.trips.show', params: [7] };
const edit = { label: 'Edit', route: null };

describe("Breadcrumbs", () => {
    it("renders nothing without breadcrumbs", () => {
        expect(mountBreadcrumbs([]).find('nav').exists()).toBe(false);
    });

    it("renders a labelled nav with an ordered list of links", () => {
        const wrapper = mountBreadcrumbs([dashboard, trips, trip, edit]);

        expect(wrapper.find('nav').attributes('aria-label')).toBe(i18n.global.t('nav.breadcrumbs'));
        expect(wrapper.find('ul').exists()).toBe(false);
        expect(wrapper.findAll('ol a').map(link => link.attributes('href')))
            .toEqual(['/admin.dashboard', '/admin.trips.index', '/admin.trips.show/7']);
    });

    it("marks only the last item as the current page", () => {
        const wrapper = mountBreadcrumbs([dashboard, edit]);

        const current = wrapper.findAll('[aria-current="page"]');
        expect(current).toHaveLength(1);
        expect(current[0].element.tagName).toBe('SPAN');
        expect(current[0].text()).toBe('Edit');
    });

    it("marks a linked last item as the current page", () => {
        const current = mountBreadcrumbs([dashboard, trips]).find('[aria-current="page"]');

        expect(current.element.tagName).toBe('A');
        expect(current.text()).toBe('Trips');
    });

    it("collapses the middle items into one hidden ellipsis below laptop", () => {
        const items = mountBreadcrumbs([dashboard, trips, trip, edit]).findAll('li');

        expect(items).toHaveLength(5);
        expect(items[1].attributes('aria-hidden')).toBe('true');
        expect(items[1].classes()).toContain('laptop:hidden');
        expect(items[2].classes()).toEqual(expect.arrayContaining(['hidden', 'laptop:flex']));
        expect(items[3].classes()).toEqual(expect.arrayContaining(['hidden', 'laptop:flex']));
        expect(items[0].classes()).toContain('flex');
        expect(items[4].classes()).toEqual(expect.arrayContaining(['flex', 'min-w-0']));
    });

    it("shows two breadcrumbs without an ellipsis", () => {
        const items = mountBreadcrumbs([dashboard, edit]).findAll('li');

        expect(items).toHaveLength(2);
        expect(items.every(item => item.classes().includes('flex'))).toBe(true);
    });
});
