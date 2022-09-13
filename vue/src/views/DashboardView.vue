<script setup>
import GroupIcon from "../components/icons/GroupIcon.vue";
import ShoppingCartIcon from "../components/icons/ShoppingCartIcon.vue";
import Logout from "../components/Logout.vue";
import SidebarItem from "../components/SidebarItem.vue";
</script>

<template>
  <div class="h-screen w-full flex">
    <div class="w-72 h-full divide-y">
      <div class="py-2 px-2">Chào Ngô Thanh Thiên!</div>
      <div>
        <SidebarItem :icon="GroupIcon" title="Quản lý nhân viên" target="Staff Manage" />
        <SidebarItem :icon="ShoppingCartIcon" title="Đơn hàng" target="Order Create" />
      </div>
    </div>
    <div class="w-full bg-slate-100 py-2 px-4 relative">
      <router-view></router-view>
    </div>
    <Logout />
  </div>
</template>

<style lang="postcss">
.router-link-active{
  @apply bg-gray-200;
}
</style>
