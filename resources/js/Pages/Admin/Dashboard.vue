<script setup>
const props = defineProps({
    bookings: Object
})
</script>
<template>
    <Admin>
        <section class="section">
            <h1 class="text-2xl font-semibold text-primary-default p-2">Dashboard</h1>
            <div class="grid grid-cols-1 tablet:grid-cols-2 laptop:grid-cols-4 gap-6">

                <!-- Nieuwe boekingen -->
                <Widget title="Nieuwe boekingen" :value="bookings.new" :link="route('admin.bookings.index')">
                    <template #icon>
                        <BlingLine class="w-6 h-6 text-accent-gold" />
                    </template>
                </Widget>
                <!-- Toekomstige vertrekken -->
                <Widget title="Komende vertrekken" :value="bookings.departDueNextMonth" subtitle="Volgende maand">
                    <template #icon>
                        <Calendar class="w-6 h-6 text-accent-gold" />
                    </template>
                </Widget>
            </div>
        </section>
    </Admin>
</template>
