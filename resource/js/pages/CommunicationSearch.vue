<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td class="col-md-1">用戶</td>
          <td class="col-md-3">
            <select class="form-control" v-model="where.userId">
              <option
                v-for="(select, index) in options.empSelect"
                :key="index"
                :value="select.value"
              >
                {{ select.name }}
              </option>
            </select>
          </td>
          <td class="col-md-1">分機號碼</td>
          <td class="col-md-3">
            <input
              type="text"
              name="extensionNo"
              class="form-control num-only rigorous"
              v-model="where.extensionNo"
              @keyup.prevent.83="where.extensionNo = 'system'"
            />
          </td>
          <td class="col-md-1">撥打號碼</td>
          <td class="col-md-3">
            <input
              type="text"
              name="orgCalledId"
              class="form-control num-only rigorous"
              v-model="where.orgCalledId"
            />
          </td>
        </tr>
        <tr>
          <td>開始時間</td>
          <td>
            <date-time-picker
              :date.sync="where.callStartBillingDate"
              :time.sync="where.callStartBillingTime"
              type="datetimepicker"
            />
          </td>
          <td>結束時間</td>
          <td>
            <date-time-picker
              :date.sync="where.callStopBillingDate"
              :time.sync="where.callStopBillingTime"
              type="datetimepicker"
            />
          </td>
          <td>按鍵</td>
          <td>
            <input
              type="text"
              name="customerLevel"
              class="form-control"
              v-model="where.customerLevel"
            />
          </td>
        </tr>
        <tr>
          <td><span>秒數</span></td>
          <td>
            <div class="input-group">
              <input
                type="text"
                name="searchSec"
                class="form-control"
                v-model="where.searchSec"
              />
              <span class="input-group-addon">~</span>
              <input
                type="text"
                name="searchSec2"
                class="form-control"
                v-model="where.searchSec2"
              />
            </div>
          </td>
          <td>撥號類型</td>
          <td>
            <select class="form-control" v-model="where.callType">
              <option
                v-for="(select, index) in options.callTypeSelector"
                :key="index"
                :value="select.value"
              >
                {{ select.name }}
              </option>
            </select>
          </td>
          <td></td>
          <td>
            <input
              type="button"
              class="form-control btn btn-primary"
              @click="doSearch()"
              value="列表"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <div class="data-container">
      <paginate
        :page="paginate.page"
        :total="paginate.total"
        :per_page="paginate.per_page"
        @change="(page) => changePage(page)"
      />
      <button type="button" class="btn btn-danger" @click="doDelete()">
        Delete
      </button>
      <button type="button" class="btn btn-default" @click="exportCSV()">
        下載
      </button>
      <data-table
        allChecked
        :startIndex="startIndex"
        :datas="datas"
        :columns="[
          { key: 'UserID', name: '用戶', sortable: true },
          { key: 'ExtensionNo', name: '分機', sortable: true },
          { key: 'OrgCalledId', name: '撥打號碼', sortable: true },
          { key: 'CallStartBillingDate', name: '開始時間', sortable: true },
          { key: 'CallDuration', name: '時間', sortable: true },
          { key: 'BillValue', name: '費用', sortable: true },
          { key: 'CustomerLevel', name: '按鍵', sortable: true },
          { key: 'CallDisconnetString', name: '掛斷說明', show: isRoot },
          { key: 'RecordDownload', name: '錄音下載' },
        ]"
        :trStyle="
          (data) => ({
            'background-color': data.CallType == 0 ? '#dddddd' : '#ffbbbb',
          })
        "
        :sort="sort"
        @changeSort="(key) => apiChangeSort(key)"
      >
        <template v-slot:allChecked>
          <input type="checkbox" v-model="isAllChecked" />
        </template>
        <template v-slot:checked="{ data }">
          <input type="checkbox" v-model="data.checked" />
        </template>
        <template v-slot:CallStartBillingDate="{ data }">
          {{ data.CallStartBillingDate + " " + data.CallStartBillingTime }}
        </template>
        <template v-slot:RecordDownload="{ data }">
          <a
            v-if="data.RecordFile"
            :href="getVoiceUrl(data)"
            class="label label-info"
          >
            下載
          </a>
          <span v-else class="label label-default"> 下載 </span>
        </template>
        <template v-slot:tfoot>
          <tr>
            <td colspan="3">合計</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ totalData.totalTime }}</td>
            <td>{{ totalData.totalMoney }}</td>
            <td></td>
            <td v-if="isRoot"></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3">總合計</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ allData.totalTime }}</td>
            <td>{{ allData.totalMoney | point }}</td>
            <td></td>
            <td v-if="isRoot"></td>
            <td></td>
          </tr>
        </template>
      </data-table>
    </div>
  </div>
