<template>
    <div class="border-b py-2">
        <button class="flex justify-between items-center w-full text-left" @click="toggle()">
            <span class="text-primary-dark">
                <strong>{{ booker.full_name }}</strong> ({{ booker.birthdate_formatted }})
            </span>
            <span>
                <DownLine :class="open ? 'rotate-180' : ''"
                    class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </DownLine>
            </span>
        </button>

        <!-- Inhoud -->
        <div v-if="open" class="mt-2 ml-4 text-primary-default">
            <p><span class="font-medium">Geboortedatum:</span> {{ booker.birthdate_formatted }}</p>
            <p><span class="font-medium">Nationaliteit:</span> {{ booker.nationality }}</p>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue'

const props = defineProps({
    booker: Object
})
const open = ref(false)

const toggle = () => {
    open.value = !open.value
}

</script>
