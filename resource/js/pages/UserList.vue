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
    <table class="table table-h table-pointer table-striped table-hover">
      <tbody>
        <tr>
          <th>
            <input type="checkbox" class="checkAll" v-model="isAllChecked" />
          </th>
          <th
            width="160"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'UserID' && sort.type == 'asc',
              sorting_desc: sort.key == 'UserID' && sort.type == 'desc',
            }"
            @click="changeSort('UserID')"
          >
            登入帳號(編輯)
          </th>
          <th
            width="80"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'UseState' && sort.type == 'asc',
              sorting_desc: sort.key == 'UseState' && sort.type == 'desc',
            }"
            @click="changeSort('UseState')"
          >
            狀態
          </th>
          <th
            width="120"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'Distributor' && sort.type == 'asc',
              sorting_desc: sort.key == 'Distributor' && sort.type == 'desc',
            }"
            @click="changeSort('Distributor')"
          >
            經銷商
          </th>
          <th
            width="110"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'ExtensionCount' && sort.type == 'asc',
              sorting_desc: sort.key == 'ExtensionCount' && sort.type == 'desc',
            }"
            @click="changeSort('ExtensionCount')"
          >
            分機數
          </th>
          <th
            width="100"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'RateGroupID' && sort.type == 'asc',
              sorting_desc: sort.key == 'RateGroupID' && sort.type == 'desc',
            }"
            @click="changeSort('RateGroupID')"
          >
            費率
          </th>
          <th
            width="120"
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'Balance' && sort.type == 'asc',
              sorting_desc: sort.key == 'Balance' && sort.type == 'desc',
            }"
            @click="changeSort('Balance')"
          >
            剩餘點數
          </th>
          <th
            class="sorting"
            :class="{
              sorting_asc: sort.key == 'UserName' && sort.type == 'asc',
              sorting_desc: sort.key == 'UserName' && sort.type == 'desc',
            }"
            @click="changeSort('UserName')"
          >
            用戶名稱
          </th>
          <th>用戶備註</th>
          <th width="100">操作</th>
        </tr>
        <tr v-for="(data, index) in sortDatas" :key="index">
          <td>
            <input
              type="checkbox"
              v-model="data.checked"
              v-if="choice != data.UserID"
            />
          </td>
          <td>{{ data.UserID }}</td>
          <td>
            <label class="switch">
              <input
                id="switch"
                disabled
                type="checkbox"
                :checked="data.UseState"
              />
              <div class="slider round"></div>
            </label>
          </td>
          <td>{{ data.Distributor }}</td>
          <td>{{ data.ExtensionCount }}</td>
          <td>{{ data.RateGroupID }}</td>
          <td>{{ parseFloat(data.Balance).toFixed(2) }}</td>
          <td>{{ data.UserName }}</td>
          <td>{{ data.NoteText }}</td>
          <td>
            <button
              type="button"
              class="btn btn-info"
              @click="toDetail(data.UserID)"
            >
              編輯
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import OrderByMixins from "mixins/OrderBy";

export default {
  mixins: [OrderByMixins],
  data: () => ({
    choice,
    datas: [],
  }),
  methods: {
    async getList() {
      const res = await $.callApi.post("user/list");
      this.datas = res.data.map((x) => ((x.checked = false), x));
    },
    toDetail(UserID) {
      redirect(`userInfo/userDetail${UserID ? "?userId=" + UserID : ""}`);
    },
    async doDelete() {
      await $.callApi.post("user/delete", {
        deleteUserID: this.deleteUserID,
      });
      alertify.alert("已成功刪除!");
      this.getList();
    },
  },
  computed: {
    isAllChecked: {
      get() {
        return this.datas.every((x) => x.checked);
      },
      set(val) {
        this.datas.forEach((x) => (x.checked = val));
      },
    },
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