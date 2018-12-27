<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: 'Script categories', link: '/admin/script/category' },
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
import AddForm from '~/components/admin/script/category/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    AddForm
  }
})
export default class AdminScriptCategoryAdd extends Vue {
  head() {
    return {
      title: 'Script categories',
      meta: [
        {
          hid: 'description',
          name: 'description',
          content: 'Script categories'
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
