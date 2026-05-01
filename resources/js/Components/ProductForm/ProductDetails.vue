<script setup>
const model = defineModel()

const props = defineProps({
  errors: Object
})

const getError = (field) => props.errors?.[field]?.[0]

if (!model.value) model.value = []

const addDetail = () => {
  model.value.push({ key: '', value: '' })
}

const removeDetail = (index) => {
  model.value.splice(index, 1)
}
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-4">

    <h2 class="text-xl font-semibold">Product Details</h2>

    <div v-for="(item, index) in model" :key="index" class="space-y-1">

      <div class="flex gap-2">

        <input
          v-model="item.key"
          placeholder="Key"
          class="border p-2 rounded w-1/2"
        />

        <input
          v-model="item.value"
          placeholder="Value"
          class="border p-2 rounded w-1/2"
        />

        <button @click="removeDetail(index)" class="text-red-500">x</button>
      </div>

      <!-- ERRORS -->
      <p class="text-red-500 text-sm">
        {{ getError(`details.${index}.key`) }}
      </p>

      <p class="text-red-500 text-sm">
        {{ getError(`details.${index}.value`) }}
      </p>

    </div>

    <button @click="addDetail" class="bg-indigo-600 text-white px-4 py-2 rounded">
      Add Detail
    </button>

  </div>
</template>
