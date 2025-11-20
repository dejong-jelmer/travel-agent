// Composables/useSort.js
import { ref, computed, unref } from 'vue';

export function useSort(items, initialKey = null, initialDirection = 'asc') {
  const sortKey = ref(initialKey);
  const sortDirection = ref(initialDirection);

  // Helper function to get nested property value
  const getNestedValue = (obj, path) => {
    return path.split('.').reduce((current, key) => current?.[key], obj);
  };

  const sortedItems = computed(() => {
    const itemsValue = unref(items);
    if (!sortKey.value || !itemsValue) return itemsValue;

    return [...itemsValue].sort((a, b) => {
      const aValue = getNestedValue(a, sortKey.value);
      const bValue = getNestedValue(b, sortKey.value);

      // Handle null/undefined values
      if (aValue == null && bValue == null) return 0;
      if (aValue == null) return 1;
      if (bValue == null) return -1;

      let comparison = 0;

      if (typeof aValue === 'string' && typeof bValue === 'string') {
        comparison = aValue.toLowerCase().localeCompare(bValue.toLowerCase());
      } else if (typeof aValue === 'number' && typeof bValue === 'number') {
        comparison = aValue - bValue;
      } else {
        // Fallback: convert to string and compare
        comparison = String(aValue).toLowerCase().localeCompare(String(bValue).toLowerCase());
      }

      return sortDirection.value === 'asc' ? comparison : -comparison;
    });
  });

  const sortBy = (key) => {
    if (sortKey.value === key) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortKey.value = key;
      sortDirection.value = 'asc';
    }
  };

  return {
    sortedItems,
    sortBy,
    sortKey,
    sortDirection
  };
}
