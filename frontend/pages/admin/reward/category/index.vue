<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon"><i class="el-icon-fa-gift"></i></div>
      <breadcrumb :data="[
        { name: 'Reward', link: '/admin/reward/category' },
        { name: $t('default.list'), link: '' }
      ]">
      </breadcrumb>
      <div class="top-right-toolbar">
        <nuxt-link to="/admin/reward/define"> &nbsp;
          <el-button type="text" icon="el-icon-plus" size="mini">
            Add type
          </el-button>
        </nuxt-link> &nbsp;
        <el-button size="mini" type="success" icon="el-icon-plus" @click="onShowAddForm">
          Add gift
        </el-button>
      </div>
    </el-col>
    <el-col :span="24">
      <el-col :md="6" style="padding: 16px">
        <nuxt-link to="/admin/reward/category/add" style="float: right;display: inline-block;">
          <el-button type="primary" icon="el-icon-plus" size="mini">
            Add category
          </el-button>
        </nuxt-link>
        <el-tree
          style="margin-top: 40px;"
          accordion
          highlight-current
          node-key="id"
          :data="rewardcategories"
          :props="defaultProps"
          @node-click="handleNodeClick">
          <span class="custom-tree-node" slot-scope="{ node, data }">
            <span>{{ node.label }}</span>
            <span>
              <el-button
                style="float: left;margin-right: 5px;"
                type="text"
                size="mini"
                @click="() => edit(data)">
                Edit
              </el-button>
              <delete-button
                style="float: left;margin-right: 5px;"
                :id="data.id"
                store="rewardcategories">
              </delete-button>
            </span>
          </span>
        </el-tree>
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
import DeleteButton from "~/components/admin/delete-button.vue";

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminRewardTypeItems,
    AddForm,
    DeleteButton
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

  edit(data) {
   console.log(data); 
   return this.$router.push({
     path: `/admin/reward/category/edit/${data.id}`
   })
  }

  remove(node, data) {
    
  }
}
</script>

<style lang="scss">
  .custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    padding-right: 8px;
  }
  .el-tree-node {
    margin-bottom: 10px;
  }
</style>