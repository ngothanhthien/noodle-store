<script setup>
import { computed, onMounted, ref } from 'vue';
import MealViewCard from '../../components/MealViewCard.vue';
import mealTypes from '../../mapping/type';
import { getAllMealAPI } from '../../api';
import { getUserToken } from '../../logic/auth';
import errorHandle from '../../logic/errorHandle'
import axios from 'axios';
import FilterContainer from '../../components/FilterContainer.vue';
import ButtonCreate from '../../components/ButtonCreate.vue';
import MealCreateModal from '../../components/MealCreateModal.vue';
import MealDetailModal from '../../components/MealDetailModal.vue';
const meals = ref([]);
const filterType = ref(null);
const mealSearchBar = ref('');
const token = getUserToken();
const mealCreateVisible = ref(false);
const mealDetail=ref(null);
onMounted(async () => {
    try {
        const response = await axios({
            method: 'GET',
            url: getAllMealAPI,
            headers: {
                'Authorization': 'Bearer ' + token
            },
        });
        meals.value = response.data.meals;
    } catch (error) {
        errorHandle(error.response.status, error);
    }
})
const filteredMeal = computed(() => {
    if (filterType.value == null) {
        if (mealSearchBar.value.length < 3) {
            return meals.value;
        }
        return meals.value.filter((meal) => meal.name.toLowerCase().includes(mealSearchBar.value.toLowerCase()));
    }
    if (mealSearchBar.value.length < 3) {
        return meals.value.filter((meal) => meal.type == filterType.value);
    }
    return meals.value.filter((meal) => meal.type == filterType.value).filter((meal) => meal.name.toLowerCase().includes(mealSearchBar.value.toLowerCase()))
})
function addTypeToFilter(type) {
    if (filterType.value == type) {
        filterType = '';
        return;
    }
    filterType.value = type;
}
function editMeal(meal) {
    mealDetail.value=meal;
}
function successCreateMeal({meal}) { 
    mealCreateVisible.value=false;
    meal.buy_amount=0;
    meals.value.unshift(meal);
}
function successEditMeal({meal}) {
    mealDetail.value=null;
    const foundIndex=meals.value.findIndex(obj=>obj.id==meal.id);
    meals.value[foundIndex] =meal;
}
</script>
<template>
    <div class="h-full flex flex-col overflow-y-scroll">
        <div class="text-2xl">Món ăn</div>
        <ButtonCreate @click="mealCreateVisible=true" class="my-2" />
        <div class="mx-auto w-1/2 my-4">
            <input v-model="mealSearchBar" type="text"
                class="w-full outline-none px-2 py-1 rounded outline-green-700 outline-offset-0"
                placeholder="Tên sản phẩm...">
        </div>
        <FilterContainer @changeFilter="addTypeToFilter" :currentFilter="filterType" :filterList="mealTypes">
        </FilterContainer>
        <div class="flex flex-wrap grow">
            <div class="w-1/5 px-2 py-2" :key="meal.id" v-for="meal in filteredMeal">
                <MealViewCard @click="editMeal(meal)" :meal="meal"></MealViewCard>
            </div>
        </div>
        <MealCreateModal @close="mealCreateVisible=false" @outside="mealCreateVisible=false"
            @successCb="successCreateMeal" v-if="mealCreateVisible"></MealCreateModal>
        <MealDetailModal @successCb="successEditMeal" :meal="mealDetail" @close="mealDetail=null" @outside="mealDetail=null" v-if="mealDetail"></MealDetailModal>
    </div>
</template>