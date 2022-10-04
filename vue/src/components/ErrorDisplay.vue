<script setup>
const props= defineProps(['error']);
</script>

<template>
    <div class="text-red-600 absolute top-0">
        {{error}}
    </div>
</template>