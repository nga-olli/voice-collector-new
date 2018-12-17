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
            <el-col :md="8">
              <el-form-item prop="name" label="Name">
                <el-input type="text" v-model="form.name" autofocus clearable></el-input>
              </el-form-item>
              <el-form-item prop="cost" label="Cost">
                <el-input type="text" v-model="form.cost" clearable></el-input>
              </el-form-item>
              <el-form-item prop="lowstockthreshold" label="Low stock threshold">
                <el-input type="text" v-model="form.lowstockthreshold" clearable></el-input>
              </el-form-item>
            </el-col>
            <el-col :md="8">
              <el-form-item prop="delivery" style="margin-top: 28px">
                <el-select v-model="form.delivery" placeholder="Select delivery type">
                  <el-option label="Auto" value="1"></el-option>
                  <el-option label="Manual" value="3"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item prop="description" label="Description">
                <el-input type="textarea" v-model="form.description" autosize :autosize="{ minRows: 6, maxRows: 8}"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :md="20">
              <p>Dynamic Attributes</p>
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
import { Vue, Component, Prop } from 'nuxt-property-decorator';
import { Action } from 'vuex-class';

@Component
export default class DefineTypeForm extends Vue {
  @Action('rewardtypes/add') addAction;
  // @Action('gifts/get_form_source') formsourceAction;

  loading: boolean = false;
  form: any = {
    name: '',
    description: '',
    cost: '',
    lowstockthreshold: '',
    delivery: '1',
    category: '1',
    attrs: [
      { key: 1, name: '', type: '1', unit: '', order: 1, displaytype: '1', critical: '3' }
    ]
  };

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
        this.loading = true;
        await this.addAction({ formData: this.form });

        this.loading = false;

        this.$message({
          showClose: true,
          message: this.$t('msg.addSuccess').toString(),
          type: 'success',
          duration: 3 * 1000
        })

        // await this.formsourceAction();

        return this.onReset();
      } else {
        return false;
      }
    });
  }
}
</script>
