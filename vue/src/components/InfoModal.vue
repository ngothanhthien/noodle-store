<script setup>
import { onClickOutside } from '@vueuse/core';
import { ref } from 'vue';
const emits = defineEmits(['close']);
const box = ref(null);
onClickOutside(box, () => emits('close'));
</script>

<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="bg-gray-200 inline-block px-3 py-4 w-96 rounded">
            <div class="mb-4 text-center m-auto">
                <slot name="content"></slot>
            </div>
            <div class="flex justify-center items-center">
                <div @click="emits('close')"
                    class="select-none hover:bg-green-700 cursor-pointer bg-green-600 inline-block w-32 text-center p-1 rounded text-white">
                    <slot name="button"></slot>
                </div>
            </div>
        </div>
    </div>
</template>