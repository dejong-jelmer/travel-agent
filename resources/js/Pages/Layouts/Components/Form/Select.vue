<script setup>
    import Feedback from "./Feedback.vue";

    const props = defineProps({
        modelValue: [String, Number],
        name: String,
        label: String,
        feedback: {
            type: String,
            required: false,
        },
        required: {
            type: Boolean,
            default: true
        },
        items: Array,
     });
     console.log(props.items);

</script>
<template>
    <div>
        <label :for="name" class="form-label">{{ label }}</label>
        <select :id="name" :value="modelValue"
            :placeholder="name" :required="required"
            class="form-input"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">Koppel een land</option>
            <template v-for="(item, index) in items" :key="index">
                <option :value="item.id">{{ item.name }}</option>
            </template>
        </select>
        <template v-if="feedback">
            <Feedback :message="feedback" />
        </template>
    </div>
</template>
