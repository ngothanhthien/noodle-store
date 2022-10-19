<script setup>
import RemoveIcon from './icons/RemoveIcon.vue';
import AddIcon from './icons/AddIcon.vue';</script>
<template>
    <div class="bg-white flex max-w-fit items-center rounded-md overflow-hidden">
        <div class="flex items-center group">
            <div class="cursor-pointer p-1 bg-green-600 group-hover:bg-green-700 text-white">
                <AddIcon class="w-5 h-5 fill-current" />
            </div>
            <div class="cursor-pointer flex space-x-1 items-center px-2">
                <div class="text-red-700 font-bold">0</div>
                <div>Xúc Xích</div>
            </div>
        </div>
        <div class="cursor-pointer p-1 hover:bg-red-700 bg-red-600 text-white">
            <RemoveIcon class="w-5 h-5 fill-current" />
        </div>
    </div>
</template>