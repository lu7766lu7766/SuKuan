<template>
  <div class="table-responsive">
    <input
      type="button"
      class="btn btn-danger"
      value="Delete"
      @click="doDelete()"
    />
    <a class="btn btn-primary" style="color: white" @click="toDetail()"
      >新增
    </a>
    <button type="button" class="btn btn-success" @click="exportCSV()">
      下載
    </button>

    <button
      type="button"
      class="btn btn-warning"
      v-if="isRoot"
      @click="upload()"
    >
      上傳
    </button>

    <data-table
      allChecked
      :datas="sortDatas"
      :columns="[
        { key: 'UserID', name: '登入帳號(編輯)', sortable: true, width: 160 },
        { key: 'UseState', name: '狀態', sortable: true, width: 80 },
        { key: 'Distributor', name: '經銷商', sortable: true, width: 120 },
        { key: 'ExtensionCount', name: '分機數', sortable: true, width: 110 },
        { key: 'RateGroupID', name: '費率', sortable: true, width: 100 },
        { key: 'Balance', name: '剩餘點數', sortable: true, width: 120 },
        { key: 'UserName', name: '用戶名稱', sortable: true },
        { key: 'NoteText', name: '用戶備註' },
        { key: 'action', name: '操作', width: 100 },
      ]"
      :sort="sort"
      @changeSort="(e) => changeSort(e)"
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
      <template v-slot:UseState="{ data }">
        <label class="switch">
          <input
            id="switch"
            disabled
            type="checkbox"
            true-value="1"
            false-value="0"
            v-model="data.UseState"
          />
          <div class="slider round"></div>
        </label>
      </template>
      <template v-slot:action="{ data }">
        <button
          type="button"
          class="btn btn-info"
          @click="toDetail(data.UserID)"
        >
          編輯
        </button>
      </template>
    </data-table>
  </div>
</template>
<script>
import OrderByMixins from "mixins/OrderBy";
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import LibraryMixins from "mixins/Library";

export default {
  mixins: [CommonMixins, OrderByMixins, ListMixins, LibraryMixins],
  methods: {
    async getList() {
      const res = await $.callApi.post("user/list");
      const checkedUserID = this.datas
        .filter((x) => x.checked)
        .map((x) => x.UserID);
      this.datas = res.data.map((x) => {
        x.Balance = parseFloat(x.Balance || 0).toFixed(2);
        x.checked = checkedUserID.includes(x.UserID);
        return x;
      });
    },
    toDetail(UserID) {
      redirect(`userInfo/userDetail${UserID ? "?userID=" + UserID : ""}`);
    },
    async doDelete() {
      await $.callApi.post("user/delete", {
        deleteUserID: this.deleteUserID,
      });
      alertify.alert("已成功刪除!");
      this.getList();
    },
    exportCSV() {
      this.fileFunc.exportCSV(
        this.fileFunc.buildCSVContext(this.sortDatas, [
          { key: "UserID", name: "帳號" },
          { key: "UseState", name: "狀態" },
          { key: "Distributor", name: "經銷商" },
          { key: "ExtensionCount", name: "分機數" },
          { key: "RateGroupID", name: "費率" },
          { key: "Balance", name: "剩餘點數" },
          { key: "UserName", name: "用戶名稱" },
          { key: "NoteText", name: "用戶備註" },
        ]),
        "用戶列表.csv"
      );
    },
    async upload() {
      const file = await this.$upload({
        accept: ".csv",
      });
      const text = await this.fileFunc.toText(file);
      const datas = this.fileFunc.toDatas(text, [
        "UserID",
        "UseState",
        "Distributor",
        "ExtensionCount",
        "RateGroupID",
        "Balance",
        "UserName",
        "NoteText",
      ]);
      await $.callApi.post("user/create/batch", { datas });
      alertify.alert("已成功新增!");
      await $.updateSession();
      location.reload();
    },
  },
  computed: {
    deleteUserID() {
      return this.datas.filter((x) => x.checked).map((x) => x.UserID);
    },
  },
  mounted() {
    const TEN_SECS_TO_UPDATE = 10 * 1000;
    this.getList();
    const timer = setInterval(this.getList, TEN_SECS_TO_UPDATE);
    this.$once("hook:destroyed", () => clearInterval(timer));
  },
};
</script>