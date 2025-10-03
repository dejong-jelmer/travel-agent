<script setup>
const props = defineProps({
    booking: Object,
    required: true
})
</script>

<template>
    <Layout>
        <section class="section">
            <div class="min-h-screen bg-gradient-to-br from-neutral-50 to-secondary-sage/10">
                Je boeking is verwerkt.
                Dit is de reis die je hebt geboekt:
                {{ booking.product.name }}
            </div>
        </section>
    </Layout>

</template>
