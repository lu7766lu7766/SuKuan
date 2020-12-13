<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td>用戶:</td>
          <td>
            <select
              class="form-control"
              v-model="editData.UserID"
              :disabled="isUpdate"
            >
              <option
                v-for="(item, index) in options.subEmp"
                :key="index"
                :value="item.value"
              >
                {{ item.name }}
              </option>
            </select>
          </td>
          <td>顯示號碼:</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.RouteCLI"
            />
          </td>
          <td>Trunk IP:</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.TrunkIP"
            />
          </td>
        </tr>
        <tr>
          <td>前置碼:</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.PrefixCode"
              :disabled="isUpdate"
            />
          </td>
          <td>新增前置碼:</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.AddPrefix"
            />
          </td>
          <td>*Trunk port:</td>
          <td>
            <input
              type="text"
              class="form-control"
              :class="{
                'input-invalid': isTrunkPortEmpty || isTrunkPortError,
              }"
              value="5060"
              placeholder="1~65535"
              v-model="editData.TrunkPort"
            />
          </td>
        </tr>
        <tr>
          <td>路由名稱:</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.RouteName"
            />
          </td>
          <td></td>
          <td colspan="3">
            <input
              type="button"
              class="form-control btn btn-primary"
              :disabled="hasInvalid"
              :value="isUpdate ? '更新' : '新增'"
              @click="isUpdate ? doUpdate() : doCreate()"
            />
          </td>
        </tr>
      </tbody>
    </table>
    <div>
      用戶切換：
      <select v-model="keyword">
        <option value="">顯示全部</option>
        <option
          v-for="(item, index) in options.subEmp"
          :key="index"
          :value="item.value"
        >
          {{ item.name }}
        </option>
      </select>
    </div>
    <data-table
      :startIndex="1"
      :datas="filterDatas"
      :columns="[
        { key: 'UserID', name: '用戶' },
        { key: 'PrefixCode', name: '前置碼' },
        { key: 'AddPrefix', name: '新增前置碼' },
        { key: 'RouteCLI', name: '顯示號碼' },
        { key: 'TrunkIP', name: 'Trunk IP' },
        { key: 'TrunkPort', name: 'Trunk Port' },
        { key: 'RouteName', name: '路由名稱' },
        { key: 'SubNum', name: '刪除幾碼' },
        { key: 'action', name: '操作' },
      ]"
    >
      <template v-slot:action="{ data }">
        <button type="button" class="btn btn-info" @click="edit(data)">
          編輯
        </button>
        <button type="button" class="btn btn-danger" @click="doDelete(data)">
          刪除
        </button>
      </template>
    </data-table>
  </div>
</template>

<script>
import DataTable from "../components/DataTable";
import ListMixins from "mixins/List";

export default {
  mixins: [ListMixins],
  components: {
    DataTable,
  },
  data: () => ({
    options: {
      subEmp: [],
    },
    keyword: "",
    editData: {},
    isUpdate: false,
  }),
  methods: {
    async getOptions() {
      const res = await $.callApi.post("oldApi/subEmp");
      this.options.subEmp = res.data;
    },
    async getList() {
      const res = await $.callApi.post("api/manualUserRoute/list");
      this.datas = res.data;
    },
    edit(data) {
      this.isUpdate = true;
      this.editData = _.cloneDeep(data);
    },
    async doCreate() {
      await $.callApi.post("api/manualUserRoute/create", this.editData);
      this.$swal("新增成功");
      this.editData = {};
      this.getList();
    },
    async doUpdate() {
      await $.callApi.post("api/manualUserRoute/update", this.editData);
      this.$swal("更新成功");
      this.isUpdate = false;
      this.editData = {};
      this.getList();
    },
    async doDelete(data) {
      await this.$confirm();
      await $.callApi.post("api/manualUserRoute/delete", {
        UserID: data.UserID,
        PrefixCode: data.PrefixCode,
      });
      this.getList();
    },
  },
  computed: {
    filterDatas() {
      return this.keyword
        ? this.datas.filter((x) => x.UserID == this.keyword)
        : this.datas;
    },
    isTrunkPortEmpty() {
      return !this.editData.TrunkPort;
    },
    isTrunkPortError() {
      return this.editData.TrunkPort < 1 || this.editData.TrunkPort > 65535;
    },
    hasInvalid() {
      return this.isTrunkPortEmpty || this.isTrunkPortError;
    },
  },
  mounted() {
    this.getOptions();
    this.getList();
  },
};
</script>