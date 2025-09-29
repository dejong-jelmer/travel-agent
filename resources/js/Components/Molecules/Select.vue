<script setup>
import FormFeedback from "@/Components/Atoms/FormFeedback.vue";
import { computed } from 'vue';

const props = defineProps({
    name: String,
    label: String,
    modelValue: {
        type: Array,
        required: true,
    },
    options: {
        type: Object,
        required: true,
    },
    placeholder: {
        type: String,
        required: false,
    },
    feedback: {
        type: [ String, Array ],
        required: false,
    },
    required: {
        type: Boolean,
        required: false,
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    showLabel: {
        type: Boolean,
        required: false,
        default: true,
    },
});
const emit = defineEmits(["update:modelValue"]);
const handleChange = (event) => {
    const selectedValues = Array.from(
        event.target.selectedOptions,
        (option) => option.value
    );
    emit("update:modelValue", selectedValues);
};

const normalizedOptions = computed(() => {
  if (Array.isArray(props.options)) {
    return props.options.map(option => {
      if (typeof option === 'object' && option !== null) {
        return {
          value: option.id,
          label: option.name
        };
      }
      return {
        value: option,
        label: option
      };
    });
  } else if (typeof props.options === 'object') {
    return Object.entries(props.options).map(([value, label]) => ({
      value,
      label
    }));
  }

  return [];
});
</script>
<template>
    <div>
        <Label v-if="showLabel" :form-field="name">{{ label }}</Label>
        <select
            class="form-input"
            :id="name"
            :required="required"
            :multiple="multiple"
            @change="handleChange"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option
                v-for="(option, index) in normalizedOptions"
                :selected="modelValue.includes(option['value'])"
                :key="index"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <template v-if="feedback">
            <FormFeedback :message="feedback" />
        </template>
    </div>
</template>
