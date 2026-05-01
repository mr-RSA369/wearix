<template>
  <div class="min-h-screen bg-gradient-to-b from-orange-50/30 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 lg:py-12">

      <!-- Main Product Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 xl:gap-16">

        <!-- LEFT COLUMN: Image Gallery with Slider -->
        <div class="space-y-4">
          <!-- Main Display Image -->
          <div class="relative group overflow-hidden rounded-2xl bg-white shadow-xl">
            <Transition name="fade" mode="out-in">
              <img
                :key="displayImage?.id || 'main'"
                :src="displayImage?.path || currentImages[0]?.path"
                class="w-full object-cover transition-transform duration-700 group-hover:scale-105"
                alt="Product main view"
              />
            </Transition>
            <!-- Image counter badge -->
            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
              {{ currentImageIndex + 1 }} / {{ currentImages.length }}
            </div>
          </div>

          <!-- Thumbnail Slider -->
          <div class="relative">
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-orange-300 scrollbar-track-gray-100">
              <button
                v-for="(img, idx) in currentImages"
                :key="img.id"
                @click="setCurrentImage(idx)"
                class="flex-shrink-0 transition-all duration-300 rounded-xl overflow-hidden border-2"
                :class="currentImageIndex === idx ? 'border-orange-500 shadow-lg shadow-orange-200 scale-105' : 'border-transparent hover:border-orange-300'"
              >
                <img :src="img.path" class="w-10 h-10 sm:w-12 sm:h-12 object-cover" />
              </button>
            </div>
            <!-- Scroll hint -->
            <div class="absolute right-0 top-1/2 -translate-y-1/2 bg-gradient-to-l from-white to-transparent w-12 h-full pointer-events-none lg:hidden"></div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Product Info -->
        <div class="space-y-6">
          <!-- Brand / Category Badge -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-medium px-3 py-1 bg-orange-100 text-orange-700 rounded-full">NEW ARRIVAL</span>
            <span class="text-xs text-gray-500 flex items-center gap-1">
              <span class="text-yellow-400">★</span>
              <span>4.9 (128 reviews)</span>
            </span>
          </div>

          <!-- Title -->
          <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
            {{ product.name }}
          </h1>

          <!-- Price with animated badge -->
          <div class="flex items-baseline gap-3 flex-wrap">
            <p class="text-3xl font-bold text-orange-600">
              Rs. {{ currentPrice }}
            </p>
            <p class="text-sm text-gray-400 line-through" v-if="product.comparePrice">
              Rs. {{ product.comparePrice }}
            </p>
            <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full">Free Shipping</span>
          </div>

          <!-- Color Selection -->
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <h3 class="font-semibold text-gray-800">Color: <span class="text-orange-600">{{ selectedColor?.name || 'Choose' }}</span></h3>
            </div>
            <div class="flex gap-4 flex-wrap">
              <button
                v-for="color in colorOptions"
                :key="color.id"
                @click="selectedColor = color"
                class="group relative transition-all duration-300"
              >
                <div class="w-12 h-12 rounded-xl overflow-hidden border-2 transition-all"
                  :class="selectedColor?.id === color.id ? 'border-orange-500 shadow-lg shadow-orange-200 scale-105' : 'border-gray-200 hover:border-orange-300'"
                >
                  <img :src="color.firstImage?.path" class="w-full h-full object-cover" />
                </div>
                <p class="text-xs text-center mt-1 font-medium"
                  :class="selectedColor?.id === color.id ? 'text-orange-600' : 'text-gray-600'"
                >{{ color.name }}</p>
              </button>
            </div>
          </div>

          <!-- Size Selection -->
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <h3 class="font-semibold text-gray-800">Size: <span class="text-orange-600">{{ selectedSizeName || 'Select' }}</span></h3>
              <button
                @click="showSizeGuide = true"
                class="text-sm text-blue-500 hover:text-blue-700 transition flex items-center gap-1"
              >
             Size Guide
              </button>
            </div>
            <div class="flex gap-3 flex-wrap">
              <button
                v-for="size in product.sizes"
                :key="size.id"
                @click="selectedSize = size.id; selectedSizeName = size.name"
                class="relative w-12 h-12 flex items-center justify-center rounded-xl font-medium transition-all duration-300"
                :class="selectedSize === size.id
                  ? 'bg-orange-500 text-white shadow-lg shadow-orange-200 scale-105'
                  : 'bg-gray-100 text-gray-700 hover:bg-orange-100 hover:text-orange-600'"
              >
                {{ size.name }}
                <span v-if="!size.inStock" class="absolute -top-1 -right-1 w-3 h-3 bg-red-400 rounded-full"></span>
              </button>
            </div>
            <p class="text-xs text-gray-400" v-if="!isSizeInStock">⚠️ Selected size is out of stock</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4 pt-4">
            <button
              class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-md hover:shadow-xl flex items-center justify-center gap-2"
              @click="addToCart"
            >
              <span>🛒</span>
              Add to Cart
            </button>
            <button class="p-3 rounded-xl border border-gray-300 hover:border-orange-300 hover:bg-orange-50 transition-all duration-300">
              <span class="text-gray-600 hover:text-red-500 transition text-xl">❤️</span>
            </button>
          </div>

          <!-- Short Description -->
          <div class="pt-4 border-t border-gray-100">
            <p class="text-gray-600 text-sm leading-relaxed">{{ product.description?.slice(0, 120) }}...</p>
          </div>

          <!-- Key Highlights Pills -->
          <div class="flex gap-2 flex-wrap">
            <span v-for="tag in product.tags?.slice(0, 3)" :key="tag" class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
              {{ tag }}
            </span>
          </div>
        </div>
      </div>

      <!-- DETAILS TABS SECTION -->
      <div class="mt-16">
        <div class="border-b border-gray-200 flex gap-6 overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            class="pb-3 text-sm font-medium transition-all duration-300 relative"
            :class="activeTab === tab.key ? 'text-orange-600' : 'text-gray-500 hover:text-gray-700'"
          >
            {{ tab.label }}
            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-orange-500 transition-all duration-300"
              :class="activeTab === tab.key ? 'scale-x-100' : 'scale-x-0'"></span>
          </button>
        </div>

        <div class="py-8">
          <!-- Details Tab Content -->
          <div v-if="activeTab === 'details'" class="space-y-4">
            <Transition name="slide-fade" mode="out-in">
              <div class="space-y-4" :key="'details' + showAllDetails">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div
                    v-for="d in visibleDetails"
                    :key="d.id"
                    class="flex justify-between items-center p-3 bg-gray-50 rounded-xl hover:bg-orange-50 transition-colors"
                  >
                    <span class="font-medium text-gray-700">{{ d.key }}</span>
                    <span class="text-gray-900">{{ d.value }}</span>
                  </div>
                </div>
                <div v-if="product.details?.length > 3">
                  <button
                    @click="showAllDetails = !showAllDetails"
                    class="text-orange-500 hover:text-orange-600 text-sm font-medium transition flex items-center gap-1"
                  >
                    {{ showAllDetails ? 'Show Less ↑' : 'Show More ↓' }}
                  </button>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Description Tab Content -->
          <div v-if="activeTab === 'description'">
            <Transition name="slide-fade" mode="out-in">
              <div class="prose max-w-none" :key="'description'">
                <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
              </div>
            </Transition>
          </div>

          <!-- Reviews Tab Content -->
          <div v-if="activeTab === 'reviews'">
            <Transition name="slide-fade" mode="out-in">
              <div class="space-y-6" :key="'reviews'">
                <div class="text-center py-12 bg-gray-50 rounded-2xl">
                  <span class="text-6xl text-gray-300 mb-3 inline-block">💬</span>
                  <p class="text-gray-500">No reviews yet. Be the first to review!</p>
                  <button class="mt-4 text-orange-500 text-sm font-medium">Write a Review →</button>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </div>

      <!-- GALLERY SECTION -->
      <div class="mt-16" v-if="generalImages.length > 0">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
          <span class="text-orange-500 text-2xl">📷</span>
          Gallery
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div
            v-for="img in generalImages"
            :key="img.id"
            class="group overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-500"
          >
            <img
              :src="img.path"
              class="w-full h-48 md:h-64 object-cover transition-transform duration-700 group-hover:scale-110"
            />
          </div>
        </div>
      </div>

      <!-- SIZE GUIDE MODAL -->
      <Transition name="modal">
        <div v-if="showSizeGuide" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showSizeGuide = false">
          <div class="bg-white rounded-2xl max-w-lg w-full max-h-[80vh] overflow-auto shadow-2xl">
            <div class="sticky top-0 bg-white p-4 border-b flex justify-between items-center">
              <h3 class="font-bold text-lg">Size Guide</h3>
              <button @click="showSizeGuide = false" class="p-1 rounded-full hover:bg-gray-100">
                <span class="text-gray-500 text-xl">✕</span>
              </button>
            </div>
            <div class="p-4">
              <table class="w-full text-sm border-collapse">
                <thead>
                  <tr class="bg-orange-50">
                    <th v-for="(col, i) in product.size_guide?.sizes?.columns" :key="i" class="border p-2 text-left">
                      {{ col }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in product.size_guide?.sizes?.rows" :key="i" class="hover:bg-gray-50">
                    <td v-for="(cell, j) in row" :key="j" class="border p-2">
                      {{ cell }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </Transition>

      <!-- TOAST NOTIFICATION -->
      <Transition name="toast">
        <div v-if="toastVisible" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 z-50">
          <span class="text-green-400">✓</span>
          {{ toastMessage }}
        </div>
      </Transition>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      price: 0,
      description: '',
      media: [],
      colors: [],
      sizes: [],
      details: [],
      tags: [],
      size_guide: { sizes: { columns: [], rows: [] } }
    })
  }
})

