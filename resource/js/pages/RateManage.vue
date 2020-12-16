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
                v-model="data.RateGroupID"
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
              v-model="data.RateGroupName"
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
    <a class="btn btn-success" style="color: white" @click="exportCSV()"
      >下載
    </a>
    <data-table
      allChecked
      :datas="rateGroup"
      :columns="[
        { key: 'RateGroupID', name: '費率表代號', show: isRoot },
        { key: 'RateGroupName', name: '費率表名稱' },
        { key: 'action', name: '操作' },
      ]"
    >
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
import LibraryMixins from "mixins/Library";

export default {
  components: { DataTable },
  mixins: [CommonMixins, LibraryMixins],
  data: () => ({
    rateGroup: [],
    data: {},
  }),
  methods: {
    async getList() {
      const res = await $.callApi.post("rate/list");
      this.rateGroup = res.data.map((x) => ((x.checked = false), x));
    },
    toModify(id, name) {
      redirect(
        "userInfo/userRatesModify?rateGroupId=" + id + "&rateGroupName=" + name
      );
    },
    async doCreate() {
      const res = await $.callApi.post("rate/create", {
        RateGroupID: this.data.RateGroupID,
        RateGroupName: this.data.RateGroupName,
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
      const RateGroupIDs = this.rateGroup
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
            this.rateGroup.map((x) =>
              [x.RateGroupID, x.RateGroupName].join(",")
            )
          )
          .join("\r\n"),
        "費率表.csv"
      );
    },
  },
  mounted() {
    this.getList();
  },
};
</script>