<script setup>
import { computed } from '@vue/reactivity';
import NavigateBeforeIcon from './icons/NavigateBeforeIcon.vue';
import NavigateNextIcon from './icons/NavigateNextIcon.vue';
const PAGE_RANGE=2;
const props= defineProps(['currentPage', 'totalPage']);
const emits= defineEmits(['pageClicked']);
const paginate=computed(()=>{
    const totalPage=props.totalPage;
    const currentPage=props.currentPage;
    const arr=[];
    if(totalPage<=5*PAGE_RANGE){
        for(let i=1;i<=totalPage;i++){
            arr.push(i);
        }
        return arr;
    }
    if(currentPage-1<=PAGE_RANGE){
        for(let i=1;i<=PAGE_RANGE*2+1;i++){
            arr.push(i);
        }
        arr.push('...');
        arr.push(totalPage);
        return arr;
    }
    if(totalPage-currentPage<=PAGE_RANGE){
        arr.push('...');
        for(let i=PAGE_RANGE*2;i>=0;i--){
            arr.push(totalPage-i);
        }
        return arr;
    }
    arr.push(1);
    if(currentPage-PAGE_RANGE>2){
        arr.push('...');
    }else{
        arr.push(2);
    }
    for(let i=currentPage-2;i<=currentPage+2;i++){
        arr.push(i);
    }
    if(totalPage-currentPage-PAGE_RANGE>2){
        arr.push('...');
    }else{
        arr.push(totalPage-1);
    }
    arr.push(totalPage);
    return arr;
})
</script>

<template>
    <div class="flex justify-center items-center">
        <div @click="emits('pageClicked',currentPage==1?1:currentPage-1)" class="cursor-pointer hover:text-green-400 px-2 py-2"><NavigateBeforeIcon class="w-5 h-5 fill-current" /></div>
        <div @click="emits('pageClicked',page)" :class="{'text-green-600 text-xl':currentPage==page,'cursor-pointer hover:text-green-400':page!='...'}" class="px-4 py-2  select-none" v-for="page in paginate">
            {{page}}
        </div>
        <div @click="emits('pageClicked',currentPage==totalPage?totalPage:currentPage+1)" class="cursor-pointer hover:text-green-400 px-2 py-2"><NavigateNextIcon class="w-5 h-5 fill-current" /></div>
    </div>
</template>