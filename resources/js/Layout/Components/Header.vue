<template>
    <div class="h-screen-minus-nav bg-cover bg-center flex items-center justify-center text-white text-center px-6" style="background-image: url('https://picsum.photos/1600/900');">
      <div>
        <h1 class="text-4xl md:text-6xl font-bold">{{ title }}</h1>
        <p class="mt-4 text-lg md:text-xl">{{ subtitle }}</p>
      </div>
    </div>
</template>
<script>
export default {
    data() {
      return {
        title: "TussenTijd Reizen",
        subtitle: "Bezoek het verleden met oog voor de toekomst"
      };
    }
}
</script>
