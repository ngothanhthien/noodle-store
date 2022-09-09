<script setup>
const props= defineProps(['isChecked']);
const emits= defineEmits(['check']);
</script>

<template>
    <div @click="emits('check')" :class="{checked:isChecked}" 
    class="py-1 px-2 border select-none border-gray-700 inline-block mr-2 cursor-pointer">
        <slot></slot>
    </div>
</template>

<style lang="postcss">
.checked{
    @apply bg-green-700 text-white border-green-700
}
</style>