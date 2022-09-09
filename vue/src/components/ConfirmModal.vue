<script setup>
import { ref } from 'vue';
import { onClickOutside } from '@vueuse/core'
const emits=defineEmits(['clickOutside','cancel','confirm']);
const props=defineProps(['visible']);
const box=ref(null);
onClickOutside(box, () => emits('clickOutside'))
</script>
<template>
    <div v-if="visible" class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="bg-gray-200 inline-block px-3 py-4 w-96">
            <div class="mb-4 text-center w-40 m-auto">
                <slot name="text"></slot>
            </div>
            <div class="flex">
                <div @click="$emit('cancel')" class="bg-gray-700 hover:bg-gray-600 transition cursor-pointer text-white py-1 text-center w-32">
                    Hủy bỏ
                </div>
                <div @click="$emit('confirm')"
                    class="bg-green-700 hover:bg-green-600 transition cursor-pointer ml-auto text-white py-1 text-center w-32">
                    Xác nhận
                </div>
            </div>
        </div>
    </div>
</template>