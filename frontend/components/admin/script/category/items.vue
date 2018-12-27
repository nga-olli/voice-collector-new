<template>
  <section>
    <el-table :data="scriptcategories" style="width: 100%" row-key="id">
      <el-table-column label="#" prop="id" width="70"></el-table-column>
      <el-table-column label="Name" prop="name" width="250" :show-overflow-tooltip="true">
      </el-table-column>
      <el-table-column label="Display order" prop="displayorder" width="100">
      </el-table-column>
      <el-table-column label="Status" :show-overflow-tooltip="true" class="el-status">
        <template slot-scope="scope">
          <el-tag :type="scope.row.status.style" size="mini">{{ scope.row.status.label }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="Date created" width="130">
        <template slot-scope="scope">
          <small>{{ scope.row.datecreated.readable }}</small>
        </template>
      </el-table-column>
      <el-table-column class-name="td-operation" width="130">
        <template slot-scope="scope">
          <el-button-group class="operation">
            <el-button icon="el-icon-edit" size="mini" @click="onShowEditForm(scope.row.id)"></el-button>
            <delete-button :id="scope.row.id" store="scriptcategories"></delete-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <scroll-top :duration="1000" :timing="'ease'"></scroll-top>
    <edit-form :editFormState="visible" :itemId="itemId" :onClose="onHideEditForm"></edit-form>
  </section>
</template>

<script lang="ts">
import { Vue, Component, Prop } from "nuxt-property-decorator";
import { State, Action } from 'vuex-class';
import DeleteButton from "~/components/admin/delete-button.vue";
import EditForm from '~/components/admin/script/category/edit-form.vue';

@Component({
  components: {
    DeleteButton,
    EditForm
  }
})
export default class AdminScriptCategoryItems extends Vue {
  @Prop() scriptcategories: any[];
  @Action('scriptcategories/get_all') listAction;
  @Action('scriptcategories/get_form_source') formsourceAction;
  @State(state => state.scriptcategories.formSource) formSource;

  visible: boolean = false;
  itemId: number = 0;

  onShowEditForm(id) {
    this.visible = !this.visible;
    this.itemId = id;
  }

  onHideEditForm() { this.visible = false; }
}
</script>
