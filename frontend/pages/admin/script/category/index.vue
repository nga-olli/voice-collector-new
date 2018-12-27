<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-goods"></i></div>
      <breadcrumb :data="[
        { name: 'Script categories', link: '/admin/script/category' },
        { name: $t('default.list'), link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <nuxt-link to="/admin/script/category/add">
          <el-button type="text" icon="el-icon-plus">{{ $t('default.add') }}</el-button>
        </nuxt-link>
        <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination>
      </div>
    </el-col>
    <el-col :span="24">
      <div class="filter-container">
        <filter-bar></filter-bar>
      </div>
      <div class="panel-body">
        <admin-script-category-items :scriptcategories="scriptcategories"></admin-script-category-items>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminScriptCategoryItems from '~/components/admin/script/category/items.vue';
import FilterBar from '~/components/admin/script/category/filter-bar.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminScriptCategoryItems,
    FilterBar
  }
})
export default class AdminScriptCategoryPage extends Vue {
  @Action('scriptcategories/get_all') listAction;
  @State(state => state.scriptcategories.data) scriptcategories;
  @State(state => state.scriptcategories.totalItems) totalItems;
  @State(state => state.scriptcategories.recordPerPage) recordPerPage;
  @State(state => state.scriptcategories.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;

  head() {
    return {
      title: 'Script categories',
      meta: [
        { hid: 'description', name: 'description', content: 'Script categories' }
      ]
    };
  }

  created() { this.initData(); }

  async initData() {
    this.loading = true;

    return await this.listAction({ query: this.$route.query })
      .then(() => {
        this.loading = false;
      })
      .catch(e => {
        this.loading = false;
      });
  }
}
</script>
