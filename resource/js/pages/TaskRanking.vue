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
    <table class="table table-h table-striped table-hover" v-if="display_mode">
      <tbody>
        <tr>
          <th>編號</th>
          <th>帳號</th>
          <th v-if="showExtensionNo">分機號</th>
          <th>用戶名稱</th>
          <th>時間</th>
          <th>通數</th>
          <th>費用</th>
          <th v-if="isRoot">成本</th>
        </tr>
        <tr v-for="(data, index) in datas" :key="index">
          <td>{{ index + 1 }}</td>
          <td>{{ data.UserID }}</td>
          <td v-if="showExtensionNo">{{ data.ExtensionNo || "Wait" }}</td>
          <td>{{ data.UserName }}</td>
          <td>{{ data.CallDuration }}</td>
          <td>{{ data.Count }}</td>
          <td>{{ data.BillValue }}</td>
          <td v-if="isRoot">{{ data.BillCost }}</td>
        </tr>
        <tr>
          <td colspan="2">合計</td>
          <td></td>
          <td v-if="showExtensionNo"></td>
          <td>{{ _.jSumBy(datas, 'CallDuration') }}</td>
          <td>{{ _.jSumBy(datas, 'Count') }}</td>
          <td>{{ _.jSumBy(datas, 'BillValue') }}</td>
          <td v-if="isRoot">{{ _.jSumBy(datas, 'BillCost') }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import DateTimePicker from "../components/DateTimePicker.vue";

export default {
  components: { DateTimePicker },
  data: () => ({
    choice,
    isRoot,
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
      const res = await $.callApi.post(
        "communicationHistory/getTaskRankingList",
        this.editData
      );
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