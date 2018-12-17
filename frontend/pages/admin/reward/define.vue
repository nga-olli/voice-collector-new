<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: 'Reward', link: '/admin/reward' },
        { name: 'Define', link: '' }
      ]">
      </breadcrumb>
    </el-col>
    <el-col :span="24">
      <define-form></define-form>
    </el-col>
  </el-row>  
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import DefineForm from '~/components/admin/reward/define-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    DefineForm
  }
})
export default class AdminRewardDefine extends Vue {
  head() {
    return {
      title: 'Define reward',
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Define reward'
        }
      ]
    };
  }

  loading: boolean = false;

  onBack() { return this.$router.go(-1); }
}

</script>

<style scoped>
.panel-body {
  margin-left: 0;
}

.filter-icon {
  cursor: pointer;
}
</style>
