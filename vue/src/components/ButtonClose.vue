<script setup>
import CloseIcon from './icons/CloseIcon.vue';
const emits=defineEmits('close');
</script>

<template>
    <div @click="$emit('close')" class="absolute cursor-pointer top-1 right-1 w-5 h-5 bg-red-500 hover:bg-red-600 rounded-full text-white p-0.5">
        <CloseIcon class="w-full h-full fill-current" />
    </div>
</template>