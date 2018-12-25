<template>
  <section>
    <el-table :data="gifts"  style="width: 100%" row-key="id"
      @selection-change="onSelectionChange">
      <el-table-column type="selection"></el-table-column>
      <el-table-column label="#" prop="id" width="70"></el-table-column>
      <el-table-column :label="Info" width="450">
        <template slot-scope="scope">
          <el-row v-if="scope.row.stocks.data.length > 0" v-for="(item, index) in scope.row.stocks.data" :key="index">
            <el-col :md="24" :xs="24">
              <el-row :gutter="10">
                <el-col :md="8" class="attr_name">{{ item.attribute.data.name }}</el-col>
                <el-col :md="16">
                  <span class="value">
                    {{ item.value }}
                  </span>
                  <span class="unit">
                    <code>{{ item.attribute.data.unit }}</code>
                  </span>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
        </template>
      </el-table-column>
      <el-table-column label="Date created">
        <template slot-scope="scope">
          {{ scope.row.datecreated.readable }}
        </template>
      </el-table-column>
      <el-table-column label="Status"
        :show-overflow-tooltip="true">
        <template slot-scope="scope">
          <el-tag :type="scope.row.status.style" size="small">{{ scope.row.status.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="Claim ID">
      </el-table-column>
      <el-table-column label="Claim date">
      </el-table-column>
      <el-table-column label="Delivery date">
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
    <!-- <edit-form :editFormState="visible" :itemId="itemId" :onClose="onHideEditForm"></edit-form> -->
    <!-- <clone-form :cloneFormState="visibleClone" :itemId="itemId" :onClose="onHideCloneForm"></clone-form> -->
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { Action } from "vuex-class";
import DeleteButton from "~/components/admin/delete-button.vue";
// import EditForm from "~/components/admin/reward/edit-form.vue";
// import CloneForm from "~/components/admin/reward/clone-form.vue";

@Component({
  components: {
    DeleteButton,
    // EditForm,
    // CloneForm
  }
})
export default class AdminGiftItems extends Vue {
  @Prop() gifts: any[];
  @Action("rewards/bulk") bulkAction;
  @Action("rewards/get_all") listAction;

  visible: boolean = false;
  visibleClone: boolean = false;
  itemId: number = 0;
  bulkSelected: object[] = [];
  bulkName: string = "";

  get bulkList() {
    return [{ value: "delete", label: this.$t("label.delete") }];
  }

  onShowCloneForm(id) {
    this.visibleClone = !this.visibleClone;
    this.itemId = id;
  }

  onHideCloneForm() {
    this.visibleClone = false;
  }

  onShowEditForm(id) {
    this.visible = !this.visible;
    this.itemId = id;
  }

  onHideEditForm() {
    this.visible = false;
  }

  onSelectionChange(item) {
    this.bulkSelected = item;
  }

  onBulkSubmit() {
    if (this.bulkSelected.length === 0) {
      this.$message({
        showClose: true,
        message: this.$t("default.msg.noItemSelected").toString(),
        type: "warning",
        duration: 2 * 1000
      });
    } else if (this.bulkName === "") {
      this.$message({
        showClose: true,
        message: this.$t("default.msg.noActionChosen").toString(),
        type: "warning",
        duration: 2 * 1000
      });
    } else {
      this.$confirm(
        this.$t("msg.confirmBulk").toString(),
        this.$t("default.warning").toString(),
        {
          confirmButtonText: this.$t("default.msg.confirm").toString(),
          cancelButtonText: this.$t("default.msg.cancel").toString(),
          type: "warning",
          dangerouslyUseHTMLString: true
        }
      ).then(async () => {
        await this.bulkAction({
          formData: {
            itemSelected: this.bulkSelected,
            actionSelected: this.bulkName
          }
        }).then(async () => {
          let queryParams = Object.assign({}, this.$route.query);

          await this.listAction({
              query: queryParams,
              gtid: this.$route.params.id
            }).then(() => {
            this.$message({
              showClose: true,
              message: `${this.bulkName.charAt(0).toUpperCase() +
                this.bulkName.slice(1)} ${this.$t(
                "default.msg.deleteSuccess"
              )}`,
              type: "success",
              duration: 2 * 1000
            });
          });
        });
      });
    }
  }
}
</script>

<style lang="scss">
.cover {
  margin-right: 10px;
  float: left;
  display: inline-block;
}
.name {
  line-height: 30px;
  font-size: 1em;
}
.attr_name {
  font-size: 0.85em;
  font-weight: lighter;
}
.value {
  font-size: 0.9em;
}
.unit {
  font-size: 0.85em;
  font-weight: lighter;
  color: #ea8787;
}
.el-table .cell {
  line-height: 19px;
}
.el-badge__content {
  font-size: 0.8em;
}
.date_used {
  background-color: whitesmoke;
  display: block;
}
</style>
