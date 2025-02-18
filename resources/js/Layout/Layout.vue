<script setup>
import Nav from './Components/Nav.vue'
</script>

<template>
  <main>
    <header>
        <Nav></Nav>
    </header>
    <article>
        <slot />
    </article>
  </main>
</template>
