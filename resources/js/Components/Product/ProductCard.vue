<template>
  <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

    <!-- MEDIA SLIDER -->
    <div class="relative w-full h-56 overflow-hidden">

      <!-- Image -->
      <img
        v-if="currentMedia?.type === 'image'"
        :src="currentMedia.path"
        class="w-full h-56 object-cover"
      />

      <!-- Video -->
      <video
        v-else-if="currentMedia?.type === 'video'"
        :src="currentMedia.path"
        class="w-full h-56 object-cover"
        muted
        autoplay
        loop
      />

      <!-- Fallback -->
      <div v-else class="w-full h-56 bg-gray-200 flex items-center justify-center">
        No Media
      </div>

    </div>

    <!-- INFO -->
    <div class="p-4">
      <h3 class="font-semibold text-lg truncate">
        {{ product.name }}
      </h3>

      <p class="text-gray-500 text-sm mt-1">
        {{ product.category?.name }}
      </p>

      <div class="flex justify-between items-center mt-3">
        <span class="text-indigo-600 font-bold">
          ${{ product.price }}
        </span>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  product: Object
})

const currentIndex = ref(0)
const currentMedia = ref(null)
let interval = null

const startSlider = () => {
  if (!props.product.media || props.product.media.length === 0) return

  currentMedia.value = props.product.media[0]

  interval = setInterval(() => {
    currentIndex.value =
      (currentIndex.value + 1) % props.product.media.length

    currentMedia.value = props.product.media[currentIndex.value]

  }, 3000) // 3 seconds
}

onMounted(() => {
  startSlider()
})

onBeforeUnmount(() => {
  clearInterval(interval)
})
</script>
