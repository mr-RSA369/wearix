<script setup>
import { reactive } from 'vue'

const model = defineModel()

// Initialize structure
if (!model.value) {
  model.value = {
    columns: ['Size'],
    rows: []
  }
}

// Add column
const addColumn = () => {
  model.value.columns.push('New Column')

  model.value.rows.forEach(row => {
    row.push('')
  })
}

// Remove column
const removeColumn = (index) => {
  model.value.columns.splice(index, 1)
  model.value.rows.forEach(row => row.splice(index, 1))
}

// Add row
const addRow = () => {
  const newRow = model.value.columns.map(() => '')
  model.value.rows.push(newRow)
}

// Remove row
const removeRow = (index) => {
  model.value.rows.splice(index, 1)
}
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-4">

    <h2 class="text-xl font-semibold">Size Guide</h2>

    <!-- Columns -->
    <div class="flex gap-2">
      <div v-for="(col, i) in model.columns" :key="i" class="flex items-center gap-1">
        <input
          v-model="model.columns[i]"
          class="border p-2 rounded w-32"
        />
        <button @click="removeColumn(i)" class="text-red-500">x</button>
      </div>

      <button @click="addColumn" class="bg-indigo-500 text-white px-3 rounded">
        +
      </button>
    </div>

    <!-- Rows -->
    <div v-for="(row, rIndex) in model.rows" :key="rIndex" class="flex gap-2">

      <input
        v-for="(cell, cIndex) in row"
        :key="cIndex"
        v-model="model.rows[rIndex][cIndex]"
        class="border p-2 rounded w-32"
      />

      <button @click="removeRow(rIndex)" class="text-red-500">x</button>

    </div>

    <button @click="addRow" class="bg-green-600 text-white px-4 py-2 rounded">
      Add Row
    </button>

  </div>
</template>
