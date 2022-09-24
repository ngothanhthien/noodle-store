<script setup>
const props=defineProps(['title','target']);
</script>

<template>
    <div class="hover:bg-gray-50 cursor-pointer flex items-center">
    <router-link
      :to="{ name: props.target }" class="p-2 w-full"
    >
      <div>{{ title }}</div>
    </router-link>
  </div>
</template>

<style lang="postcss" scoped>
.router-link-exact-active{
    @apply bg-gray-300
}   
</style>