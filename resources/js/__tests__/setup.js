/**
 * @fileoverview Global test setup for Vitest
 * Configures i18n and other global plugins for all tests
 */

import { config } from '@vue/test-utils';
import i18n from '@/plugins/i18n.js';

// Make i18n available globally for all tests
config.global.plugins = [i18n];

// Make $t available as a global property for components
config.global.mocks = {
    $t: (key, params) => i18n.global.t(key, params),
};
