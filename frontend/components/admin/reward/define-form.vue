<template>
  <div class="panel-body">
    <el-col :md="5" :xs="24">
      <h2>{{ $t('info.define.description') }}</h2>
      <p>{{ $t('info.define.extraDescription') }}</p>
    </el-col>
    <el-col :md="19" :xs="24">
      <el-col :md="24">
        <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="defineForm" size="mini">
          <el-row :gutter="30">
            <el-col :md="7">
              <el-form-item prop="name" label="Name">
                <el-input type="text" v-model="form.name" autofocus clearable></el-input>
              </el-form-item>
              <el-form-item prop="cost" label="Cost">
                <el-input type="text" v-model="form.cost" clearable style="width: 30%"></el-input>
              </el-form-item>
              <el-form-item prop="lowstockthreshold" label="Low stock threshold">
                <el-input type="text" v-model="form.lowstockthreshold" clearable style="width: 30%"></el-input>
              </el-form-item>
            </el-col>
            <el-col :md="6">
              <el-form-item prop="delivery" label="Delivery" label-position="top">
                <el-select v-model="form.delivery" placeholder="Select delivery type" style="width: 100%">
                  <el-option label="Auto" value="1"></el-option>
                  <el-option label="Manual" value="3"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item prop="category" label="Reward Category">
                <el-select size="small" v-model="form.category" placeholder="Select" style="width: 100%">
                  <el-option label="None" :value="0"></el-option>
                  <el-option v-for="item in categoriesFormSource.categoryList" :key="item.id" :label="item.name" :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item prop="status" label="Status">
                <el-select size="small" v-model="form.status" placeholder="Select" style="width: 100%" :loading="loading">
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
          <el-row>
            <el-col :md="20">
              <p><strong>Dynamic Attributes</strong></p>
            </el-col>
          </el-row>
          <el-row style="margin-bottom: 10px">
            <el-col :md="5">Name</el-col>
            <el-col :md="2">Type</el-col>
            <el-col :md="2">Unit</el-col>
            <el-col :md="2">Display order</el-col>
            <el-col :md="2">Display type</el-col>
            <el-col :md="2">Critical?</el-col>
          </el-row>
          <el-row v-for="(attr, index) in form.attrs" :key="index" :gutter="10">
            <el-col :md="5">
              <el-form-item
                :prop="`attrs.${index}.name`"
                :rules="{ required: true, message: $t('msg.nameIsRequired'), trigger: 'blur' }">
                <el-input v-model="attr.name" placeholder="Name" clearable></el-input>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item :prop="`attrs.${index}.type`">
                <el-select v-model="attr.type" placeholder="Select type">
                  <el-option label="Text" value="1"></el-option>
                  <el-option label="QR Code" value="3"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item :prop="`attrs.${index}.unit`">
                <el-input v-model="attr.unit" placeholder="Unit" clearable></el-input>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item :prop="`attrs.${index}.order`">
                <el-input v-model="attr.order" placeholder="Display order" clearable></el-input>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item :prop="`attrs.${index}.displaytype`">
                <el-select v-model="attr.displaytype" placeholder="Select display type">
                  <el-option label="Normal" value="1"></el-option>
                  <el-option label="Emphasis" value="3"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item :prop="`attrs.${index}.critical`">
                <el-select v-model="attr.critical" placeholder="Is critical?">
                  <el-option label="Yes" value="1"></el-option>
                  <el-option label="No" value="3"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :md="2">
              <el-form-item>
                <el-button
                  @click="onRemoveAttribute(attr)"
                  icon="el-icon-fa-trash"
                  type="danger"
                  size="mini"
                  :plain="true">
                </el-button>
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item>
            <el-button @click="onAddAttribute" icon="el-icon-fa-plus" size="mini" type="success"> Add more</el-button>
          </el-form-item>
          <el-row>
            <el-col :md="16">
              <el-form-item prop="description" label="Term and conditions">
                <el-input type="textarea" v-model="form.description" :autosize="{ minRows: 8, maxRows: 16}"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item style="margin-top: 30px">
            <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.add') }}
            </el-button>
            <el-button @click="onReset">{{ $t('default.reset') }}</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-col>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class DefineTypeForm extends Vue {
  @Action('rewardtypes/add') addAction;
  @Action('rewardtypes/get_form_source') formsourceAction;
  @Action('rewardcategories/get_form_source') categoriesFormsourceAction;
  @State(state => state.rewardtypes.formSource) formSource;
  @State(state => state.rewardcategories.formSource) categoriesFormSource;
  @Watch('$route')
  onPageChange() { this.initData(); };

  loading: boolean = false;
  form: any = {
    name: '',
    description: '',
    cost: '',
    lowstockthreshold: '',
    delivery: '1',
    category: null,
    status: null,
    attrs: [
      { key: 1, name: '', type: '1', unit: '', order: 1, displaytype: '1', critical: '3' }
    ]
  };
  imageUrl = '';
  myFiles: any[] = [];

  $refs: {
    defineForm: HTMLFormElement
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

  onAddAttribute() {
    this.form.attrs.push({
      key: this.form.attrs.length + 1,
      name: '',
      type: '1',
      unit: '',
      order: this.form.attrs.length + 1,
      displaytype: '1',
      critical: '3'
    });
  }

  onRemoveAttribute(item) {
    const index = this.form.attrs.indexOf(item);
    if (index !== -1) {
      this.form.attrs.splice(index, 1);
    }
  }

  onReset() {
    this.$refs.defineForm.resetFields();
  }

  onSubmit() {
    this.$refs.defineForm.validate(async valid => {
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
            message: 'Added successfully',
            type: 'success',
            duration: 3 * 1000
          })

          return this.onReset();
        } catch (error) {
          this.loading = false;
        }
      } else {
        return false;
      }
    });
  }

  created() { return this.initData(); }

  async initData() {
    await this.formsourceAction();
    await this.categoriesFormsourceAction();
  }
}
</script>
