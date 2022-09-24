<script setup>
import CheckBox from "./CheckBox.vue"
const props= defineProps(['filterList','currentFilter']);
const emits=defineEmits(['changeFilter']);
</script>
<template>
    <div class="flex items-center">
        <div class="px-2 flex">
            <CheckBox @check="emits('changeFilter',null)" :isChecked="currentFilter==null"
                class="rounded">Tất cả</CheckBox>
            <div :key="id" v-for="(filter,id) in filterList">
                <CheckBox @check="emits('changeFilter',id)" :isChecked="currentFilter==id" class="rounded">
                    {{filter.name}}</CheckBox>
            </div>
        </div>
    </div>
</template>