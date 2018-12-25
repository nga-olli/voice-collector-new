<lang src="./lang.yml"></lang>

<template>
  <div>
    <el-select clearable size="mini" v-model="categoryId" placeholder="Select category" style="float: right;margin-left: 10px;">
      <el-option v-for="item in formSource.voicescriptcategoryList" :key="item.id" :label="item.name" :value="item.id">
      </el-option>
    </el-select>
    <el-upload
      action=""
      multiple
      :auto-upload="false"
      with-credentials
      :file-list="myFiles"
      :on-change="onChange"
      :on-remove="onRemove"
      >
      <el-button slot="trigger" size="mini" type="primary">
        Upload voice script files
      </el-button>
      <el-button v-show="myFiles.length > 0" :loading="loading" style="margin-left: 10px;" size="mini" icon="el-icon-fa-upload" type="success" @click="onUpload">
        {{ $t('label.import') }}
      </el-button>
      <div class="el-upload__tip" slot="tip">
        {{ $t('msg.fileTypeAllowed') }}
      </div>
    </el-upload>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import { Action, State } from 'vuex-class';

@Component
export default class ImportButton extends Vue {
  @Action('scripts/import') importAction;
  @Action('scripts/get_all') listAction;
  @State(state => state.scripts.formSource) formSource;

  loading: boolean = false;
  myFiles: any[] = [];
  categoryId: any = null;

  onChange(file, filelist) {
    this.myFiles = filelist;
  }

  onRemove(file, filelist) {
    this.myFiles = filelist;
  }

  async onUpload() {
    if (this.categoryId === null) {
      this.$message({
        showClose: true,
        message: `Please select Script category`,
        type: 'warning',
        duration: 5 * 1000
      });
      return;
    }

    this.loading = true;

    await this.importAction({ formData: {
      categoryId: this.categoryId,
      files: this.myFiles
    } })
      .then(async res => {
        this.loading = false;

        this.$message({
          showClose: true,
          message: `${res.data.scriptsImported} ${this.$t('msg.importSuccess').toString()}`,
          type: 'success',
          duration: 3 * 1000
        });

        await this.listAction({ query: {} });

        this.myFiles = [];
        this.categoryId = null;
      })
      .catch(err => {
        this.loading = false;
      });
  }
}
</script>
