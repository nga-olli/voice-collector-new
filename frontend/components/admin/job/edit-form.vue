<template>
  <el-dialog
    :visible.sync="editFormState"
    :before-close="onClose"
    :lock-scroll="true"
    v-on:open="onOpen"
    v-on:close="onClosed">
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="editForm">
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
              <el-date-picker type="date" format="dd/MM/yyyy" value-format="timestamp" placeholder="Pick a date" v-model="form.dateexpired" style="width: 100%;"></el-date-picker>
            </el-form-item>
            <el-form-item prop="postedby" label="Posted by">
              <el-input type="text" size="small" v-model="form.postedby"></el-input>
            </el-form-item>
            <el-form-item prop="requiredid" label="Required Job ID">
              <el-input type="text" size="small" v-model="form.requiredid" placeholder="enter the ID of the job required to be finished before this job"></el-input>
            </el-form-item>
            <el-form-item style="margin-top: 30px">
              <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.update') }}
              </el-button>
              <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
            </el-form-item>
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
          </el-form>
        </el-col>
      </el-col>
    </el-row>
  </el-dialog>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class EditForm extends Vue {
  @Action('jobs/get_form_source') formsourceAction;
  @Action('jobs/get') getAction;
  @Action('jobs/update') updateAction;
  @State(state => state.jobs.formSource) formSource;

  @Prop() itemId: number;
  @Prop() editFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  form: object = {};
  imageUrl = '';
  myFiles: any[] = [];

  $refs: {
    editForm: HTMLFormElement
  }

  get rules() {
    return {
      name: [
        {
          required: true,
          message: this.$t('msg.nameIsRequired'),
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

  async onOpen() {
    return await this.getAction({ id: this.itemId })
      .then(res => {
        this.form = {
          name: res.data.name,
          description: res.data.description,
          maxcoinreward: res.data.maxcoinreward,
          numberofscripts: res.data.numberofscripts,
          vscid: res.data.vscid,
          isvalidate: res.data.isvalidate,
          dateexpired: parseInt(res.data.dateexpired.timestamp + '000'),
          postedby: res.data.postedby,
          requiredid: res.data.requiredid,
          status: res.data.status.value,
        };
        this.imageUrl = res.data.cover
      });
  }

  onClosed() {
    this.$refs.editForm.clearValidate();
  }

  onSubmit() {
    this.$refs.editForm.validate(async valid => {
      if (valid) {
        try {
          this.loading = true;
          const res = await this.updateAction({
            id: this.itemId,
            formData: this.form,
            covers: this.myFiles
          });

          this.loading = false;
          this.$message({
              showClose: true,
              message: this.$t('msg.updateSuccess').toString(),
              type: 'success',
              duration: 3 * 1000
            })

          return this.onClose();
        } catch (error) {
          this.loading = false;
        }
      } else {
        return false;
      }
    });
  }

  async onReset() {
    this.$refs.editForm.resetFields();
    await this.getAction({ id: this.itemId })
      .then(res => {
        this.form = {
          name: res.data.name,
          description: res.data.description,
          maxcoinreward: res.data.maxcoinreward,
          numberofscripts: res.data.numberofscripts,
          vscid: res.data.vscid,
          isvalidate: res.data.isvalidate,
          dateexpired: res.data.dateexpired.timestamp,
          postedby: res.data.postedby,
          requiredid: res.data.requiredid,
          status: res.data.status.value
        };
        this.imageUrl = res.data.cover
      });
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction() }
}
</script>
