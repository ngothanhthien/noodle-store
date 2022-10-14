<script setup>
import { ref } from "vue";
import GroupIcon from "../components/icons/GroupIcon.vue";
import RamenDinningIcon from "../components/icons/RamenDinningIcon.vue";
import ShoppingCartIcon from "../components/icons/ShoppingCartIcon.vue";
import Logout from "../components/Logout.vue";
import SidebarItem from "../components/SidebarItem.vue";
import SideBarItemExpand from "../components/SideBarItemExpand.vue";
import SidebarSubItem from "../components/SidebarSubItem.vue";
import SettingsIcon from "../components/icons/SettingsIcon.vue";
import ContractPhone from "../components/icons/ContractPhone.vue";
import {getUserInfo} from "../logic/auth"
const orderExpand=ref(false);
const settingExpand=ref(false);
const name=ref('Admin');
const info=getUserInfo();
const rules=ref(['admin']);
console.log(info);
if(info){
  name.value=info.name;
  rules.value=info.rules.map(rule=>rule.name);
}
const ruleStaff=()=>rules.value.includes('admin')||rules.value.includes("staff-manage");
const ruleMeal=()=>rules.value.includes('admin')||rules.value.includes("staff-manage");
const ruleCustomer=()=>rules.value.includes('admin')||rules.value.includes("customer-manage");
</script>

<template>
  <div class="h-screen w-full flex">
    <div class="w-72 h-full divide-y">
      <div class="py-2 px-2">Chào {{name}}!</div>
      <div>
        <SidebarItem v-if="ruleStaff()" :icon="GroupIcon" title="Quản lý nhân viên" target="Staff Manage" />
        <SideBarItemExpand @toggle="orderExpand=!orderExpand" :isExpanded="orderExpand" :icon="ShoppingCartIcon" title="Đơn hàng">
          <SidebarSubItem title="Tạo đơn" target="Order Create"/>
          <SidebarSubItem title="Xử lý đơn" target="Order Manage" />
        </SideBarItemExpand>
        <SidebarItem v-if="ruleMeal()" :icon="RamenDinningIcon" title="Quản lý món ăn" target="Meal Manage" />
        <SidebarItem v-if="ruleCustomer()" :icon="ContractPhone" title="Khách hàng" target="Customer View" />
        <SideBarItemExpand @toggle="settingExpand=!settingExpand" :isExpanded="settingExpand" :icon="SettingsIcon" title="Cài đặt" target="Profile">
          <SidebarSubItem title="Đổi mật khẩu" target="Change Password"/>
          <SidebarSubItem title="Cập nhật thông tin" target="Change Info" />
        </SideBarItemExpand>
      </div>
    </div>
    <div class="w-full h-full bg-slate-100 py-2 px-4 relative">
      <router-view></router-view>
    </div>
    <Logout />
  </div>
</template>

<style lang="postcss" scoped>
.router-link-active{
  @apply bg-gray-200;
}
</style>
