<template>
  <el-row>
    <el-col :span="24">
      <div class="filter-icon" @click="onBack">
        <i class="el-icon-fa-angle-left"></i>
      </div>
      <breadcrumb :data="[
        { name: 'Reward', link: '/admin/reward/category' },
        { name: `Edit reward category: ${form.name}`, link: '' }
      ]">
      </breadcrumb>
    </el-col>
    <el-col :span="24">
      <div class="panel-body">
        <el-col :md="5" :xs="24">
          <h2>{{ $t('info.edit.description') }}</h2>
          <p>{{ $t('info.edit.extraDescription') }}</p>
        </el-col>
        <el-col :md="19" :xs="24">
          <el-col :md="24">
            <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="editForm" size="small">
              <el-row :gutter="30">
                <el-col :md="9">
                  <el-form-item prop="parentid" label="Parent ID">
                    <el-select v-model="form.parentid" placeholder="Select" style="width: 100%">
                      <el-option label="None" :value="0"></el-option>
                      <el-option v-for="item in formSource.categoryList" :key="item.id" :label="item.name" :value="item.id">
                      </el-option>
                    </el-select>
                  </el-form-item>
                  <el-form-item prop="name" label="Name">
                    <el-input type="text" v-model="form.name" autofocus clearable></el-input>
                  </el-form-item>
                  <el-form-item prop="description" label="Description">
                    <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="form.description"></el-input>
                  </el-form-item>
                  <el-form-item prop="displayorder" label="Display order">
                    <el-input type="text" v-model="form.displayorder" clearable></el-input>
                  </el-form-item>
                  <el-form-item prop="status" label="Status">
                    <el-select v-model="form.status" placeholder="Select" style="width: 100%" :loading="loading">
                      <el-option v-for="item in formSource.statusList" :key="item.label" :label="item.label" :value="item.value">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :md="6">
                  <el-form-item>
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
                  </el-form-item>
                </el-col>
              </el-row>
              <el-form-item style="margin-top: 30px">
                <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.update') }}
                </el-button>
                <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
              </el-form-item>
            </el-form>
          </el-col>
        </el-col>
      </div>
    </el-col>
  </el-row>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';
import Breadcrumb from '~/components/admin/breadcrumb.vue';

@Component({
  layout: 'admin',
  middleware: 'authenticated',
  components: {
    Breadcrumb
  }
})
export default class AdminRewardCategoryEditPage extends Vue {
  @Action('rewardcategories/get') getAction;
  @Action('rewardcategories/update') updateAction;
  @Action('rewardcategories/get_form_source') formsourceAction;
  @State(state => state.rewardcategories.formSource) formSource;
  @State(state => state.rewardcategories.data) rewards;
  @Watch('$route')
  onPageChange() { this.initData() }

  loading: boolean = false;
  form: any = {
    name: '',
    description: '',
    displayorder: '',
    parentid: null,
    status: null
  };
  imageUrl = '';
  myFiles: any[] = [];

  $refs: {
    editForm: HTMLFormElement
  }

  head() {
    return {
      title: 'Rewards',
      meta: [
        { hid: 'description', name: 'description', content: 'Rewards' }
      ]
    };
  }

  get rules() {
    return {
      name: [
        {
          required: true,
          message: 'Name is required',
          trigger: 'blur'
        }
      ]
    };
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

  onReset() {
    this.initData();
  }

  onSubmit() {
    this.$refs.editForm.validate(async valid => {
      if (valid) {
        try {
          this.loading = true;

          await this.updateAction({
            id: this.$route.params.id,
            formData: this.form,
            covers: this.myFiles
          });

          this.loading = false;

          this.$message({
            showClose: true,
            message: 'Update successfully',
            type: 'success',
            duration: 3 * 1000
          });

          this.initData();
        } catch (error) {
          this.loading = false;
        }
      } else {
        return false;
      }
    });
  }

  created() {
    this.initData();
  }

  async initData() {
    try {
      this.loading = true;
      await this.formsourceAction();
      const myCategory = await this.getAction({ id: this.$route.params.id });
      this.loading = false;

      this.form = {
        name: myCategory.data.name,
        description: myCategory.data.description,
        displayorder: myCategory.data.displayorder,
        parentid: myCategory.data.parentid,
        status: myCategory.data.status.value
      };
      this.imageUrl = myCategory.data.cover;

      document.title = `Edit reward category: "${myCategory.data.name}"`;
    } catch (error) {
      this.loading = false;
    }
  }

  onBack() { return this.$router.go(-1); }
}
</script>

<style lang="scss" scoped>
.el-upload {
  img {
    width: 145px;
    height: 145px;
  }
}
.panel-body {
  margin-left: 0 ;
}

.filter-icon {
  cursor: pointer;
}
</style>