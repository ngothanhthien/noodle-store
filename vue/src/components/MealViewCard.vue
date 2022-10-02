<script setup>
import { numberToMoney } from '../logic/money'
import tagMapping from '../mapping/type'
const props = defineProps(['meal','sold','tag']);
</script>
    
<template>
    <div class="rounded-md cursor-pointer overflow-hidden shadow-md hover:shadow-xl transition duration-500 w-full group">
        <div class="overflow-hidden">
            <img class="w-full duration-500 group-hover:scale-105 select-none transition" src="https://api.lorem.space/image/burger?w=200&h=200" alt="">
        </div>
        <div class="bg-white p-1">
            <div class="select-none">{{meal.name}}</div>
            <div class="select-none text-sm text-gray-400">
               {{tagMapping[meal.type].name}}
            </div>
            <div class="flex">
                <div class="select-none text-red-600 font-medium">
                    {{numberToMoney(meal.price)}}
                </div>
                <div class="ml-auto font-medium text-red-700">Đã bán: {{meal.buy_amount}}</div>
            </div>
        </div>
    </div>
</template>