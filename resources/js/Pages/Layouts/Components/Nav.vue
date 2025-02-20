<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
</script>

<template>
    <nav class="fixed inset-0 bg-white shadow-md h-24 flex items-center px-8 md:px-[160px] justify-between">
      <!-- Logo -->
      <div class="text-xl font-bold text-gray-800"><Link href="/">{{ logo }}</Link></div>

      <!-- Navigation Links -->
      <div class="hidden md:flex space-x-6">
        <Link href="/about" class="text-gray-600 hover:text-gray-900">{{ about }}</Link>
        <Link href="/contact" class="text-gray-600 hover:text-gray-900">{{ contact }}</Link>
        <template v-if="user">
            <Link href="/admin/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</Link>
            <Link href="/admin/logout" class="text-gray-600 hover:text-gray-900">Loguit</Link>
        </template>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="toggleMenu" class="md:hidden text-gray-600">
        <div class="w-6 h-0.5 bg-gray-600 transition-transform transform" :class="{ 'rotate-45 translate-y-2': isMenuOpen }"></div>
        <div class="w-6 h-0.5 bg-gray-600 mt-1.5 transition-opacity" :class="{ 'opacity-0': isMenuOpen }"></div>
        <div class="w-6 h-0.5 bg-gray-600 mt-1.5 transition-transform transform" :class="{ '-rotate-45 -translate-y-2': isMenuOpen }"></div>
      </button>

      <!-- Mobile Menu -->
      <div v-if="isMenuOpen" class="absolute top-24 left-0 w-full bg-white shadow-md md:hidden flex flex-col items-center space-y-4 py-4">
        <Link href="/about" class="text-gray-600 hover:text-gray-900">{{ about }}</Link>
        <Link href="/contact" class="text-gray-600 hover:text-gray-900">{{ contact }}</Link>
        <template v-if="user">
            <Link href="/admin/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</Link>
            <Link href="/admin/logout" class="text-gray-600 hover:text-gray-900">Loguit</Link>
        </template>
      </div>
    </nav>
  </template>

  <script>
  export default {
    data() {
      return {
        logo: "TussenTijd Reizen",
        about: "Over mij",
        contact: "Contact",
        isMenuOpen: false
      };
    },
    methods: {
      toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen;
      },
    },
  };
  </script>
