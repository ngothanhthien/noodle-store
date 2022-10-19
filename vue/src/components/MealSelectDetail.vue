<script setup>
import ButtonClose from './ButtonClose.vue';
import ToppingAdd from './ToppingAdd.vue';
</script>
<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-2/3 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="flex">
                <div class="w-1/2 pr-2 py-2">
                    <img class="w-full" src="https://random.imagecdn.app/400/300">
                </div>
                <div class="w-1/2 p-2">
                    <div>Chọn Topping:</div>
                    <div class="mt-2">
                        <ToppingAdd />
                    </div>
                </div>
            </div>
            <button class="absolute bottom-2 right-2 p-2 bg-blue-600 text-white hover:bg-blue-700 rounded w-24">Thêm</button>
        </div>
    </div>
</template>