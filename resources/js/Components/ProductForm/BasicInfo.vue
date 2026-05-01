<script setup>
import CategorySelector from './CategorySelector.vue'
const model = defineModel()
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-4">

    <h2 class="text-xl font-semibold">Basic Information</h2>

    <input
      v-model="model.name"
      placeholder="Product Name"
      class="w-full border p-3 rounded"
    />
    <p class="text-red-500 text-sm">{{ errors?.name?.[0] }}</p>

    <textarea
      v-model="model.description"
      placeholder="Description"
      class="w-full border p-3 rounded"
    ></textarea>
    <p class="text-red-500 text-sm">{{ errors?.description?.[0] }}</p>

    <CategorySelector
      v-model="model.category_id"
      :errors="errors"
    />
    <p class="text-red-500 text-sm">{{ errors?.category_id?.[0] }}</p>
  </div>
</template>
