<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import MealViewCard from '../../components/MealViewCard.vue';
import mealTypes from '../../mapping/type';
import { getAllMealAPI } from '../../api/mealAPI';
import errorHandle from '../../logic/errorHandle'
import FilterContainer from '../../components/FilterContainer.vue';
import ButtonCreate from '../../components/ButtonCreate.vue';
import MealCreateModal from '../../components/MealCreateModal.vue';
import MealDetailModal from '../../components/MealDetailModal.vue';
import LoadingElement from '../../components/LoadingElement.vue';
import { removeObjFromArrayById } from '../../logic/array'
import InfoModal from '../../components/InfoModal.vue';
const meals = ref([]);
const filterType = ref(null);
const mealSearchBar = ref('');
const mealCreateVisible = ref(false);
const mealDetail = ref(null);
const isLoadingMeals = ref(true);
const updatedMealID = ref(null);
const infoModal=reactive({
    visible:false,
    text:'',
})
onMounted(async () => {
    try {
        const response = await getAllMealAPI();
        isLoadingMeals.value = false;
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
    mealDetail.value = meal;
}
function successCreateMeal(meal) {
    mealCreateVisible.value = false;
    updatedMealID.value = meal.id;
    meal.buy_amount = 0;
    meals.value.unshift(meal);
    showInfoModal('Tạo món thành công');
}
function successEditMeal(meal) {
    mealDetail.value = null;
    updatedMealID.value = meal.id;
    const foundIndex = meals.value.findIndex(obj => obj.id == meal.id);
    meals.value[foundIndex] = meal;
    showInfoModal('Sửa món thành công');
}
function successDeleteMeal(id) {
    mealDetail.value = null;
    removeObjFromArrayById(meals.value, id);
    showInfoModal('Xóa món thành công');
}
function showInfoModal(text) {
    infoModal.text=text;
    infoModal.visible = true;
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
        <FilterContainer @changeFilter="addTypeToFilter" :currentFilter="filterType" :filterList="mealTypes"/>
        <LoadingElement v-if="isLoadingMeals" class="mt-2 ml-1" />
        <div class="flex flex-wrap grow">
            <div class="w-1/5 px-2 py-2" :key="meal.id" v-for="meal in filteredMeal">
                <MealViewCard :class="{'border-yellow-500 border-2':meal.id==updatedMealID}" @click="editMeal(meal)"
                    :meal="meal"/>
            </div>
        </div>
        <MealCreateModal @close="mealCreateVisible=false" @outside="mealCreateVisible=false"
            @successCb="successCreateMeal" v-if="mealCreateVisible" />
        <MealDetailModal v-if="mealDetail" @successDelete="successDeleteMeal" @successCb="successEditMeal" :meal="mealDetail"
            @close="mealDetail=null" @outside="mealDetail=null"/>
        <InfoModal v-if="infoModal.visible" @close="infoModal.visible=false">
            <template #content>
                {{infoModal.text}}
            </template>
            <template #button>
                OK
            </template>
        </InfoModal>
    </div>
</template>