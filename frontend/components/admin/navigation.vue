<lang>
---

en:
  dashboard: Dashboard
  user: User
  voice: Voice
  script: Script
  reward: Reward
  type: Type & Attributes
  category: Category
  job: Job
vi:
  dashboard: Tổng quan
  user: Thành viên
  voice: Thu âm
  script: Script
  reward: Phần thưởng
  type: Loại & thuộc tính
  category: Category
  job: Job
</lang>

<template>
  <el-menu mode="horizontal" unique-opened :default-active="$route.path" router>
    <el-menu-item index="/admin">
      {{ $t('dashboard') }}
    </el-menu-item>
    <el-submenu index="/admin/user">
      <template slot="title">{{ $t('user') }}</template>
      <el-menu-item index="/admin/user">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/user/add">
        <i class="el-icon-fa-plus"></i>
        {{ $t('default.add') }}
      </el-menu-item>
    </el-submenu>
    <el-submenu index="/admin/voice">
      <template slot="title">{{ $t('voice') }}</template>
      <el-menu-item index="/admin/voice">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
       <el-menu-item index="/admin/script/category">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('category') }}
      </el-menu-item>
      <el-menu-item index="/admin/script">
        <i class="el-icon-fa-text-width"></i>
        {{ $t('script') }}
      </el-menu-item>
    </el-submenu>
    <el-submenu index="/admin/reward">
      <template slot="title">{{ $t('reward') }}</template>
      <el-menu-item index="/admin/reward/category">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/reward/define">
        <i class="el-icon-fa-plus"></i>
        Type
      </el-menu-item>
    </el-submenu>
    <el-submenu index="/admin/job">
      <template slot="title">{{ $t('job') }}</template>
      <el-menu-item index="/admin/job">
        <i class="el-icon-fa-navicon"></i>
        {{ $t('default.list') }}
      </el-menu-item>
      <el-menu-item index="/admin/job/add">
        <i class="el-icon-fa-plus"></i>
        {{ $t('default.add') }}
      </el-menu-item>
    </el-submenu>
    <el-dropdown class="avatar-container" v-if="currentUser">
      <div class="avatar-wrapper">
        <span class="user-name">
          {{ currentUser.sub.fullname }}
        </span>
        <img v-if="currentUser.sub.avatar !== ''" :src="currentUser.sub.avatar" width="30" height="30" class="user-avatar">
        <img v-else src="/img/default_avatar.png" width="30" height="30" class="user-avatar">
        <i class="el-icon-caret-bottom"></i>
      </div>
      <el-dropdown-menu class="user-dropdown" slot="dropdown">
        <el-dropdown-item>
          <nuxt-link to="/admin/user/changepassword">
            {{ $t('default.changePassword') }}
          </nuxt-link>
        </el-dropdown-item>
        <el-dropdown-item divided>
          <nuxt-link to="/admin/user/logout">
            <span style="display:block;">{{ $t('default.logout') }}</span>
          </nuxt-link>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </el-menu>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator'
import { Getter } from 'vuex-class'
import { setLocale } from '~/helper/locale';

@Component
export default class Navigation extends Vue {
  @Getter('loggedUser') currentUser

  onChangeLocale(locale) {
    setLocale(locale);
    return window.location.reload();
  }
}
</script>

<style lang="scss">
.el-menu--horizontal {
  height: 45px;

  .el-menu-item {
    height: 45px;
    line-height: 45px;
  }

  .el-submenu {
    .el-menu {
      top: 50px;
    }
    .el-submenu__title {
      height: 45px;
      line-height: 45px;
    }
  }

  .avatar-container {
    height: 43px;
    display: inline-block;
    float: right;
    margin-right: 40px;
    .avatar-wrapper {
      cursor: pointer;
      margin-top: 7px;
      position: relative;
      float: left;
      clear: both;

      .user-name {
        float: left;
        padding-top: 7px;
        padding-right: 10px;
      }
      .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 15px;
      }
      .el-icon-caret-bottom {
        position: absolute;
        right: -20px;
        top: 10px;
        font-size: 12px;
      }
    }
  }

  .locale-container {
    cursor: pointer;
    float: right;
    margin-left: 15px;
    margin-right: 15px;
    margin-top: 14px;
    font-size: 14px;
    border-right: 1px solid #ecf0f1;
    padding-right: 15px;
  }
}
</style>
