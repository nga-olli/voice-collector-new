<template>
  <div class="panel-body">
    <el-col :md="5" :xs="24">
      <h2>{{ $t('info.add.description') }}</h2>
      <p>{{ $t('info.add.extraDescription') }}</p>
    </el-col>
    <el-col :md="19" :xs="24">
      <el-row :gutter="30">
        <el-col :md="9">
          <el-form autoComplete="on" label-position="left" :model="form" ref="addForm" size="small">
            <el-form-item prop="parentid" label="Parent ID">
              <el-select size="small" v-model="form.parentid" placeholder="Select category" style="width: 100%" :loading="loading">
                <el-option label="None" :value="0"></el-option>
                <el-option v-for="item in formSource.categoryList" :key="item.id" :label="item.name" :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item prop="name" label="Name">
              <el-input type="text" size="small" v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item prop="description" label="Description">
              <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="form.description"></el-input>
            </el-form-item>
            <el-form-item prop="displayorder" label="Display order">
              <el-input type="text" size="small" v-model="form.displayorder"></el-input>
            </el-form-item>
            <el-form-item prop="status" label="Status">
              <el-select size="small" v-model="form.status" placeholder="Select" style="width: 100%" :loading="loading">
                <el-option v-for="item in formSource.statusList" :key="item.label" :label="item.label" :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item style="margin-top: 30px">
              <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.add') }}
              </el-button>
              <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
            </el-form-item>
          </el-form>
        </el-col>
        <el-col :md="9">
          <el-upload
            ref="cover"
            action=""
            :auto-upload="false"
            :limit="1"
            list-type="picture-card"
            :on-preview="handlePictureCardPreview"
            :on-change="onChange"
            :on-remove="onRemove">
            <img v-if="imageUrl" :src="imageUrl">
            <i v-else class="el-icon-plus"></i>
          </el-upload>
        </el-col>
      </el-row>
    </el-col>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class AddForm extends Vue {
  @Action('rewardcategories/get_form_source') formsourceAction;
  @Action('rewardcategories/add') addAction;
  @State(state => state.rewardcategories.formSource) formSource;
  @Watch('$route')
  onPageChange() { this.initData(); };

  loading: boolean = false;
  form: object = {
    name: '',
    description: '',
    displayorder: '',
    parentid: null,
    status: null
  };
  imageUrl = '';
  myFiles: any[] = [];

  $refs: {
    addForm: HTMLFormElement
  }

  onChange(file, filelist) {
    this.myFiles = filelist;
  }

  onRemove(file, filelist) {
    this.myFiles = filelist;
  }

  handlePictureCardPreview(file) {
    this.imageUrl = file.url;
  }

  onSubmit() {
    this.$refs.addForm.validate(async valid => {
      if (valid) {
        try {
          this.loading = true;
          await this.addAction({
            formData: this.form,
            covers: this.myFiles
          });
          this.loading = false;

          this.$message({
            showClose: true,
            message: this.$t('msg.addSuccess').toString(),
            type: 'success',
            duration: 3 * 1000
          })

          this.$refs.addForm.resetFields();
        } catch (error) {
          this.loading = false; 
        }
      } else {
        return false;
      }
    });
  }

  onReset() { return this.$refs.addForm.resetFields(); }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }
}
</script>

<style>

</style>
