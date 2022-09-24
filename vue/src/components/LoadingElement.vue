<script setup>
import RefreshIcon from './icons/RefreshIcon.vue';
const props=defineProps(['isNotFound']);
</script>
<template>
    <div v-if="!isNotFound" class="flex items-center">
        <div class="animate-spin h-8 w-8">
            <RefreshIcon class="w-full h-full fill-current" />
        </div>
        <div>
            Đang tải dữ liệu...
        </div>
    </div>
    <div v-else>
        Không tìm thấy dữ liệu
    </div>
</template>