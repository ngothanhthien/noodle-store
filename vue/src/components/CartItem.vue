<script setup>
import { computed } from "@vue/reactivity";
import { numberToMoney } from "../logic/money"
import AddIcon from "./icons/AddIcon.vue";
import RemoveIcon from "./icons/RemoveIcon.vue";
const props = defineProps(['item']);
const emits = defineEmits(['increase', 'decrease']);
const totalPrice = computed(() => {
    return numberToMoney(props.item.quality * props.item.price);
})
</script>

<template>
    <div class="flex bg-gray-50 mb-1 hover:bg-gray-100">
        <div class="h-20 w-20">
            <img class="w-full h-full" src="https://api.lorem.space/image/burger?w=200&h=200" alt="">
        </div>
        <div class="ml-2">
            <div> 
                {{item.title}}
            </div>
            <div class="text-red-600"> 
                {{totalPrice}}
            </div>
        </div>
        <div class="ml-auto mr-2 flex items-center select-none">
            <div class="text-green-600 border-green-600 border p-0.25 rounded-full cursor-pointer" @click="emits('decrease')"><RemoveIcon class="w-5 h-5 fill-current" /></div>
            <div class="mx-2">{{item.quality}}</div>
            <div class="text-white cursor-pointer bg-green-600 rounded-full p-0.25 border border-green-600" @click="emits('increase')"><AddIcon class="w-5 h-5 fill-current" /></div>
        </div>
    </div>
</template>