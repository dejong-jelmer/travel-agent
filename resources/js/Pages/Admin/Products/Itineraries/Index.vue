<script setup>
import Layout from '@/Pages/Layouts/Layout.vue'
import SortableBlocks from '@/Pages/Layouts/Components/SortableBlocks.vue';
import Itinerary from '@/Pages/Layouts/Components/Itinerary.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { ref } from "vue";

const user = usePage().props.auth?.user ?? {};
const props = defineProps({
    product: Object,
});
const duration = ref(props.product.duration || 0);
const itineraries = ref(props.product.itineraries.length || 0);
</script>
<template>
    <Layout>
        <div class="space-y-4">
            <div class="w-full justify-end flex items-center">
                <Link v-if="product.itineraries?.length < product.duration" class="form-button" as="button" :href="route('products.itineraries.create', product)">Voeg een itinerary block toe</Link>
                <Link v-else :href="route('products.edit', product)" class="text-sm info-button">Itinerary block toevoegen? <br /> Verleng het aantal reisdagen</Link>
            </div>
            <SortableBlocks
                :blocks="product.itineraries"
                @update:order="updateOrder"
                class="grid gap-y-10"
                >
                <template v-slot:default="slotProps">
                    <Itinerary
                        :isAdmin="!!user.id"
                        :itinerary="slotProps.block"
                    />
                </template>
            </SortableBlocks>
        </div>
    </Layout>
</template>
<script>
import axios from "axios";

export default {
    components: {
        SortableBlocks,
    },

    props: {
        blocks: {
            type: Array,
            required: true,
        },
    },
    methods: {
        updateOrder(orderedItinerary) {
            axios.patch(route('products.itineraries.order', this.product), {
                itineraries: orderedItinerary
            })
                .then(response => console.log(response.data))
                .catch(error => console.error(error));
        }
    }
};
</script>
