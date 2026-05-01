<template>
  <div>

    <!-- Main -->
    <div class="mb-4">
      <img v-if="active.type === 'image'" :src="active.path" class="w-full rounded" />
      <video v-else controls class="w-full rounded">
        <source :src="active.path" />
      </video>
    </div>

    <!-- Thumbnails -->
    <div class="flex gap-2">
      <div
        v-for="item in media"
        @click="active = item"
        class="cursor-pointer"
      >
        <img v-if="item.type === 'image'" :src="item.path" class="w-16 h-16 object-cover" />
        <span v-else>🎥</span>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({ media: Array })
const active = ref(props.media[0])
</script>
