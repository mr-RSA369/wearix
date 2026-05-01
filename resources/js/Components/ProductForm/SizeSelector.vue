<script setup>
const model = defineModel()

const props = defineProps({
  sizes: Array,
  errors: Object
})

const getError = (field) => props.errors?.[field]?.[0]
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Sizes</h2>

    <div class="flex gap-2 flex-wrap">
      <label v-for="size in sizes" :key="size.id">
        <input type="checkbox" :value="size.id" v-model="model" />
        {{ size.name }}
      </label>
    </div>

    <p class="text-red-500 text-sm mt-2">
      {{ getError('sizes') }}
    </p>

  </div>
</template>
