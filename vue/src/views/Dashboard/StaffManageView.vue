<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import axios from 'axios'
import ButtonCreate from "../../components/ButtonCreate.vue";
import ruleMap from "../../mapping/rule";
import { addOrRemove, removeObjFromArrayById, arrayHasSubArray } from "../../logic/array";
import { getAllStaffAPI, staffAPI } from "../../api";
import Cookies from "js-cookie";
import ButtonSoftDelete from "../../components/ButtonSoftDelete.vue";
import ConfirmModal from "../../components/ConfirmModal.vue";
import StaffCreateModal from "../../components/StaffCreateModal.vue";
import InfoModal from "../../components/InfoModal.vue";
import LoadingElement from "../../components/LoadingElement.vue";
import errorHandle from "../../logic/errorHandle"
import StaffEditModal from "../../components/StaffEditModal.vue";
import LockResetIcon from "../../components/icons/LockResetIcon.vue";
import { getNewPasswordAPI } from "../../api"
const rulesFilter = ref([]);
const staffs = ref([]);
const search = ref('');
const isLoadingStaffs = ref(true);
const editStaff = ref(null);
const updatedStaffId = ref(null);
const newPasswordResponse=ref(null);
const newPasswordInfoVisible=ref(false);
const staffsFiltered = computed(() => {
  return staffs.value.filter((staff) =>
    staff.name.toLowerCase().includes(search.value.toLowerCase())
    && arrayHasSubArray(staff.rules.map(rule => rule.name), rulesFilter.value)
  );
});
const confirmDeleteVisible = ref(false);
const createModalVisible = ref(false);
const infoModalVisible = ref(false);
const staffOnDelete = reactive({
  name: null,
  id: null,
});
const userCreated = reactive({
  username: null,
  password: null,
})
const token = Cookies.get('User Token');
async function confirmDelete() {
  confirmDeleteVisible.value = false;
  const staffId = staffOnDelete.id;
  try {
    await axios({
      method: 'delete',
      url: staffAPI + '/' + staffId,
      headers: {
        'Authorization': 'Bearer ' + token
      }
    });
    removeObjFromArrayById(staffs.value, staffId);
  } catch (error) {
    errorHandle(error.response.status, error);
  }
}
function showConfirmDelete(staff) {
  staffOnDelete.name = staff.name;
  staffOnDelete.id = staff.id;
  confirmDeleteVisible.value = true;
}
async function loadStaff() {
  try {
    const response = await axios({
      method: 'get',
      url: getAllStaffAPI,
      headers: {
        'Authorization': 'Bearer ' + token,
      }
    });
    isLoadingStaffs.value = false;
    staffs.value = response.data.data;
  } catch (error) {
    errorHandle(error.response.status, error);
  }
}
onMounted(() => {
  loadStaff();
})
function rulesClass(index) {
  if (rulesFilter.value.includes(index)) {
    return ["text-white", "bg-green-700"];
  }
  return ["text-green-700"];
};
function toggleFilter(index) {
  addOrRemove(rulesFilter.value, index);
};
function successCreateCb({ username, password, user_info }) {
  createModalVisible.value = false;
  updatedStaffId.value = user_info.id;
  userCreated.username = username;
  userCreated.password = password;
  user_info.orders_today = 0;
  user_info.orders_this_month = 0;
  staffs.value.unshift(user_info);
  infoModalVisible.value = true;
}
function staffEditCb(form, id) {
  editStaff.value = null;
  updatedStaffId.value = id;
  const updatedStaffIndex = staffs.value.findIndex(staff => staff.id == id);
  staffs.value[updatedStaffIndex]['phone'] = form.phone;
  staffs.value[updatedStaffIndex]['name'] = form.name;
  staffs.value[updatedStaffIndex]['rules'] = form.rules.map(rule => ({ name: rule }));
}
function rulesToView(rules) {
  const output = [];
  for (let i = 0; i < rules.length; i++) {
    const rule = rules[i]['name'].split(':')[0];
    if (output.indexOf(rule) === -1) {
      output.push(rule);
    }
  }
  return output;
};
async function getNewPassword(id) {
  const response = await axios({
    url: getNewPasswordAPI(id),
    method: "PATCH",
    headers: {
      "Authorization": "Bearer " + token
    }
  })
  newPasswordInfoVisible.value=true;
  newPasswordResponse.value=response.data;
}
</script>

