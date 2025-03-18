<script setup>
import Layout from '@/Pages/Layouts/Layout.vue'
import SortableBlocks from '@/Pages/Layouts/Components/SortableBlocks.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    // itineraries: Object,
});
console.log(props.product.id);
</script>
<template>
    <Layout>
        <SortableBlocks :blocks="product.itineraries" @update:order="updateOrder">
            <template v-slot:default="slotProps">
                <Itinerary
                    :isAdmin="true"
                    :itinerary="slotProps.block"
                    imageUrl="https://picsum.photos/150/150"
                />
            </template>
        </SortableBlocks>
    </Layout>
</template>
<script>
import axios from "axios";
import Itinerary from '../../../Layouts/Components/Itinerary.vue';

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
    mounted() {
        console.log(this.product);
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
