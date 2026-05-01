<template>
  <div class="w-64">

    <input
      v-model="search"
      placeholder="Search products..."
      class="w-full border p-2 rounded mb-4"
    />

    <select v-model="category" class="w-full border p-2 rounded">
      <option value="">All Categories</option>
      <option v-for="cat in categories" :key="cat.id" :value="cat.id">
        {{ cat.name }}
      </option>
    </select>

    <button @click="apply" class="mt-4 w-full bg-indigo-600 text-white py-2 rounded">
      Apply
    </button>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['filter'])

const search = ref('')
const category = ref('')

const apply = () => {
  emit('filter', {
    search: search.value,
    category: category.value
  })
}
</script>
