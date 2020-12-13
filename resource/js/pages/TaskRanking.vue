<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td class="col-md-2">用戶:</td>
          <td class="col-md-2">
            <select class="form-control" v-model="editData.userID">
              <option value=""></option>
              <option
                :value="item.value"
                v-for="(item, index) in options.subEmps"
                :key="index"
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
          <template v-if="isRoot">
            <td>顯示模式</td>
            <td>
              <select
                name="display_mode"
                class="form-control"
                v-model="editData.display_mode"
              >
                <option value="0">分機排序</option>
                <option value="1">用戶排序</option>
              </select>
            </td>
          </template>
          <td></td>
          <td :colspan="isRoot ? 3 : 5">
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

    <data-table
      v-if="display_mode"
      :datas="sortDatas"
      :startIndex="1"
      :columns="[
        { key: 'UserID', name: '帳號', sortable: true },
        {
          key: 'ExtensionNo',
          name: '分機號',
          sortable: true,
          show: showExtensionNo,
        },
        { key: 'UserName', name: '用戶名稱', sortable: true },
        { key: 'CallDuration', name: '時間', sortable: true },
        { key: 'Count', name: '通數', sortable: true },
        { key: 'BillValue', name: '費用', sortable: true },
        { key: 'BillCost', name: '成本', sortable: true, show: isRoot },
      ]"
      :sort="sort"
    >
      <template v-slot:ExtensionNo="{ data }">
        {{ data.ExtensionNo || "Wait" }}
      </template>

      <template v-slot:tfoot>
        <tr>
          <td colspan="2">合計</td>
          <td></td>
          <td v-if="showExtensionNo"></td>
          <td>{{ _.jSumBy(datas, "CallDuration") }}</td>
          <td>{{ _.jSumBy(datas, "Count") }}</td>
          <td>{{ _.jSumBy(datas, "BillValue") }}</td>
          <td v-if="isRoot">{{ _.jSumBy(datas, "BillCost") }}</td>
        </tr>
      </template>
    </data-table>
  </div>
</template>

<script>
import DateTimePicker from "../components/DateTimePicker.vue";
import OrderByMixins from "mixins/OrderBy";
import CommonMixins from "mixins/Common";
import DataTable from "../components/DataTable";

export default {
  mixins: [CommonMixins, OrderByMixins],
  components: { DateTimePicker, DataTable },
  data: () => ({
    options: {
      subEmps: [],
    },
    editData: {
      userID: "",
      callStartBillingDate: moment().startOf("month").format("YYYY/MM/DD"),
      callStopBillingDate: moment().subtract(1, "days").format("YYYY/MM/DD"),
      display_mode: "0",
    },
    display_mode: "",
    datas: [],
  }),
  methods: {
    async getOptions() {
      const res = await $.callApi.post("userInfo/getSubEmpOption");
      this.options.subEmps = res.data.option;
    },
    async getList() {
      const res = await $.callApi.post("api/taskReanking/list", this.editData);
      this.datas = res.data;
      this.display_mode = this.editData.display_mode;
    },
  },
  computed: {
    showExtensionNo() {
      return this.display_mode === "0";
    },
  },
  mounted() {
    this.getOptions();
  },
};
</script>