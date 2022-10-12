<script setup>
import DoneIcon from './icons/DoneIcon.vue';
import UndoIcon from './icons/UndoIcon.vue';
import CancelScheduleSendIcon from './icons/CancelScheduleSendIcon.vue';
const props= defineProps(['state']);
const emits= defineEmits(['cancel','fail','success']);
const ORDER_SUCCESS=0;
const ORDER_DELIVERY=1;
const ORDER_FAIL=2;
const ORDER_CANCEL=3;
</script>
<template>
    <div v-if="state!=ORDER_CANCEL" class="flex p-2 max-w-fit space-x-4">
        <div v-if="state==ORDER_DELIVERY" @click="emits('success')"
            class="p-1.5 hover:bg-green-600 bg-green-500 cursor-pointer text-white rounded">
            <DoneIcon class="w-5 h-5 fill-current" />
        </div>
        <div @click="emits('cancel')"
            class="p-1.5 bg-gray-500 cursor-pointer hover:bg-gray-600 text-white rounded">
            <UndoIcon class="w-5 h-5 fill-current" />
        </div>
        <div v-if="state==ORDER_SUCCESS||state==ORDER_DELIVERY" @click="emits('fail')"
            class="p-1.5 hover:bg-red-600 bg-red-500 cursor-pointer text-white rounded">
            <CancelScheduleSendIcon class="w-5 h-5 fill-current" />
        </div>
    </div>
</template>