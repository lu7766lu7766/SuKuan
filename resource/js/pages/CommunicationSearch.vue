<template>
  <div class="table-responsive" v-cloak id="container">
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
          <td class="col-md-1">目的端號碼</td>
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
          <td>等級</td>
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
        @change="(page) => pageChange(page)"
      />
      <table class="table table-h table-striped table-hover">
        <tbody>
          <tr>
            <th>
              <input type="checkbox" v-model="isAllChecked" />
            </th>
            <th>編號</th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'UserID' && sort.type == 'asc',
                sorting_desc: sort.key == 'UserID' && sort.type == 'desc',
              }"
              @click="apiChangeSort('UserID')"
            >
              用戶
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'ExtensionNo' && sort.type == 'asc',
                sorting_desc: sort.key == 'ExtensionNo' && sort.type == 'desc',
              }"
              @click="apiChangeSort('ExtensionNo')"
            >
              分機
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'OrgCalledId' && sort.type == 'asc',
                sorting_desc: sort.key == 'OrgCalledId' && sort.type == 'desc',
              }"
              @click="apiChangeSort('OrgCalledId')"
            >
              目的端號碼
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc:
                  sort.key == 'CallStartBillingDate' && sort.type == 'asc',
                sorting_desc:
                  sort.key == 'CallStartBillingDate' && sort.type == 'desc',
              }"
              @click="apiChangeSort('CallStartBillingDate')"
            >
              開始時間
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'CallDuration' && sort.type == 'asc',
                sorting_desc: sort.key == 'CallDuration' && sort.type == 'desc',
              }"
              @click="apiChangeSort('CallDuration')"
            >
              時間
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'BillValue' && sort.type == 'asc',
                sorting_desc: sort.key == 'BillValue' && sort.type == 'desc',
              }"
              @click="apiChangeSort('BillValue')"
            >
              費用
            </th>
            <th
              class="sorting"
              :class="{
                sorting_asc: sort.key == 'CustomerLevel' && sort.type == 'asc',
                sorting_desc:
                  sort.key == 'CustomerLevel' && sort.type == 'desc',
              }"
              @click="apiChangeSort('CustomerLevel')"
            >
              等級
            </th>
            <th>錄音下載</th>
          </tr>
          <tr
            v-for="(data, index) in datas"
            :key="index"
            :style="{
              'background-color': data.CallType == 0 ? '#dddddd' : '#ffbbbb',
            }"
          >
            <td>
              <input type="checkbox" v-model="data.checked" />
            </td>
            <td>{{ startIndex + index }}</td>
            <td>{{ data.UserID }}</td>
            <td>{{ data.ExtensionNo }}</td>
            <td>{{ data.OrgCalledId }}</td>
            <td>
              {{ data.CallStartBillingDate + " " + data.CallStartBillingTime }}
            </td>
            <td>{{ data.CallDuration }}</td>
            <td>{{ data.BillValue }}</td>
            <td>{{ data.CustomerLevel }}</td>
            <td>
              <a
                :href="getVoiceUrl(data)"
                :target="data.RecordFile ? '_blank' : ''"
                :class="[
                  'label',
                  {
                    [data.RecordFile ? 'label-info' : 'label-default']: true,
                  },
                ]"
                >下載</a
              >
            </td>
          </tr>
          <tr>
            <td colspan="3">合計</td>
            <!--                    <td></td>-->
            <td></td>
            <td></td>
            <td></td>
            <td>{{ totalData.totalTime }}</td>
            <td>{{ totalData.totalMoney }}</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3">總合計</td>
            <!--                    <td></td>-->
            <td></td>
            <td></td>
            <td></td>
            <td>{{ allData.totalTime }}</td>
            <td>{{ allData.totalMoney }}</td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import CommonMixins from "mixins/Common";
import PaginateMixins from "mixins/Paginate";
import ListMixins from "mixins/List";
import EmpMixins from "mixins/Emp";
import OrderBy from "mixins/OrderBy";
import DateTimePicker from "../components/DateTimePicker";

export default {
  mixins: [CommonMixins, PaginateMixins, ListMixins, EmpMixins, OrderBy],
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
    },
  }),
  methods: {
    getVoiceUrl(data) {
      return data.RecordFile
        ? downloaderUrl +
            "downloadFile/recordFile?userId=" +
            data.UserID +
            "&connectDate=" +
            moment(data.CallStartBillingDate).format("YYYYMMDD") +
            "&fileName=" +
            data.RecordFile
        : "#";
    },
    openDownloadWindow() {
      window.open(ctrl_uri + "communicationSearchDownload");
    },
    async getCommonData() {
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
    doSearch() {
      this.pageChange(1);
      this.getCommonData();
    },
    doDelete() {
      $.callApi
        .post("api/communication/common", {
          id: this.datas.filter((x) => x.checked).map((x) => x.LogID),
        })
        .then(() => {
          this.doSearch();
          alertify.alert("刪除成功!");
        })
        .catch(() => {
          alertify.alert("刪除失敗!");
        });
    },
    pageChange(page) {
      this.paginate.page = page;
      this.getList();
    },
    apiChangeSort(key) {
      this.changeSort(key);
      this.getList();
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