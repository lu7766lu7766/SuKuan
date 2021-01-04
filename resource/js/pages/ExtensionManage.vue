<template>
  <div class="table-responsive">
    <table class="table table-v table-condensed">
      <tbody>
        <tr>
          <td class="col-md-2">用戶:</td>
          <td class="col-md-4">
            <select class="form-control" v-model="editData.UserID">
              <option value=""></option>
              <option
                v-for="(item, index) in options.empSelect"
                :key="index"
                :value="item.value"
              >
                {{ item.name }}
              </option>
            </select>
          </td>
          <td class="col-md-2">查詢內容:</td>
          <td class="col-md-3">
            <input
              type="text"
              name="search_content"
              class="form-control num-only"
              v-model="editData.SearchContent"
            />
          </td>
          <td class="non col-md-1">
            <input
              type="button"
              class="form-control btn btn-primary"
              value="列表"
              @click="doSearch()"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <paginate
      :page="paginate.page"
      :total="paginate.total"
      :per_page="paginate.per_page"
      @change="(page) => changePage(page)"
    />

    <input
      type="button"
      class="btn btn-danger"
      value="Delete"
      v-if="isRoot"
      @click="doDelete()"
    />

    <data-table
      allChecked
      :startIndex="startIndex"
      :datas="datas"
      :columns="[
        { key: 'UserID', name: '編號' },
        { key: 'ExtensionNo', name: '分機' },
        { key: 'ExtName', name: '名稱' },
        { key: 'OffNetCli', name: '手動撥號顯號' },
        { key: 'Received', name: '來源IP' },
        { key: 'StartRecorder', name: '錄音' },
        { key: 'CalloutGroupID', name: '座席' },
        { key: 'Suspend', name: '狀態' },
        { key: 'ETime', name: '註冊' },
        { key: 'UseState', name: '啟用' },
        { key: 'PingTime', name: 'Ping' },
        { key: 'action', name: '操作' },
      ]"
    >
      <template v-slot:allChecked>
        <input type="checkbox" class="checkAll" v-model="isAllChecked" />
      </template>
      <template v-slot:checked="{ data }">
        <input type="checkbox" v-model="data.checked" />
      </template>
      <template v-slot:Received="{ data }">
        {{ data.Received || data.HostInfo }}
      </template>
      <template v-slot:StartRecorder="{ data }">
        <Switcher disabled :value="data.StartRecorder" />
      </template>
      <template v-slot:Suspend="{ data }">
        <Switcher disabled on="0" off="1" :value="data.Suspend" />
      </template>
      <template v-slot:ETime="{ data }">
        <Switcher
          disabled
          :on="true"
          :value="moment(data.ETime).isAfter(moment()).toString()"
        />
      </template>
      <template v-slot:UseState="{ data }">
        <Switcher v-if="canSwitchExtension" disabled :value="data.UseState" />
        <span v-else></span>
      </template>
      <template v-slot:action="{ data }">
        <button type="button" class="btn btn-info" @click="toEdit(data)">
          編輯
        </button>
      </template>
    </data-table>
  </div>
</template>

<script>
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import EmpMixins from "mixins/Emp";

export default {
  mixins: [CommonMixins, ListMixins, EmpMixins],
  watch: {
    requestBody: {
      deep: true,
      handler() {
        this.$router.push({
          query: this.requestBody,
        });
      },
    },
  },
  methods: {
    async getList() {
      const res = await $.callApi.post(
        "api/extensionManage/list",
        this.requestBody
      );
      this.datas = res.data.map((x) => ({ ...x, checked: false }));
    },
    async getTotal() {
      const res = await $.callApi.post(
        "api/extensionManage/total",
        this.requestBody
      );
      this.paginate.total = res.data;
    },
    async doDelete() {
      await this.$confirm();
      await $.callApi.post("api/extensionManage/delete", {
        datas: this.datas
          .filter((x) => x.checked)
          .map((x) => _.pick(x, ["UserID", "ExtensionNo"])),
      });
      this.getList();
    },
    toEdit(data) {
      redirect(
        `extensionInfo/extensionModify?UserID=${data.UserID}&ExtensionNo=${data.ExtensionNo}`
      );
    },
  },
  computed: {
    requestBody() {
      return {
        ...this.editData,
        ...this.paginate,
      };
    },
    canSwitchExtension() {
      return this.isRoot || +this.options.choicer.CanSwitchExtension;
    },
  },
  mounted() {
    this.editData = _.pick(this.$route.query, ["UserID", "SearchContent"]);
    this.getChoicer();
    this.getEmpSelect();
    this.doSearch();
  },
};
</script>