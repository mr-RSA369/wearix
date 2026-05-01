<script setup>
import { onMounted, ref, computed } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    error: {
        type: String,
        default: null,
    }
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

// Dynamic class based on error
const inputClass = computed(() => {
    return props.error
        ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
});
</script>

<template>
    <input
        :class="[
    'rounded-md shadow-sm w-full',
    inputClass
]"
        v-model="model"
        ref="input"
    />
</template>
