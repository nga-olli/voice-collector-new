<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-text-width"></i></div>
      <breadcrumb :data="[
        { name: 'Job', link: '/admin/job' },
        { name: $t('default.list'), link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <nuxt-link to="/admin/job/add">
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
        <admin-script-items :jobs="jobs"></admin-script-items>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminScriptItems from '~/components/admin/job/items.vue';
import FilterBar from '~/components/admin/job/filter-bar.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminScriptItems,
    FilterBar
  }
})
export default class AdminJobPage extends Vue {
  @Action('jobs/get_all') listAction;
  @State(state => state.jobs.data) jobs;
  @State(state => state.jobs.totalItems) totalItems;
  @State(state => state.jobs.recordPerPage) recordPerPage;
  @State(state => state.jobs.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;

  head() {
    return {
      title: 'Jobs',
      meta: [
        { hid: 'description', name: 'description', content: 'Jobs' }
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
