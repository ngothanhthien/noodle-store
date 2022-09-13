<script setup>
const props = defineProps(["icon", "title", "target"]);
</script>
<template>
  <div class="hover:bg-gray-100">
    <router-link
      :to="{ name: props.target }"
      class="flex items-center py-2 px-2 cursor-pointer"
    >
      <div>
        <component :is="icon" class="w-5 h-5 fill-current"></component>
      </div>
      <div class="ml-2">{{ title }}</div>
    </router-link>
  </div>
</template>
