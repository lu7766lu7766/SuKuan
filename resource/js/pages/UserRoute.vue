<template>
  <div class="table-responsive">
    <validation v-slot="{ invalid }">
      <table class="table table-v table-condensed">
        <tbody>
          <tr>
            <td>*用戶:</td>
            <td>
              <validate rules="required">
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
              </validate>
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
            <td>*前置碼:</td>
            <td>
              <validate rules="required">
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.PrefixCode"
                  :disabled="isUpdate"
                />
              </validate>
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
              <validate rules="required|min_value:1|max_value:65535">
                <input
                  type="text"
                  class="form-control"
                  value="5060"
                  placeholder="1~65535"
                  v-model.number="editData.TrunkPort"
                />
              </validate>
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
                :disabled="invalid"
                :value="isUpdate ? '更新' : '新增'"
                @click="isUpdate ? doUpdate() : doCreate()"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </validation>

    <div>
      <button type="button" class="btn btn-success" @click="exportCSV()">
        下載
      </button>

      <button type="button" class="btn btn-warning" @click="upload()">
        上傳
      </button>

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
        { key: 'SubNum', name: '刪除機碼' },
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
import ListMixins from "mixins/List";
import LibraryMixins from "mixins/Library";

export default {
  mixins: [ListMixins, LibraryMixins],
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
      const res = await $.callApi.post("api/userRoute/list");
      this.datas = res.data;
    },
    edit(data) {
      this.isUpdate = true;
      this.editData = _.cloneDeep(data);
    },
    async doCreate() {
      await $.callApi.post("api/userRoute/create", this.editData);
      this.$swal("新增成功");
      this.editData = {};
      this.getList();
    },
    async doUpdate() {
      await $.callApi.post("api/userRoute/update", this.editData);
      this.$swal("更新成功");
      this.isUpdate = false;
      this.editData = {};
      this.getList();
    },
    async doDelete(data) {
      await this.$confirm();
      await $.callApi.post("api/userRoute/delete", {
        UserID: data.UserID,
        PrefixCode: data.PrefixCode,
      });
      this.getList();
    },
    exportCSV() {
      this.fileFunc.exportCSV(
        [
          [
            "用戶",
            "前置碼",
            "新增前置碼",
            "顯示號碼",
            "Trunk IP",
            "Trunk Port",
            "路由名稱",
            "刪除機碼",
          ].join(","),
        ]
          .concat(
            this.filterDatas.map((x) =>
              [
                x.UserID,
                x.PrefixCode,
                x.AddPrefix,
                x.RouteCLI,
                x.TrunkIP,
                x.TrunkPort,
                x.RouteName,
                x.SubNum,
              ].join(",")
            )
          )
          .join("\r\n"),
        "自動撥號路由.csv"
      );
    },
    async upload() {
      const file = await this.$upload({
        accept: ".csv",
      });
      const text = await this.fileFunc.toText(file);
      const datas = text
        .split("\r\n")
        .slice(1)
        .filter((x) => x)
        .map((line) => {
          const {
            0: UserID,
            1: PrefixCode,
            2: AddPrefix,
            3: RouteCLI,
            4: TrunkIP,
            5: TrunkPort,
            6: RouteName,
            7: SubNum,
          } = line.split(",").map((x) => x.trim());
          return _.pickBy({
            UserID,
            PrefixCode,
            AddPrefix,
            RouteCLI,
            TrunkIP,
            TrunkPort,
            RouteName,
            SubNum,
          });
        });
      await $.callApi.post("api/userRoute/create/batch", { datas });
      this.$swal("新增成功");
      this.getList();
    },
  },
  computed: {
    filterDatas() {
      return this.keyword
        ? this.datas.filter((x) => x.UserID == this.keyword)
        : this.datas;
    },
  },
  mounted() {
    this.getOptions();
    this.getList();
  },
};
</script>