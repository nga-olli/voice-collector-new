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
            <el-form-item prop="name" label="Name">
              <el-input type="text" size="small" v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item prop="description" label="Description">
              <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" v-model="form.description"></el-input>
            </el-form-item>
            <el-form-item prop="maxcoinreward" label="Max coin reward">
              <el-input type="text" size="small" v-model="form.maxcoinreward"></el-input>
            </el-form-item>
            <el-form-item prop="numberofscripts" label="Number of scripts">
              <el-input type="text" size="small" v-model="form.numberofscripts"></el-input>
            </el-form-item>
            <el-form-item prop="vscid" label="Voice script category">
              <el-select size="small" v-model="form.vscid" placeholder="Select" style="width: 100%" :loading="loading">
                <el-option v-for="item in formSource.voicescriptcategoryList" :key="item.id" :label="item.name" :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item prop="status" label="Status">
              <el-select size="small" v-model="form.status" placeholder="Select" style="width: 100%" :loading="loading">
                <el-option v-for="item in formSource.statusList" :key="item.label" :label="item.label" :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item prop="isvalidate" label="Validation required">
              <el-radio v-model="form.isvalidate" label="1">Yes</el-radio>
              <el-radio v-model="form.isvalidate" label="3">No</el-radio>
            </el-form-item>
            <el-form-item prop="dateexpired" label="Date expired">
              <el-date-picker type="date" placeholder="Pick a date" v-model="form.dateexpired" style="width: 100%;"></el-date-picker>
            </el-form-item>
            <el-form-item prop="postedby" label="Posted by">
              <el-input type="text" size="small" v-model="form.postedby"></el-input>
            </el-form-item>
            <el-form-item prop="requiredid" label="Required Job ID">
              <el-input type="text" size="small" v-model="form.requiredid" placeholder="enter the ID of the job required to be finished before this job"></el-input>
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
  @Action('jobs/get_form_source') formsourceAction;
  @Action('jobs/add') addAction;
  @State(state => state.jobs.formSource) formSource;
  @Watch('$route')
  onPageChange() { this.initData(); };

  loading: boolean = false;
  form: object = {
    name: '',
    description: '',
    maxcoinreward: '',
    numberofscripts: '',
    vscid: null,
    isvalidate: null,
    dateexpired: null,
    postedby: '',
    requiredid: '',
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
        // this.loading = true;
        console.log(this.myFiles)

        await this.addAction({
          formData: this.form,
          covers: this.myFiles
        })
          // .then(res => {
          //     this.loading = false;

          //     this.$message({
          //       showClose: true,
          //       message: this.$t('msg.addSuccess').toString(),
          //       type: 'success',
          //       duration: 3 * 1000
          //     })

          //     this.$refs.addForm.resetFields();
          // })
          // .catch(err => {
          //   this.loading = false;
          // })
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