// =========================
// STATE
// =========================
const selectedColor = ref(null)
const selectedSize = ref(null)
const selectedSizeName = ref('')
const showAllDetails = ref(false)
const showSizeGuide = ref(false)
const activeTab = ref('details')
const currentImageIndex = ref(0)
const toastVisible = ref(false)
const toastMessage = ref('')

// =========================
// TABS
// =========================
const tabs = [
  { key: 'details', label: 'Details' },
  { key: 'description', label: 'Description' },
  { key: 'reviews', label: 'Reviews' }
]

// =========================
// HELPERS
// =========================
const generalImages = computed(() =>
  props.product.media?.filter(m => m.color_id === null && m.type === 'image') || []
)

const colorOptions = computed(() =>
  props.product.colors?.map(c => ({
    ...c,
    firstImage: c.media?.[0] || null
  })) || []
)

// =========================
// CURRENT DISPLAY IMAGES
// =========================
const currentImages = computed(() => {
  if (selectedColor.value) {
    return selectedColor.value.media || []
  }
  return [
    ...generalImages.value,
    ...colorOptions.value.map(c => c.firstImage).filter(Boolean)
  ]
})

const displayImage = computed(() => currentImages.value[currentImageIndex.value])

const setCurrentImage = (idx) => {
  currentImageIndex.value = idx
}

