<template>
    <div>
      <!-- Overlay (indien open) -->
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black bg-opacity-30 z-40"
        @click="isOpen = false"
      ></div>

      <!-- Sidebar met vastzittende pijl -->
      <aside
        class="fixed top-0 left-0 h-full w-64 bg-gray-800 text-white transform transition-transform duration-300 ease-in-out z-50 flex"
        :class="{ '-translate-x-[85%]': !isOpen, 'translate-x-0': isOpen }"
      >
        <div class="flex-grow p-4">
          <nav class="mt-4">
            <Link
              v-for="(item, index) in menuItems"
              :key="index"
              :href="item.path"
              class="block px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition"
              :class="{ 'bg-gray-700 text-white': $page.url === item.path }"
            >
              {{ item.label }}
            </Link>
          </nav>
        </div>

        <!-- Pijlknop die meeschuift -->
        <button
          class="bg-gray-800 text-white px-2 py-3 rounded-r-md shadow-md"
          @click="isOpen = !isOpen"
        >
          {{ isOpen ? '❮' : '❯' }}
        </button>
      </aside>
    </div>
  </template>

  <script>
  import { ref } from "vue";
  import { Link } from "@inertiajs/vue3";

  export default {
    components: { Link },
    setup() {
      const isOpen = ref(false);

      const menuItems = [
        { label: "Producten", path: route('products.index') },
        { label: "Dashboard", path: route('admin.dashboard') },
      ];

      return { isOpen, menuItems };
    },
  };
  </script>
