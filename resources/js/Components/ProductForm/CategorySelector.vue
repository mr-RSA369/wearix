<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const model = defineModel()

const props = defineProps({
  errors: Object
})

const categories = ref([])
const loading = ref(false)

// Add category UI
const showAdd = ref(false)
const newCategory = ref('')
const parentId = ref(null)

// helper
const getError = (field) => props.errors?.[field]?.[0]

// Load categories
const loadCategories = async () => {
  try {
    loading.value = true
    const res = await axios.get('/api/categories')
    categories.value = res.data
  } catch (e) {
    console.error('Category load error', e)
  } finally {
    loading.value = false
  }
}

// Add category
const addCategory = async () => {
  if (!newCategory.value) return

  try {
    await axios.post('/api/categories', {
      name: newCategory.value,
      parent_id: parentId.value
    })

    newCategory.value = ''
    parentId.value = null
    showAdd.value = false

    await loadCategories()

  } catch (e) {
    console.error(e)
  }
}

onMounted(loadCategories)
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-4">

    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold">Category</h2>

      <button
        @click="showAdd = !showAdd"
        class="bg-indigo-600 text-white px-3 py-1 rounded"
      >
        +
      </button>
    </div>

    <!-- SELECT -->
    <select v-model="model" class="w-full border p-3 rounded">

      <option value="">Select Category</option>

      <template v-for="parent in categories" :key="parent.id">

        <!-- Parent (disabled) -->
        <option :value="parent.id" disabled>
          {{ parent.name }}
        </option>

        <!-- Children -->
        <option
          v-for="child in parent.children"
          :key="child.id"
          :value="child.id"
        >
          └ {{ child.name }}
        </option>

      </template>

    </select>

    <!-- ERROR -->
    <p class="text-red-500 text-sm">
      {{ getError('category_id') }}
    </p>

    <!-- ADD CATEGORY -->
    <div v-if="showAdd" class="space-y-2 border-t pt-4">

      <input
        v-model="newCategory"
        placeholder="Category name"
        class="w-full border p-2 rounded"
      />

      <select v-model="parentId" class="w-full border p-2 rounded">
        <option value="">Parent (optional)</option>

        <option
          v-for="parent in categories"
          :key="parent.id"
          :value="parent.id"
        >
          {{ parent.name }}
        </option>
      </select>

      <button
        @click="addCategory"
        class="bg-green-600 text-white px-4 py-2 rounded"
      >
        Add Category
      </button>

    </div>

  </div>
</template>
