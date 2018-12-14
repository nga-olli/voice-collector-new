<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: 'Job', link: '/admin/job' },
        { name: $t('default.add'), link: '' }
      ]">
      </breadcrumb>
    </el-col>
    <el-col :span="24">
      <add-form></add-form>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import AddForm from '~/components/admin/job/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    AddForm
  }
})
export default class AdminJobAdd extends Vue {
  head() {
    return {
      title: 'Add',
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Add'
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
