<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-text-width"></i></div>
      <breadcrumb :data="[
        { name: 'Reward', link: '/admin/reward' },
        { name: 'Category', link: '/admin/reward/category' },
        { name: $t('default.list'), link: '' }
      ]">
      </breadcrumb>
      <div class="top-right-toolbar">
        <nuxt-link to="/admin/reward/category/add">
          <el-button type="text" icon="el-icon-plus">Category</el-button>
        </nuxt-link>
        <nuxt-link to="/admin/reward/define"> &nbsp;
          <el-button type="text" icon="el-icon-plus">Type</el-button>
        </nuxt-link> &nbsp;
        <el-button size="mini" type="text" icon="el-icon-plus" @click="onShowAddForm">
          Gift
        </el-button>
      </div>
    </el-col>
    <el-col :span="24">
      <el-col :md="6" style="padding: 16px">
        <el-tree :data="rewardcategories" :props="defaultProps" @node-click="handleNodeClick"></el-tree>
      </el-col>
      <el-col :md="18">
        <admin-reward-type-items :rewardtypes="rewardtypes"></admin-reward-type-items>
      </el-col>
    </el-col>
    <add-form :addFormState="addFormVisible" :onClose="onHideAddForm"></add-form>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminRewardTypeItems from '~/components/admin/reward/type/items.vue';
import AddForm from '~/components/admin/reward/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminRewardTypeItems,
    AddForm
  }
})
export default class AdminRewardCategoryPage extends Vue {
  @Action('rewardcategories/get_all_closure') listAction;
  @Action('rewardtypes/get_all') listGiftTypeAction;
  @State(state => state.rewardcategories.data) rewardcategories;
  @State(state => state.rewardtypes.data) rewardtypes;
  @State(state => state.rewardtypes.totalItems) totalItems;
  @State(state => state.rewardtypes.recordPerPage) recordPerPage;
  @State(state => state.rewardtypes.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  defaultProps: any = {
    children: 'children',
    label: 'name'
  };
  loading: boolean = false;
  addFormVisible: boolean = false;

  head() {
    return {
      title: 'Reward categories',
      meta: [
        { hid: 'description', name: 'description', content: 'Reward categories' }
      ]
    };
  }

  async handleNodeClick(data) {
    if (typeof data.children === 'undefined') {
      await this.listGiftTypeAction({ query: {
        rcid: data.id
      } })
        .then(() => {
          this.loading = false;
        })
        .catch(e => {
          this.loading = false;
        });
    }
  }

  created() { this.initData(); }

  async initData() {
    this.loading = true;

    await this.listAction({ query: this.$route.query })
      .then(() => {
        this.loading = false;
      })
      .catch(e => {
        this.loading = false;
      });
  }

  onShowAddForm() {
    this.addFormVisible = !this.addFormVisible;
  }

  onHideAddForm() { this.addFormVisible = false; }
}
</script>

<style lang="scss">

</style>