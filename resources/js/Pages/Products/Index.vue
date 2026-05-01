<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProductGrid from '@/Components/Product/ProductGrid.vue'
import ProductFilters from '@/Components/Product/ProductFilters.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const categories = ref([])
const loading = ref(false)

const fetchProducts = async (filters = {}) => {
  loading.value = true

  try {
    const res = await axios.get('/api/products', {
      params: filters
    })

    products.value = res.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories')
    categories.value = res.data
  } catch (err) {
    console.error(err)
  }
}

const applyFilters = (filters) => {
  fetchProducts(filters)
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>

<template>
  <AppLayout>

    <div class="flex gap-6">

      <!-- Filters -->
      <ProductFilters
        :categories="categories"
        @filter="applyFilters"
      />

      <!-- Content -->
      <div class="flex-1">

        <div v-if="loading" class="text-center py-10">
          Loading...
        </div>

        <ProductGrid
          v-else
          :products="products"
        />

      </div>

    </div>

  </AppLayout>
</template>
