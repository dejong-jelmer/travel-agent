<template>
    <div  :class="navbarClasses" class="fixed inset-x-0 top-0 z-10">
        <Topbar></Topbar>
        <Nav></Nav>
    </div>
</template>
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
    'transition-all duration-300',
    scrolledPast.value ? 'bg-deep-green' : 'bg-transparent'
  ]
})
</script>

