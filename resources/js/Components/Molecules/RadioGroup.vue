<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, Object],
    required: true,
  },
  name: { type: String, required: true },
  options: { type: Array, required: true },
})

const emit = defineEmits(["update:modelValue"])

const isChecked = (val) => props.modelValue === val

const updateValue = (val) => {
  emit("update:modelValue", val)
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <label
      v-for="(option, index) in options"
      :key="index"
      class="flex items-center gap-x-2 cursor-pointer"
    >
      <input
        type="radio"
        :name="name"
        :value="index"
        :checked="isChecked(index)"
        @change="() => updateValue(index)"
        class="absolute opacity-0 w-0 h-0"
      />

      <!-- Circle -->
      <div
        class="w-5 h-5 flex items-center justify-center border-2 border-brand-primary rounded-full transition-all"
        :class="{ 'border-brand-primary': isChecked(index) }"
      >
        <!-- Binnenste bolletje -->
        <div
          class="w-2.5 h-2.5 rounded-full bg-brand-primary transition-transform"
          :class="isChecked(index) ? 'scale-100' : 'scale-0'"
        ></div>
      </div>

      <!-- Label -->
      <span class="text-black">{{ option.label }}</span>
    </label>
  </div>
</template>
