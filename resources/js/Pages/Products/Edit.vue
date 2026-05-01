<script setup>
import ProductForm from '@/Components/ProductForm/ProductForm.vue'
import { onMounted } from 'vue'

const props = defineProps({
  product: Object
})
console.log('PRODUCT:', props.product)

</script>

<template>
  <div v-if="product">
  <ProductForm :product="product" />
  </div>
    <div v-else class="text-center py-10 text-gray-500">
        Loading...
        </div>
</template>
