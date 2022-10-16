<script setup>
const props = defineProps(['rule', 'isChecked', 'formSubRule']);
const emits = defineEmits(['check', 'subRule-check']);
</script>

<template>
    <div>
        <div @click="emits('check')"
            :class="{'bg-green-700 text-white border-green-700':isChecked,'border-gray-700':!isChecked}"
            class="px-2 py-1 border select-none inline-block cursor-pointer">
            {{rule.name}}
        </div>
        <div v-if="isChecked&&formSubRule!=null" class="bg-gray-300">
            <div >
                <label :key="key" v-for="(subRule,key) in rule.subRule" class="flex items-center cursor-pointer select-none space-x-1 px-2 py-1 hover:bg-gray-400">
                    <input  @change="emits('subRule-check',key)" type="checkbox" :checked="formSubRule.includes(key)" class="accent-green-600 mt-0.5">
                    <div>{{subRule}}</div>
                </label>
            </div>
        </div>
    </div>
</template>