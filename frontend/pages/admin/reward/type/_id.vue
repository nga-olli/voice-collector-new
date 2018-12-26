
<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: 'Reward', link: '/admin/reward/category' },
        { name: `Items of type: ${myType.data.name}`, link: '' }
      ]" :totalItems="totalItems">
      </breadcrumb>
      <div class="top-right-toolbar">
        <el-button size="mini" type="text" icon="el-icon-plus" @click="onShowAddForm">
          Add
        </el-button>
        <pagination :totalItems="totalItems" :currentPage="query.page" :recordPerPage="recordPerPage"></pagination>
      </div>
    </el-col>
    <el-col :span="24">
      <!-- <div class="filter-container">
        <filter-bar></filter-bar>
      </div> -->
      <div class="panel-body">
        <admin-gift-items :gifts="rewards"></admin-gift-items>
      </div>
    </el-col>
    <add-form :addFormState="addFormVisible" :onClose="onHideAddForm"></add-form>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';
import Pagination from '~/components/admin/pagination.vue';
import AdminGiftItems from '~/components/admin/reward/items.vue';
import AddForm from '~/components/admin/reward/add-form.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb,
    Pagination,
    AdminGiftItems,
    AddForm
  }
})
export default class AdminRewardTypePage extends Vue {
  @Action('rewards/get_all') listAction;
  @Action('rewardtypes/get') getTypeAction;
  @State(state => state.rewards.data) rewards;
  @State(state => state.rewards.totalItems) totalItems;
  @State(state => state.rewards.recordPerPage) recordPerPage;
  @State(state => state.rewards.query) query;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;
  addFormVisible: boolean = false;
  myType: any = {
    data: {
      name: ''
    }
  };

  head() {
    return {
      title: 'Rewards',
      meta: [
        { hid: 'description', name: 'description', content: 'Rewards' }
      ]
    };
  }

  created() {
    this.initData();
  }

  async initData() {
    try {
      this.loading = true;
      await this.listAction({
        query: this.$route.query,
        gtid: this.$route.params.id
      });
      const myType = await this.getTypeAction({ id: this.$route.params.id });
      this.loading = false;

      document.title = `Items of type: ${myType.data.name}`;
      this.myType = myType;
    } catch (error) {
      this.loading = false;
    }
  }

  onBack() { return this.$router.go(-1); }

  onShowAddForm() {
    this.addFormVisible = !this.addFormVisible;
  }

  onHideAddForm() { this.addFormVisible = false; }
}
</script>
