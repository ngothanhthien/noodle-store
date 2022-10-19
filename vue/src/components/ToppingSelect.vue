<script setup>
import { computed } from 'vue';

const props= defineProps(['filter','toppings','checked']);
const emits=defineEmits(['toggleTopping','changeFilter']);
const ALL=0;
const SELECTED=2;
const toppingFiltered=computed(()=>{
    if(props.filter==ALL){
        return props.toppings;
    }
    if(props.filter==SELECTED){
        return props.toppings.filter((topping)=>props.checked.includes(topping.id));
    }
});
</script>
<template>
    <div>
        <div class="flex space-x-2 border-b border-gray-400">
            <div @click="emits('changeFilter',ALL)" :class="{'bg-gray-300':filter==ALL}" class="py-1 px-2 rounded-t-md cursor-pointer select-none hover:bg-gray-300">
                <span>Tất cả</span>
                <span class="ml-1">({{toppings.length}})</span>
            </div>
            <div @click="emits('changeFilter',SELECTED)" :class="{'bg-gray-300':filter==SELECTED}" class="py-1 px-2 rounded-t-md select-none cursor-pointer hover:bg-gray-300">
                <span>Đã chọn</span>
                <span class="ml-1">({{checked.length}})</span>
            </div>
        </div>
        <div class="flex flex-wrap h-24 overflow-y-auto pt-2 bg-white content-start">
            <div @click="emits('toggleTopping',topping.id)" v-for="topping in toppingFiltered" class="px-2 mr-2 hover:bg-gray-300 py-0.5 flex items-center space-x-1 cursor-pointer select-none accent-green-600">
                <input :checked="checked.includes(topping.id)" type="checkbox" class="mt-0.5">
                <div>{{topping.name}}</div>
            </div>
        </div>
    </div>
</template>