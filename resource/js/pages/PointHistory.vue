<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td class="col-md-2">開始日期:</td>
          <td class="col-md-2">
            <input
              type="text"
              name="startDate"
              class="form-control date-picker"
              v-model="editData.StartDate"
            />
          </td>
          <td class="col-md-2">結束日期:</td>
          <td class="col-md-2">
            <input
              type="text"
              name="endDate"
              class="form-control date-picker today-date"
              v-model="editData.EndDate"
            />
          </td>
          <td class="col-md-2">備註:</td>
          <td class="col-md-2">
            <input
              type="text"
              name="memo"
              class="form-control"
              v-model="editData.Memo"
            />
          </td>
        </tr>
        <tr>
          <td>用戶:</td>
          <td>
            <select class="form-control" v-model="editData.UserID">
              <option value=""></option>
              <option
                v-for="(item, index) in options.subEmp"
                :key="index"
                :value="item.value"
              >
                {{ item.name }}
              </option>
            </select>
          </td>
          <td></td>
          <td colspan="3">
            <input
              type="button"
              class="form-control btn btn-primary get_btn"
              value="列表"
              @click="doSearch()"
            />
          </td>
        </tr>
      </tbody>
    </table>
    <a class="btn btn-success" style="color: white" @click="exportCSV()"
      >下載
    </a>
    <data-table
      :startIndex="startIndex"
      :datas="datas"
      :columns="[
        { key: 'SaveUserID', name: '儲值者' },
        { key: 'UserID', name: '儲值對象' },
        { key: 'AddValue', name: '儲值金額' },
        { key: 'AddTime', name: '儲值時間' },
        { key: 'Memo', name: '備註' },
      ]"
    >
      <template v-slot:Memo="{ data }">
        <input
          type="text"
          class="form-control"
          v-if="isRoot"
          v-model="data.Memo"
          @change="doUpdate(data)"
        />
        <span v-else>{{ data.Memo }}</span>
      </template>
    </data-table>
  </div>
</template>

<script>
import CommonMiins from "mixins/Common";
import PaginateMiins from "mixins/Paginate";
import ListMiins from "mixins/List";
import LibraryMixins from "mixins/Library";

export default {
  mixins: [CommonMiins, PaginateMiins, ListMiins, LibraryMixins],
  data: () => ({
    options: {
      subEmp: [],
    },
    editData: {
      StartDate: moment().startOf("month").format("YYYY/MM/DD"),
      EndDate: moment().format("YYYY/MM/DD"),
      Memo: "",
      UserID: "",
    },
  }),
  methods: {
    async getOptions() {
      const res = await $.callApi.post("oldApi/subEmp");
      this.options.subEmp = res.data;
    },
    async getList() {
      const res = await $.callApi.post(
        "api/point/history/list",
        this.requestBody
      );
      this.datas = res.data;
    },
    async getTotal() {
      const res = await $.callApi.post(
        "api/point/history/total",
        this.requestBody
      );
      this.paginate.total = res.data;
    },
    doSearch() {
      this.getTotal();
      this.getList();
    },
    async doUpdate(data) {
      await $.callApi.post("api/point/history/update", data);
      alertify.alert("更新成功");
    },
    exportCSV() {
      this.fileFunc.exportCSV(
        [["儲值者", "儲值對象", "儲值金額", "儲值時間", "備註"].join(",")]
          .concat(
            this.datas.map((x) =>
              [x.SaveUserID, x.UserID, x.AddValue, x.AddTime, x.Memo].join(",")
            )
          )
          .join("\r\n"),
        "儲值紀錄.csv"
      );
    },
  },
  mounted() {
    this.getOptions();
  },
};
</script>