<template>
  <div class="flex flex-col h-full">
    <div>
      <div class="text-2xl mb-3">Tất cả nhân viên</div>
      <ButtonCreate @click="createModalVisible=true" />
      <div class="flex items-center my-2">
        <div>Lọc chức vụ:</div>
        <div :key="index" v-for="(rule, index) in ruleMap" :class="rulesClass(index)"
          class="ml-2 py-1 px-2 border border-green-700 cursor-pointer select-none" @click="toggleFilter(index)">
          {{ rule.name }}
        </div>
      </div>
      <div class="w-1/2 m-auto">
        <input v-model="search" type="text" name="search" placeholder="Tìm theo tên..."
          class="py-1 px-2 border focus:border focus:outline-none w-full" />
      </div>
    </div>
    <div class="flex-auto overflow-y-auto">
      <LoadingElement v-if="isLoadingStaffs" />
      <div v-else>
        <div v-if="staffsFiltered.length==0">Không có nhân viên.</div>
        <table v-else class="table-auto border-collapse w-full my-2 ">
          <tr>
            <th class="text-left" rowspan="2">Thông tin</th>
            <th colspan="2">Tổng đơn</th>
            <th rowspan="2">Quyền</th>
            <th rowspan="2">Tác vụ</th>
          </tr>
          <tr>
            <th>Trong ngày</th>
            <th>Trong tháng</th>
          </tr>
          <tr class="hover:bg-gray-200" :class="{'bg-yellow-300':staff.id==updatedStaffId}" :key="staff.id+'staff'"
            v-for="staff in staffsFiltered">
            <td @click="editStaff=staff" class="cursor-pointer">
              <div>{{staff.name!=null?staff.name:'Chưa có tên'}}</div>
              <div>{{staff.phone!=null?staff.phone:'Chưa có số'}}</div>
            </td>
            <td @click="editStaff=staff" class="cursor-pointer text-center">{{staff.orders_today}}</td>
            <td @click="editStaff=staff" class="cursor-pointer text-center">{{staff.orders_this_month}}</td>
            <td @click="editStaff=staff" class="cursor-pointer divide-x-2 divide-gray-400 text-center">
              <div :key="rule.name+' - '+staff.id" class="inline-block p-1" v-if="staff.rules.length!=0"
                v-for="rule in rulesToView(staff.rules)">
                {{ruleMap[rule]['name']}}
              </div>
              <div class="inline-block p-1" v-else>Cơ bản</div>
            </td>
            <td>
              <div class="flex items-center space-x-4 justify-center">
                <ButtonSoftDelete @click="showConfirmDelete({name: staff.name, id: staff.id})" class="w-7 h-7" />
                <div @click="getNewPassword(staff.id)"
                  class="cursor-pointer w-7 h-7 bg-gray-500 text-white rounded p-1 hover:bg-gray-600">
                  <LockResetIcon class="w-full h-full fill-white" />
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <ConfirmModal @clickOutside="confirmDeleteVisible=false" @cancel="confirmDeleteVisible=false" @confirm="confirmDelete"
    :visible="confirmDeleteVisible">
    <template v-slot:text>
      Bạn có muốn xóa {{staffOnDelete.name}}?
    </template>
  </ConfirmModal>
  <StaffCreateModal v-if="createModalVisible" @successCb="successCreateCb" @close="createModalVisible=false"
    @outside="createModalVisible=false" />
  <StaffEditModal @successCb="staffEditCb" @outside="editStaff=null" @close="editStaff=null" :staff="editStaff"
    v-if="editStaff!=null" />
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
  <InfoModal v-if="newPasswordInfoVisible" @close="newPasswordInfoVisible=false">
    <template v-slot:content>
      <div class="text-lg mb-4 select-none">
        Tạo mật khẩu mới thành công
      </div>
      <div class="max-w-fit m-auto">
        <div class="text-left">
          <span class="mr-2 select-none">Tài khoản:</span>
          <span>{{newPasswordResponse.username}}</span>
        </div>
        <div class="text-left mb-6">
          <span class="mr-2 select-none">Mật khẩu mới:</span>
          <span>{{newPasswordResponse.password}}</span>
        </div>
      </div>
    </template>
    <template v-slot:button>
      OK
    </template>
  </InfoModal>
</template>
<style lang="postcss" scoped>
th {
  @apply bg-gray-300
}

th,
td {
  @apply p-2 border border-gray-400
}
</style>