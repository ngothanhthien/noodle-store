<script setup>
import AddIcon from './icons/AddIcon.vue';
import ExpandMoreIcon from './icons/ExpandMoreIcon.vue';
const props = defineProps(["icon", "title","isExpanded"]);
const emits=defineEmits(['toggle']);
</script>
<template>
    <div>
        <div @click="emits('toggle')" :class="{'bg-gray-100':isExpanded}" class="hover:bg-gray-100 flex items-center py-2 px-2 cursor-pointer">
            <div>
                <component :is="icon" class="w-5 h-5 fill-current"></component>
            </div>
            <div class="ml-2">{{ title }}</div>
            <div class="ml-auto"><ExpandMoreIcon class="w-5 h-5 fill-current"/></div>
        </div>
        <div class="ml-9" v-if="isExpanded">
            <slot></slot>
        </div>
    </div>
    
</template>