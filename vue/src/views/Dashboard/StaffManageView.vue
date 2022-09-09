<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import axios from 'axios'
import ButtonCreate from "../../components/ButtonCreate.vue";
import ruleMap from "../../mapping/rule";
import { addOrRemove,removeObjFromArrayById,arrayHasSubArray } from "../../logic/array";
import { getAllStaffAPI, staffAPI } from "../../api";
import { backToLogin } from "../../logic/auth";
import Cookies from "js-cookie";
import ButtonSoftDelete from "../../components/ButtonSoftDelete.vue";
import ConfirmModal from "../../components/ConfirmModal.vue";
import status from "../../mapping/status";
import StaffCreateModal from "../../components/StaffCreateModal.vue";
import InfoModal from "../../components/InfoModal.vue";
const rulesFilter = ref([]);
const staffs = ref([]);
const search=ref('');
const staffsFiltered=computed(()=>{
  return staffs.value.filter((staff)=>
  staff.name.toLowerCase().includes(search.value.toLowerCase())
  &&arrayHasSubArray(staff.rules.map(rule=>rule.name),rulesFilter.value)
  );
});
const confirmDeleteVisible = ref(false);
const createModalVisible = ref(false);
const infoModalVisible = ref(false);
const staffOnDelete = reactive({
  name: null,
  id: null,
});
const userCreated=reactive({
  username:null,
  password:null,
})
const token = Cookies.get('User Token');
async function confirmDelete(){
  confirmDeleteVisible.value = false;
  const staffId=staffOnDelete.id;
  try {
    await axios({
      method: 'delete',
      url: staffAPI +'/'+ staffId,
      headers: {
        'Authorization': 'Bearer ' + token
      }
    });
    removeObjFromArrayById(staffs.value,staffId);
  } catch (error) {
    if(error.response.status==status['unauthorized']){
      backToLogin();
    }
  }
}
function showConfirmDelete(staff){
  staffOnDelete.name = staff.name;
  staffOnDelete.id = staff.id;
  confirmDeleteVisible.value = true;
}
async function loadStaff(){
  try{
    const response = await axios({
    method: 'get',
    url: getAllStaffAPI,
    headers: {
      'Authorization': 'Bearer ' + token,
    }
  });
  staffs.value = response.data.data;
  }catch(error){
    if(error.response.status==status['unauthorized']){
      backToLogin();
    }
  }
}
onMounted(() => {
  loadStaff();
})
function rulesClass(index){
  if (rulesFilter.value.includes(index)) {
    return ["text-white", "bg-green-700"];
  }
  return ["text-green-700"];
};
function toggleFilter(index){
  addOrRemove(rulesFilter.value, index);
};
function successCreateCb({username,password,user_info}){
  createModalVisible.value=false;
  userCreated.username=username;
  userCreated.password=password;
  user_info.orders_today=0;
  user_info.orders_this_month=0;
  staffs.value.unshift(user_info);
  infoModalVisible.value=true;
}
</script>

<template>
  <div class="text-2xl mb-3">Tất cả nhân viên</div>
  <ButtonCreate @click="createModalVisible=true" />
  <div>
    <div class="flex items-center my-2">
      <div>Lọc chức vụ:</div>
      <div v-for="(rule, index) in ruleMap" :class="rulesClass(index)"
        class="ml-2 py-1 px-2 border border-green-700 cursor-pointer select-none" @click="toggleFilter(index)">
        {{ rule }}
      </div>
    </div>
    <div>
      <div class="w-1/2 m-auto">
        <input v-model="search" type="text" name="search" placeholder="Tìm theo tên..."
          class="py-1 px-2 border focus:border focus:outline-none w-full" />
      </div>
      <table class="table-auto border-collapse w-full my-2">
        <tr>
          <th class="text-left" rowspan="2">Thông tin</th>
          <th colspan="2">Tổng đơn</th>
          <th rowspan="2">Quyền</th>
          <th rowspan="2"></th>
        </tr>
        <tr>
          <th>Trong ngày</th>
          <th>Trong tháng</th>
        </tr>
        <tr v-for="staff in staffsFiltered">
          <td>
            <div>{{staff.name!=null?staff.name:'Chưa có tên'}}</div>
            <div>{{staff.phone!=null?staff.phone:'Chưa có số'}}</div>
          </td>
          <td class="text-center">{{staff.orders_today}}</td>
          <td class="text-center">{{staff.orders_this_month}}</td>
          <td class="divide-x-2 divide-gray-400 text-center">
            <div class="inline-block p-1" v-if="staff.rules.length!=0" v-for="rule in staff.rules">
              {{ruleMap[rule.name]}}</div>
            <div class="inline-block p-1" v-else>Cơ bản</div>
          </td>
          <td>
            <ButtonSoftDelete @click="showConfirmDelete({name: staff.name, id: staff.id})" class="w-7 h-7 m-auto" />
          </td>
        </tr>
      </table>
    </div>
    <ConfirmModal @clickOutside="confirmDeleteVisible=false" @cancel="confirmDeleteVisible=false"
      @confirm="confirmDelete" :visible="confirmDeleteVisible">
      <template v-slot:text>
        Bạn có muốn xóa {{staffOnDelete.name}}?
      </template>
    </ConfirmModal>
    <StaffCreateModal v-if="createModalVisible" @successCb="successCreateCb" @close="createModalVisible=false" @outside="createModalVisible=false"></StaffCreateModal>
    <InfoModal v-if="infoModalVisible" @close="infoModalVisible=false">
        <template v-slot:content>
            <div class="text-lg mb-4 select-none">
                Tạo nhân viên thành công!
            </div>
            <div class="max-w-fit m-auto">
                <div class="text-left">
                    <span class="mr-2 select-none">Tài khoản:</span>
                    <span>{{userCreated.username}}</span>
                </div>
                <div class="text-left mb-6">
                    <span class="mr-2 select-none">Mật khẩu:</span>
                    <span>{{userCreated.password}}</span>
                </div>
            </div>
        </template>
        <template v-slot:button>
            OK
        </template>
    </InfoModal>
  </div>
</template>
<style lang="postcss" scoped>
th,
td {
  @apply p-2 border border-gray-300
}
</style>