// =========================
// CURRENT PRICE
// =========================
const currentPrice = computed(() => {
  return selectedColor.value?.price || props.product.price
})

// =========================
// DETAILS
// =========================
const visibleDetails = computed(() => {
  if (showAllDetails.value) return props.product.details || []
  return props.product.details?.slice(0, 3) || []
})

// =========================
// SIZE STOCK CHECK
// =========================
const isSizeInStock = computed(() => {
  const size = props.product.sizes?.find(s => s.id === selectedSize.value)
  return size ? size.inStock !== false : true
})

// =========================
// ADD TO CART
// =========================
const addToCart = () => {
  if (!selectedSize.value) {
    toastMessage.value = 'Please select a size'
    toastVisible.value = true
    setTimeout(() => toastVisible.value = false, 2000)
    return
  }
  if (!isSizeInStock.value) {
    toastMessage.value = 'Selected size is out of stock'
    toastVisible.value = true
    setTimeout(() => toastVisible.value = false, 2000)
    return
  }
  toastMessage.value = 'Added to cart! ✨'
  toastVisible.value = true
  setTimeout(() => toastVisible.value = false, 2000)
}
</script>

<style scoped>
/* Scrollbar styling */
.scrollbar-thin::-webkit-scrollbar {
  height: 4px;
}
.scrollbar-thumb-orange-300::-webkit-scrollbar-thumb {
  background-color: #fdba74;
  border-radius: 20px;
}
.scrollbar-track-gray-100::-webkit-scrollbar-track {
  background-color: #f3f4f6;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}
.slide-fade-enter-from {
  transform: translateX(10px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateX(-10px);
  opacity: 0;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.2s ease;
}
.modal-enter-from .bg-white {
  transform: scale(0.95);
}
.modal-leave-to .bg-white {
  transform: scale(0.95);
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(20px);
}
</style>
