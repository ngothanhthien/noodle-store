<script setup>
import AddIcon from './icons/AddIcon.vue';
import RemoveIcon from './icons/RemoveIcon.vue';
import {numberToMoney} from '../logic/money'
import TypeMapping from '../mapping/type'
const props = defineProps(['meal','quality']);
const emits= defineEmits(['increase','decrease','imageClicked']);
</script>

<template>
    <div class="rounded-md overflow-hidden shadow-md w-full group">
        <div class="cursor-pointer" @click="emits('imageClicked')">
            <img class="w-full select-none" src="https://api.lorem.space/image/burger?w=200&h=200" alt="">
        </div>
        <div class="bg-white p-2">
            <div class="select-none">{{meal.name}}</div>
            <div class="text-xs text-gray-400">{{TypeMapping[meal.type]['name']}}</div>
            <div class="flex">
                <div class="select-none">
                    <div class="text-red-700">
                        {{numberToMoney(meal.price)}}
                    </div>
                    <div class="text-sm">
                        <span class="pr-1">Đã bán:</span>
                        <span>{{meal.buy_amount}}</span>
                    </div>
                </div>
                <div class="flex ml-auto justify-center items-center">
                    <div @click="emits('decrease')" v-if="quality>0" class="cursor-pointer bg-red-700 text-white"><RemoveIcon class="w-7 h-7 fill-current" /></div>
                    <div v-if="quality>0" class="mx-2 text-xl select-none">{{quality}}</div>
                    <div @click="emits('increase')" class="text-white group-hover:bg-green-600 transition bg-green-700 cursor-pointer"><AddIcon class="w-7 h-7 fill-current" /></div>
                </div>
            </div>
        </div>
    </div>
</template>