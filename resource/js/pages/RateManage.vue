<template>
  <div class="table-responsive">
    <table class="table table-v">
      <tbody>
        <tr>
          <td class="col-md-3">費率表代號</td>
          <td class="col-md-9">
            <div class="input-group">
              <input
                type="text"
                class="form-control"
                v-model="editData.RateGroupID"
              />
              <span class="input-group-addon">( 1 ~ 99999 )</span>
            </div>
          </td>
        </tr>
        <tr>
          <td>費率表名稱</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.RateGroupName"
            />
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input
              class="btn btn-primary form-control"
              type="button"
              value="新增"
              @click="doCreate()"
            />
          </td>
        </tr>
      </tbody>
    </table>
    <input
      type="button"
      class="btn btn-danger"
      value="Delete"
      @click="doDelete()"
    />

    <button type="button" class="btn btn-success" @click="exportCSV()">
      下載
    </button>

    <button type="button" class="btn btn-warning" @click="upload()">
      上傳
    </button>

    <data-table
      allChecked
      :datas="datas"
      :columns="[
        { key: 'RateGroupID', name: '費率表代號', show: isRoot },
        { key: 'RateGroupName', name: '費率表名稱' },
        { key: 'action', name: '操作' },
      ]"
    >
      <template v-slot:allChecked>
        <input type="checkbox" class="checkAll" v-model="isAllChecked" />
      </template>

      <template v-slot:checked="{ data }">
        <input
          type="checkbox"
          v-model="data.checked"
          v-if="choice != data.UserID"
        />
      </template>

      <template v-slot:RateGroupName="{ data }">
        <span v-if="isRoot">
          <input
            type="text"
            class="form-control"
            v-model="data.RateGroupName"
            @change="doUpdate(data.RateGroupID, data.RateGroupName)"
          />
        </span>
        <span v-else>{{ data.RateGroupName }}</span>
      </template>
      <template v-slot:action="{ data }">
        <input
          type="button"
          class="btn btn-info"
          value="編輯"
          @click="toModify(data.RateGroupID, data.RateGroupName)"
        />
      </template>
    </data-table>
  </div>
</template>

<script>
import CommonMixins from "mixins/Common";
import DataTable from "../components/DataTable";
import ListMixins from "mixins/List";
import LibraryMixins from "mixins/Library";

export default {
  components: { DataTable },
  mixins: [CommonMixins, ListMixins, LibraryMixins],
  methods: {
    async getList() {
      const res = await $.callApi.post("rate/list");
      this.datas = res.data.map((x) => ((x.checked = false), x));
    },
    toModify(id, name) {
      redirect(
        "userInfo/userRatesModify?rateGroupId=" + id + "&rateGroupName=" + name
      );
    },
    async doCreate() {
      const res = await $.callApi.post("rate/create", {
        RateGroupID: this.editData.RateGroupID,
        RateGroupName: this.editData.RateGroupName,
      });
      alertify.alert("已成功新增!");
      this.getList();
    },
    async doUpdate(RateGroupID, RateGroupName) {
      const res = await $.callApi.post("rate/update", {
        RateGroupID,
        RateGroupName,
      });
      alertify.alert("已成功修改!");
      this.getList();
    },
    async doDelete() {
      const RateGroupIDs = this.datas
        .filter((x) => x.checked)
        .map((x) => x.RateGroupID);
      const res = await $.callApi.post("rate/delete", {
        RateGroupIDs,
      });
      alertify.alert("已成功刪除!");
      this.getList();
    },
    exportCSV() {
      this.fileFunc.exportCSV(
        [["費率表代號", "費率表名稱"].join(",")]
          .concat(
            this.datas.map((x) =>
              [x.RateGroupID, x.RateGroupName].join(",")
            )
          )
          .join("\r\n"),
        "費率表.csv"
      );
    },
    async upload() {
      const file = await this.$upload({
        accept: ".csv",
      });
      const text = await this.fileFunc.toText(file);
      const datas = text.split("\r\n").map((line) => {
        const { 0: RateGroupID, 1: RateGroupName } = line
          .split(",")
          .map((x) => x.trim());
        return {
          RateGroupID,
          RateGroupName,
        };
      });
      await $.callApi.post("rate/create/batch", { datas });
      alertify.alert("已成功新增!");
      this.getList();
    },
  },
  mounted() {
    this.getList();
  },
};
</script>