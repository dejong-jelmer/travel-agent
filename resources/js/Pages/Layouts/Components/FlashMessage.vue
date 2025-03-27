<script setup>
import { ref, watchEffect } from 'vue';

const props = defineProps({
  type: String,
  message: String,
});

const isVisible = ref(false);

watchEffect(() => {
  if (props.message) {
    isVisible.value = true;
    setTimeout(() => {
      isVisible.value = false;
    }, 4000);
  }
});
</script>

<template>
  <transition name="fade">
    <div v-if="isVisible"
         class="fixed top-24 right-5 px-4 py-3 rounded-lg shadow-lg text-white text-sm"
         :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'">
      {{ message }}
    </div>
  </transition>
</template>

<style scoped>
/* Fade in/out animatie */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
