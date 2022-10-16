<script setup>
const props = defineProps(["icon", "title", "target"]);
</script>
<template>
    <router-link
      :to="{ name: props.target }"
      class="flex items-center py-2 px-2 cursor-pointer hover:bg-gray-100"
    >
      <div>
        <component :is="icon" class="w-5 h-5 fill-current"></component>
      </div>
      <div class="ml-2">{{ title }}</div>
    </router-link>
</template>
