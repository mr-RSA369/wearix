<script setup>
import { ref } from 'vue'

// detect existing image
const isExisting = (file) => file && file.path

// refs for file inputs
const colorRefs = {}
const setColorRef = (el, id) => {
  if (el) colorRefs[id] = el
}
const triggerColorFile = (id) => {
  colorRefs[id]?.click()
}

// models
const images = defineModel('images') // general
const colorImages = defineModel('colorImages') // per color
const video = defineModel('video')

const props = defineProps({
  colors: Array,
  errors: Object
})

const getError = (field) => props.errors?.[field]?.[0]

// refs
const generalRef = ref()
const videoRef = ref()

const triggerGeneral = () => generalRef.value?.click()

// =====================
// GENERAL IMAGES
// =====================
const pickImages = (e) => {
  const files = Array.from(e.target.files)

  files.forEach(file => {
    images.value.push({
      file: file,
      preview: URL.createObjectURL(file)
    })
  })
}

// =====================
// COLOR IMAGES
// =====================
const pickColorImages = (color, e) => {
  const files = Array.from(e.target.files)

  if (!colorImages.value[color.temp_id]) {
    colorImages.value[color.temp_id] = []
  }

  files.forEach(file => {
    colorImages.value[color.temp_id].push({
      file: file,
      preview: URL.createObjectURL(file)
    })
  })
}

// =====================
// VIDEO
// =====================
const pickVideo = (e) => {
      const file = e.target.files[0]

      video.value = {
        file,
        preview: URL.createObjectURL(file)
      }

      e.target.value = ''
    }
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-6">

    <h2 class="font-semibold text-lg">Media</h2>

    <!-- ===================== -->
    <!-- GENERAL IMAGES -->
    <!-- ===================== -->
    <div>
      <p class="mb-2 font-medium">General Images</p>

      <div
        class="border-2 border-dashed p-6 text-center cursor-pointer"
        @click="triggerGeneral"
      >
        ➕ Add Images (No Color)
      </div>

      <input
        ref="generalRef"
        type="file"
        multiple
        class="hidden"
        @change="pickImages"
      />

      <!-- SHOW IMAGES -->
      <div class="flex gap-2 flex-wrap mt-4">
        <div v-for="(img, i) in images" :key="i" class="relative">

          <img
              v-if="img.path"
              :src="img.path"
              class="w-24 h-24 object-cover rounded"
            />

           <img
              v-else
              :src="img.preview"
              class="w-24 h-24 object-cover rounded"
            />

          <button
            @click="images.splice(i,1)"
            class="absolute top-0 right-0 bg-red-500 text-white px-1 text-xs"
          >
            x
          </button>

        </div>
      </div>

      <p class="text-red-500 text-sm mt-1">
        {{ getError('images') }}
      </p>
    </div>

    <!-- ===================== -->
    <!-- COLOR IMAGES -->
    <!-- ===================== -->
    <div>
      <p class="mb-2 font-medium">Color Images</p>

      <div
        v-for="color in colors"
        :key="color.temp_id"
        class="mb-6"
      >

        <p class="text-sm mb-2 font-medium">
          {{ color.name }}
        </p>

        <!-- Upload Box -->
        <div
          class="border-2 border-dashed p-4 text-center cursor-pointer"
          @click="triggerColorFile(color.temp_id)"
        >
          ➕ Add Images for {{ color.name }}
        </div>

        <input
          type="file"
          multiple
          class="hidden"
          :ref="el => setColorRef(el, color.temp_id)"
          @change="e => pickColorImages(color, e)"
        />

        <!-- SHOW COLOR IMAGES -->
        <div class="flex gap-2 flex-wrap mt-3">
          <div
            v-for="(img, i) in (colorImages[color.temp_id] || [])"
            :key="i"
            class="relative"
          >

            <img
              v-if="img.path"
              :src="img.path"
              class="w-20 h-20 object-cover rounded"
            />

            <img
              v-else
              :src="img.preview"
              class="w-20 h-20 object-cover rounded"
            />

            <button
              @click="colorImages[color.temp_id].splice(i,1)"
              class="absolute top-0 right-0 bg-red-500 text-white px-1 text-xs"
            >
              x
            </button>

          </div>
        </div>

      </div>

      <p class="text-red-500 text-sm">
        {{ getError('color_images') }}
      </p>
    </div>

    <!-- ===================== -->
    <!-- VIDEO -->
    <!-- ===================== -->
    <div>
      <p class="mb-2 font-medium">Product Video</p>

      <div
        class="border-2 border-dashed p-6 text-center cursor-pointer"
        @click="videoRef.click()"
      >
        🎥 Upload Video
      </div>

      <input
        ref="videoRef"
        type="file"
        class="hidden"
        @change="pickVideo"
      />

      <!-- SHOW VIDEO -->
      <div v-if="video" class="mt-3">
        <video
          v-if="video.path"
          :src="video.path"
          controls
          class="w-48"
        />

        <video
          v-else
          :src="video.preview"
          controls
          class="w-48"
        />

        <button
          @click="video = null"
          class="block mt-2 text-red-500 text-sm"
        >
          Remove Video
        </button>
      </div>

      <p class="text-red-500 text-sm mt-1">
        {{ getError('video') }}
      </p>
    </div>

  </div>
</template>
