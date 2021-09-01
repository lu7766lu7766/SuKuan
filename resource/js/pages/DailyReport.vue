<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td class="col-md-2">用戶:</td>
          <td class="col-md-2">
            <select class="form-control" v-model="editData.userID">
              <option
                v-for="(item, index) in options.subEmpSelect"
                :key="index"
                :value="item.value"
              >
                {{ item.name }}
              </option>
            </select>
          </td>
          <td class="col-md-2">開始日期:</td>
          <td class="col-md-2">
            <date-time-picker
              type="datepicker"
              :date.sync="editData.callStartBillingDate"
            />
          </td>
          <td class="col-md-2">結束日期:</td>
          <td class="col-md-2">
            <date-time-picker
              type="datepicker"
              :date.sync="editData.callStopBillingDate"
            />
          </td>
        </tr>
        <tr>
          <td></td>
          <td colspan="5">
            <input
              type="button"
              class="form-control btn btn-primary get_btn"
              value="列表"
              @click="getList()"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <a class="btn btn-success" style="color: white" @click="exportCSV()"
      >下載
    </a>
    <data-table
      :datas="sortDatas"
      :startIndex="1"
      :columns="[
        { key: 'Date', name: '日期', sortable: true },
        { key: 'UserID', name: '帳號', sortable: true },
        { key: 'UserName', name: '用戶名稱', sortable: true },
        { key: 'CallDuration', name: '時間', sortable: true },
        { key: 'Count', name: '通數', sortable: true },
        { key: 'BillValue', name: '費用', sortable: true },
        { key: 'BillCost', name: '成本', sortable: true, show: isRoot },
      ]"
      :sort="sort"
      @changeSort="(e) => changeSort(e)"
    >
      <template v-slot:ExtensionNo="{ data }">
        {{ data.ExtensionNo || "Wait" }}
      </template>

      <template v-slot:BillValue="{ data }">
        {{ (+data.BillValue).toFixed(2) }}
      </template>

      <template v-slot:BillCost="{ data }">
        {{ (+data.BillCost).toFixed(2) }}
      </template>

      <template v-slot:tfoot>
        <tr>
          <td colspan="2">合計</td>
          <td></td>
          <td></td>
          <td>{{ _.jSumBy(datas, "CallDuration") }}</td>
          <td>{{ _.jSumBy(datas, "Count") }}</td>
          <td>{{ _.jSumBy(datas, "BillValue").toFixed(2) }}</td>
          <td v-if="isRoot">{{ _.jSumBy(datas, "BillCost").toFixed(2) }}</td>
        </tr>
      </template>
    </data-table>
  </div>
</template>

<script>
import DateTimePicker from "../components/DateTimePicker.vue";
import OrderByMixins from "mixins/OrderBy";
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import EmpMixins from "mixins/Emp";
import LibraryMixins from "mixins/Library";

export default {
  mixins: [CommonMixins, OrderByMixins, ListMixins, LibraryMixins, EmpMixins],
  components: { DateTimePicker },
  data: () => ({
    editData: {
      userID: "",
      callStartBillingDate: moment().startOf("month").format("YYYY/MM/DD"),
      callStopBillingDate: moment().subtract(1, "days").format("YYYY/MM/DD"),
    },
  }),
  methods: {
    async getOptions() {
      await this.getSubEmpSelect();
      this.editData.userID = _(this.options.subEmpSelect).get("0.value")
    },
    async getList() {
      const res = await $.callApi.post("api/dailyReport/list", this.editData);
      this.datas = res.data;
    },
    exportCSV() {
      this.fileFunc.exportCSV(
        [
          ["日期", "帳號", "分機號", "用戶名稱", "時間", "通數", "費用", "成本"].join(
            ","
          ),
        ]
          .concat(
            this.sortDatas.map((x) =>
              [
                x.Date,
                x.UserID,
                x.ExtensionNo || "Wait",
                x.UserName,
                x.CallDuration,
                x.Count,
                (+x.BillValue).toFixed(2),
                this.isRoot ? (+x.BillCost).toFixed(2) : "-",
              ].join(",")
            )
          )
          .join("\r\n"),
        "話務排行.csv"
      );
    },
  },
  mounted() {
    this.getOptions()
  },
};
</script>