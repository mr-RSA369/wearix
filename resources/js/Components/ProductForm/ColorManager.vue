<script setup>
import { ref } from 'vue'



const model = defineModel()

const props = defineProps({
  errors: Object
})

const getError = (field) => props.errors?.[field]?.[0]

const newColor = ref('')
const newPrice = ref('')

const addColor = () => {
  if (!newColor.value) return

  model.value.push({
    temp_id: Date.now() + Math.random(), // unique
    name: newColor.value,
    price: newPrice.value
  })

  newColor.value = ''
  newPrice.value = ''
}

const removeColor = (index) => {
  model.value.splice(index, 1)
}
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-semibold mb-4">Colors & Pricing</h2>

    <div class="flex gap-2 mb-4">
      <input v-model="newColor" placeholder="Color name" class="border p-2 rounded" />
      <input v-model="newPrice" placeholder="Price" class="border p-2 rounded" />
      <button @click="addColor" class="bg-indigo-500 text-white px-4 rounded">+</button>
    </div>

    <div v-for="(color, i) in model" :key="i" class="space-y-1">

      <div class="flex justify-between border p-2 rounded">
        <span>{{ color.name }} - {{ color.price }}</span>
        <button @click="removeColor(i)" class="text-red-500">x</button>
      </div>

      <!-- ERRORS -->
      <p class="text-red-500 text-sm">
        {{ getError(`colors.${i}.name`) }}
      </p>

      <p class="text-red-500 text-sm">
        {{ getError(`colors.${i}.price`) }}
      </p>

    </div>

  </div>
</template>
