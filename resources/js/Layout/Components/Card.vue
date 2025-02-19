<script setup>
    import Train from './Icons/Train.vue';

    defineProps({ product: Object })
</script>

<template>
    <div class="max-w-md h-auto mx-auto bg-white rounded-2xl shadow-md relative select-none">
      <div class="h-[55%] min-h-[250px] bg-cover bg-center rounded-t-2xl overflow-hidden" :style="`background-image: url(${product.image})`">
        <div class="absolute left-auto top-0 right-0 w-[30%] rounded-bl-2xl bg-white text-black py-4 text-left pl-6">
          <div class="text-sm">Vanaf</div>
          <div class="text-md font-bold inline">€&nbsp;{{ product.price }}</div>
        </div>
      </div>
      <div class="p-5 space-y-4 text-left">
        <h3 class="text-xl font-semibold text-gray-700">{{ product.country }}</h3>
        <h4 class="text-xl font-semibold text-gray-700">{{ product.title }}</h4>
        <div class="grid grid-cols-2 gap-4">
          <div class="grid grid-cols-2 gap-4 text-left">
            <div>
                <p class="text-sm text-gray-500">Reisdagen</p>
                <p class="text-sm text-gray-500 font-bold">{{ product.days }} dagen</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Vervoer</p>
                <p class="text-sm text-gray-500 font-bold">
                    <Train />
                </p>
            </div>
          </div>
          <div class="flex justify-end">
            <button class="bg-blue-500 text-white py-2 px-4 rounded-lg">Bekijk</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        imageUrl: "https://picsum.photos/400/225",
        country: "Frankrijk",
        days: "8",
        price: "895,-",
        title: "De prehistorische mens en de grotten in de valei van de Vézère"
      };
    }
    };
  </script>

  <style scoped>

  </style>
