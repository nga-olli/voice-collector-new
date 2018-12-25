<template>
  <section>
    <el-table :data="rewardtypes"
      style="width: 100%"
      row-key="id">
      <el-table-column label="#" prop="id" width="50">
      </el-table-column>
      <el-table-column label="Type name" prop="name">
        <template slot-scope="scope">
          <div class="cover">
            <img v-if="scope.row.cover !== ''" :src="scope.row.cover" width="70" height="70">
          </div>
          <span class="fullname">{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Quantity" prop="total">
        <template slot-scope="scope">
          <el-badge :value="scope.row.total > 0 ? scope.row.total : 'Out of stock'"></el-badge>
        </template>
      </el-table-column>
      <el-table-column label="Status">
         <template slot-scope="scope">
          <el-tag :type="scope.row.status.style" size="mini">{{ scope.row.status.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="200">
        <template slot-scope="scope">
          <el-button-group class="operation">
            <nuxt-link :to="`/admin/reward/type/${scope.row.id}`" style="display: inline-block">
              <el-button icon="el-icon-menu" size="mini">Show items</el-button>
            </nuxt-link>
            <el-button icon="el-icon-edit" size="mini">Edit type</el-button>
            <!-- <delete-button :id="scope.row.id" store="jobs"></delete-button> -->
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
    <!-- <edit-form :editFormState="visible" :itemId="itemId" :onClose="onHideEditForm"></edit-form> -->
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from 'vuex-class';
// import DeleteButton from "~/components/admin/delete-button.vue";
// import EditForm from '~/components/admin/job/edit-form.vue';

@Component({
  components: {
    // DeleteButton,
    // EditForm
  }
})
export default class AdminRewardTypeItems extends Vue {
  @Prop() rewardtypes: any[];

  visible: boolean = false;
  itemId: number = 0;

  onShowEditForm(id) {
    this.visible = !this.visible;
    this.itemId = id;
  }

  onHideEditForm() { this.visible = false; }
}
</script>

<style lang="scss">
  .cover {
    margin-right: 10px;
    float: left;
    display: inline-block;
  }
</style>

