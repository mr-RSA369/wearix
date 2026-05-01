<script setup>
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'

import BasicInfo from './BasicInfo.vue'
import ColorManager from './ColorManager.vue'
import MediaUploader from './MediaUploader.vue'
import SizeSelector from './SizeSelector.vue'
import SizeGuideBuilder from './SizeGuideBuilder.vue'
import ProductDetails from './ProductDetails.vue'




const buildFormData = (form) => {
  const fd = new FormData()

  fd.append('name', form.name)
  fd.append('description', form.description)
  fd.append('category_id', form.category_id)

  // COLORS
  form.colors.forEach(c => {
    fd.append(`colors[${c.temp_id}][name]`, c.name)
    fd.append(`colors[${c.temp_id}][price]`, c.price)
  })

  // =========================
// ✅ GENERAL IMAGES (FINAL)
// =========================
form.images.forEach((img, i) => {

    if (img.id) {
    fd.append(`images_existing[${i}]`, img.id)
  }

  if (img.file instanceof File) {
    fd.append(`images_new[${i}]`, img.file)
  }

})


// =========================
// ✅ COLOR IMAGES (FINAL)
// =========================
Object.keys(form.color_images).forEach(colorId => {

  form.color_images[colorId].forEach((img, i) => {

    if (img.id) {
      fd.append(`color_images_existing[${colorId}][${i}]`, img.id)
    }

    if (img.file instanceof File) {
      fd.append(`color_images_new[${colorId}][${i}]`, img.file)
    }
  })

})

  // VIDEO
  if (form.video?.file instanceof File) {
    fd.append('video', form.video.file)
  }

  form.sizes.forEach((id, i) => {
      fd.append(`sizes[${i}]`, id)
  })

  form.details.forEach((d, i) => {
  fd.append(`details[${i}][key]`, d.key)
  fd.append(`details[${i}][value]`, d.value)
  })

  if (form.size_guide) {
  form.size_guide.columns.forEach((c, i) => {
    fd.append(`size_guide[columns][${i}]`, c)
  })

  form.size_guide.rows.forEach((row, i) => {
    row.forEach((cell, j) => {
      fd.append(`size_guide[rows][${i}][${j}]`, cell)
    })
  })
}


  return fd
}

const props = defineProps({
  product: Object
})

const sizes = ref([])
const errors = ref({})

const mapProductToForm = (p) => {
  return {
    name: p.name,
    description: p.description,
    category_id: p.category_id,

    colors: p.colors.map(c => ({
      id: c.id,
      temp_id: c.id,
      name: c.name,
      price: c.price
    })),

    // GENERAL IMAGES
    images: p.media
      .filter(m => m.color_id === null && m.type === 'image')
      .map(m => ({
        id: m.id,
        path: m.path
      })),

    // COLOR IMAGES
    color_images: p.colors.reduce((acc, c) => {
      acc[c.id] = (c.media || []).map(m => ({
        id: m.id,
        path: m.path
      }))
      return acc
    }, {}),

    // VIDEO
    video: p.media.find(m => m.type === 'video') || null,

    sizes: p.sizes.map(s => s.id),

    details: p.details,

    size_guide: p.size_guide?.sizes || {
      columns: [],
      rows: []
    }
  }
}


const form = reactive(
  props.product
    ? mapProductToForm(props.product)
    : {
        name: '',
        description: '',
        category_id: '',
        colors: [],
        images: [],
        color_images: {},
        video: null,
        sizes: [],
        details: [],
        size_guide: { columns: [], rows: [] }
      }
)

// load sizes
onMounted(async () => {
  try {
    const res = await axios.get('/api/sizes')

    sizes.value = res.data

  } catch (e) {
    console.error('Sizes load error:', e)
  }
})

const submit = async () => {
  try {
    const fd = buildFormData(form)

    if (props.product) {
      // ✅ UPDATE
      await axios.post(`/api/products/${props.product.id}?_method=PUT`, fd)
    } else {
      // ✅ CREATE
      await axios.post('/api/products', fd)
    }

    errors.value = {}

  } catch (e) {
    if (e.response && e.response.data) {
      errors.value = e.response.data.errors || {}
    } else {
      console.error('Unexpected error:', e)
    }
  }
}
</script>

<template>
  <div class="space-y-6">

    <BasicInfo v-model="form" :errors="errors" />

    <ColorManager v-model="form.colors" :errors="errors" />

    <MediaUploader
      v-model:images="form.images"
      v-model:colorImages="form.color_images"
      v-model:video="form.video"
      :colors="form.colors"
      :errors="errors"
    />

    <SizeSelector v-model="form.sizes" :sizes="sizes" :errors="errors" />

    <SizeGuideBuilder v-model="form.size_guide" :errors="errors" />

    <ProductDetails v-model="form.details" :errors="errors" />

    <button @click="submit" class="bg-indigo-600 text-white px-6 py-3 rounded">
      Save
    </button>

  </div>
</template>
