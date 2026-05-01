<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

// Access global props (auth user)
const page = usePage()

const user = computed(() => page.props.auth?.user)

// Role check
const isAdmin = computed(() => user.value?.role === 'admin')
</script>

<template>
  <div class="min-h-screen bg-gray-100">

    <!-- NAVBAR -->
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo / Brand -->
        <Link href="/products" class="text-xl font-bold text-indigo-600">
          Wearix
        </Link>

        <!-- Navigation Links -->
        <div class="flex items-center gap-6">

          <Link href="/products" class="text-gray-700 hover:text-indigo-600">
            Products
          </Link>

          <!-- Admin Only -->
          <Link
            v-if="isAdmin"
            href="/products/create"
            class="text-gray-700 hover:text-indigo-600"
          >
            Add Product
          </Link>

          <!-- User Info -->
          <div v-if="user" class="flex items-center gap-3">

            <span class="text-sm text-gray-600">
              {{ user.name }}
            </span>

            <Link
              href="/logout"
              method="post"
              as="button"
              class="text-red-500 text-sm"
            >
              Logout
            </Link>

          </div>

        </div>

      </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-6">
      <slot />
    </main>

  </div>
</template>
