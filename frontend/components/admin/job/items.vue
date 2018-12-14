<template>
  <section>
    <el-table :data="jobs" style="width: 100%" row-key="id"
      @selection-change="onSelectionChange">
      <el-table-column type="selection"></el-table-column>
      <el-table-column label="#" prop="id" width="50">
      </el-table-column>
      <el-table-column label="Name" prop="name">
      </el-table-column>
      <el-table-column label="Type">
        <template slot-scope="scope">
          <small>{{ scope.row.type.label }}</small>
        </template>
      </el-table-column>
      <el-table-column label="Reward" prop="maxcoinreward">
      </el-table-column>
      <el-table-column label="Date created">
        <template slot-scope="scope">
          <small>{{ scope.row.datecreated.readable }}</small>
        </template>
      </el-table-column>
      <el-table-column label="Date expired">
        <template slot-scope="scope">
          <small>{{ scope.row.dateexpired.readable }}</small>
        </template>
      </el-table-column>
      <el-table-column label="Status">
         <template slot-scope="scope">
          <el-tag type="primary">{{ scope.row.status.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="130">
        <template slot-scope="scope">
          <el-button-group class="operation">
            <el-button icon="el-icon-edit" size="mini" @click="onShowEditForm(scope.row.id)"></el-button>
            <delete-button :id="scope.row.id" store="jobs"></delete-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <div style="margin-top: 20px">
      <el-select v-model="bulkName" :placeholder="$t('default.selectAction')" size="small">
        <el-option v-for="item in bulkList" :key="item.value" :label="item.label" :value="item.value" size="small">
        </el-option>
      </el-select>
      <el-button style="margin-left: 10px" type="primary" size="small" @click="onBulkSubmit">{{ $t('default.submit') }}</el-button>
    </div>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
    <edit-form :editFormState="visible" :itemId="itemId" :onClose="onHideEditForm"></edit-form>
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from 'vuex-class';
import DeleteButton from "~/components/admin/delete-button.vue";
// import EditForm from '~/components/admin/script/edit-form.vue';

@Component({
  components: {
    DeleteButton,
    // EditForm
  }
})
export default class AdminJobItems extends Vue {
  @Prop() jobs: any[];
  @Action('jobs/bulk') bulkAction;
  @Action('jobs/get_all') listAction;

  visible: boolean = false;
  itemId: number = 0;
  bulkSelected: object[] = [];
  bulkName: string = '';

  get bulkList() {
    return [
      { value: 'delete', label: this.$t('label.delete') },
      { value: 'enable', label: this.$t('label.enable') },
      { value: 'disable', label: this.$t('label.disable') }
    ];
  }

  onShowEditForm(id) {
    this.visible = !this.visible;
    this.itemId = id;
  }

  onHideEditForm() { this.visible = false; }

  onSelectionChange(item) { this.bulkSelected = item; }

  onBulkSubmit() {
    if (this.bulkSelected.length === 0) {
      this.$message({
        showClose: true,
        message: this.$t('default.msg.noItemSelected').toString(),
        type: 'warning',
        duration: 2 * 1000
      })
    } else if (this.bulkName === '') {
      this.$message({
        showClose: true,
        message: this.$t('default.msg.noActionChosen').toString(),
        type: 'warning',
        duration: 2 * 1000
      });
    } else {
      this.$confirm(this.$t('msg.confirmBulk').toString(), this.$t('default.warning').toString(), {
        confirmButtonText: this.$t('default.msg.confirm').toString(),
        cancelButtonText: this.$t('default.msg.cancel').toString(),
        type: 'warning',
        dangerouslyUseHTMLString: true
      })
      .then(async () => {
        await this.bulkAction({
            formData: {
              itemSelected: this.bulkSelected,
              actionSelected: this.bulkName
            }
          })
          .then(async () => {
            let queryParams = Object.assign({}, this.$route.query);

            await this.listAction({ query: queryParams })
              .then(() => {
                this.$message({
                  showClose: true,
                  message: `${this.bulkName.charAt(0).toUpperCase() + this.bulkName.slice(1)} ${this.$t('default.msg.deleteSuccess')}`,
                  type: 'success',
                  duration: 2 * 1000
                });
              })
          });
      });
    }
  }
}
</script>

<style lang="scss">

</style>
