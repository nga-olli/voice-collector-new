<template>
  <el-dialog
    :visible.sync="addFormState"
    :before-close="onClose"
    v-on:close="onClosed"
    width="40%">
    <el-row>
      <el-col :md="24" :xs="24">
        <el-col :md="24">
          <el-form autoComplete="on" label-position="left" :model="form" :rules="rules" ref="addForm" >
            <el-form-item prop="name" label="Name">
              <el-input type="text" size="small" v-model="form.name" autofocus clearable></el-input>
            </el-form-item>
            <el-form-item prop="type" label="Type">
              <el-select size="small" v-model="form.type" placeholder="Select reward type" style="width: 100%;" :loading="loading" @change="onChangeType">
                <el-option v-for="item in formSource.typeList" :key="item.id" :label="item.name" :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-row v-for="(attr, index) in attrs" :key="index" :gutter="10" v-show="showAttrs">
              <el-col :md="12">
                <el-form-item>
                  <el-input type="text" autosize size="mini" :placeholder="attr.name" v-on:input="onInputName(index, $event)" clearable>
                    <template slot="append" v-if="attr.unit !== ''"><code>{{ attr.unit }}</code></template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item style="margin-top: 10px">
              <el-button type="primary" :loading="loading" @click.native.prevent="onSubmit"> {{ $t('default.add') }}
              </el-button>
              <el-button @click="onClose">{{ $t('default.cancel') }}</el-button>
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
export default class AddForm extends Vue {
  @Action('rewards/add') addAction;
  @Action('rewards/get_form_source') formsourceAction;
  @Action('rewardtypes/get_attrs') getattrsAction;
  @State(state => state.rewards.formSource) formSource;
  @State(state => state.rewardtypes.attrs) attrs;
  @Prop() addFormState: boolean;
  @Prop() onClose;

  loading: boolean = false;
  showAttrs = false;
  form: any = {
    name: '',
    type: null,
    attrs: null
  };

  $refs: {
    addForm: HTMLFormElement
  }

  get rules() {
    return {
      type: [
        {
          required: true,
          message: this.$t('msg.typeIsRequired'),
          trigger: 'change'
        }
      ]
    };
  }

  onInputName(index, value) {
    this.form.attrs[index].value = value;
  }

  onClosed() {
    this.form.attrs = null;
    this.showAttrs = false;
    this.$refs.addForm.resetFields();
  }

  onSubmit() {
    this.$refs.addForm.validate(async valid => {
      if (valid) {
        // that.loading = true;
        await this.addAction({ formData: this.form })
          // .then(res => {
          //   that.loading = false;

          //   that.$message({
          //     showClose: true,
          //     message: that.$t('msg.addGiftSuccess').toString(),
          //     type: 'success',
          //     duration: 3 * 1000
          //   })

          //   return that.onClose();
          // })
          // .catch(err => {
          //   that.loading = false;
          // });
      } else {
        return false;
      }
    });
  }

  async onChangeType(typeId) {
    this.loading = true;

    await this.getattrsAction({ id: typeId })
      .then(async res => {
        this.loading = false;
        this.showAttrs = true;
        this.form.attrs = [];

        this.attrs.map((item, index) => {
          this.form.attrs[index] = {
            key: item.id,
            value: ''
          };
        });
      })
      .catch(err => {
        this.loading = false;
      })
  }

  created() { return this.initData(); }

  async initData() { return await this.formsourceAction(); }
}
</script>

<style lang="scss">
.cover-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  text-align: center;

  .el-upload {
    width: 100% !important;
    img {
      display: block !important;
    }
  }
  .cover-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    line-height: 178px;
    text-align: center;
  }
  &:hover {
    border-color: #409EFF;
  }
}
</style>
