<script setup>
import Nav from './Nav.vue';
import Topbar from './Topbar.vue';
import { ref, onMounted, onUnmounted, computed } from 'vue'

const scrolledPast = ref(false)

const onScroll = () => {
  scrolledPast.value = window.scrollY > window.innerHeight
}

onMounted(() => {
  window.addEventListener('scroll', onScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
})

const navbarClasses = computed(() => {
  return [
    'transition-all duration-500 ease-in-out',
    scrolledPast.value
      ? 'bg-primary-default shadow-lg'
      : 'bg-gradient-to-b from-black/20 to-transparent'
  ]
})

const logoClasses = computed(() => {
  return [
    'transition-all duration-500 ease-in-out',
    scrolledPast.value ? '' : 'drop-shadow-lg'
  ]
})

const menuItemClasses = computed(() => {
  return [
    'transition-all duration-300',
    scrolledPast.value
      ? 'bg-accent-earth text-primary-dark hover:bg-accent-terracotta hover:text-white'
      : 'bg-white/90 backdrop-blur-sm text-primary-dark hover:bg-accent-gold hover:text-white'
  ]
})
</script>

<template>
    <div :class="navbarClasses" class="fixed inset-x-0 top-0 z-10">
        <Topbar :is-scrolled="scrolledPast"></Topbar>
        <Nav :logo-class="logoClasses" :menu-class="menuItemClasses" :is-scrolled="scrolledPast"></Nav>
    </div>
</template>