</template>

<script>
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import EmpMixins from "mixins/Emp";
import OrderBy from "mixins/OrderBy";
import DateTimePicker from "../components/DateTimePicker";
import LibraryMixins from "mixins/Library";

export default {
  mixins: [CommonMixins, ListMixins, EmpMixins, OrderBy, LibraryMixins],
  components: {
    DateTimePicker,
  },
  data: () => ({
    allData: {
      totalMoney: 0,
      totalTime: 0,
    },
    options: {
      callTypeSelector: [
        { value: "", name: "全部" },
        { value: "0", name: "自動撥號" },
        { value: "1", name: "手動撥號" },
      ],
    },
    where: {
      userId: "",
      extensionNo: "",
      orgCalledId: "",
      callStartBillingDate: moment().format("YYYY/MM/DD"),
      callStartBillingTime: "00:00:00",
      callStopBillingDate: moment().add(1, "days").format("YYYY/MM/DD"),
      callStopBillingTime: "00:00:00",
      customerLevel: "",
      searchSec: "",
      searchSec2: "",
      callType: "",
    },
    sort: {
      key: "CallStartBillingDate",
      type: "desc",
    },
  }),
  methods: {
    getVoiceUrl(data) {
      const params = new URLSearchParams();
      params.set("userId", data.UserID);
      params.set(
        "connectDate",
        moment(data.CallStartBillingDate).format("YYYYMMDD")
      );
      params.set("fileName", data.RecordFile);
      return apiUrl + "downloadFile/recordFile?" + params.toString();
    },
    async getTotal() {
      const res = await $.callApi.post(
        "api/communication/common",
        this.requestBody
      );
      this.allData.totalMoney = res.data.totalMoney;
      this.allData.totalTime = res.data.totalTime;
      this.paginate.total = res.data.rows;
    },
    async getList() {
      $("body").loading();
      const res = await $.callApi.post(
        "api/communication/list",
        this.requestBody
      );
      this.datas = res.data.map((x) => ({ ...x, checked: false }));
      $("body").loading("stop");
    },
    doDelete() {
      $.callApi
        .post("api/communication/delete", {
          id: this.datas.filter((x) => x.checked).map((x) => x.LogID),
        })
        .then(() => {
          this.getList();
          alertify.alert("刪除成功!");
        })
        .catch(() => {
          alertify.alert("刪除失敗!");
        });
    },
    apiChangeSort(key) {
      this.changeSort(key);
      this.getList();
    },
    async exportCSV() {
      $("body").loading();
      const res = await $.callApi.post("api/communication/list", {
        ...this.requestBody,
        page: 0,
      });
      this.fileFunc.exportCSV(
        this.fileFunc.buildCSVContext(
          res.data,
          [
            { key: "UserID", name: "用戶" },
            { key: "ExtensionNo", name: "分機" },
            { key: "OrgCalledId", name: "撥打號碼" },
            { key: "CallStartBillingDate", name: "開始時間" },
            { key: "CallDuration", name: "時間" },
            { key: "BillValue", name: "費用" },
            { key: "CustomerLevel", name: "按鍵" },
          ].concat(
            isRoot ? { key: "CallDisconnetString", name: "掛斷說明" } : {}
          )
        ),
        "通聯紀錄.csv"
      );
      $("body").loading("stop");
    },
  },
  computed: {
    totalData() {
      return {
        totalMoney: _.jSumBy(this.datas, "BillValue"),
        totalTime: _.jSumBy(this.datas, "CallDuration"),
      };
    },
    requestBody() {
      return {
        ...this.paginate,
        ...this.where,
        sortKey: this.sort.key,
        sortType: this.sort.type,
      };
    },
  },
  mounted() {
    this.getEmpSelect();
    $(this.$refs.del_btn).confirm({
      text: "刪除確認",
      confirm(button) {
        this.doDelete();
      },
      post: true,
      confirmButton: "確定",
      cancelButton: "取消",
      confirmButtonClass: "btn-danger",
      cancelButtonClass: "btn-default",
    });
  },
};
</script>