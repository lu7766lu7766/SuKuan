<template>
  <table class="table table-v">
    <tbody>
      <tr>
        <td class="col-md-3 col-xs-5">登入帳號</td>
        <td class="col-md-9 col-xs-7">
          <span v-if="isUpdate">{{ userID }}</span>
          <input
            type="text"
            v-else
            v-model="data.UserID"
            class="form-control"
          />
        </td>
      </tr>
      <tr>
        <td class="col-md-3 col-xs-5">登入帳號2</td>
        <td class="col-md-9 col-xs-7">
          <input
            type="text"
            name="userID2"
            class="form-control"
            v-model="data.UserID2"
            placeholder="僅登入使用"
          />
        </td>
      </tr>
      <tr>
        <td>密碼</td>
        <td>
          {{ data.UserPassword }}
        </td>
      </tr>
      <tr>
        <td>帳號狀態</td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="data.UseState" />
            <div class="slider round"></div>
          </label>
        </td>
      </tr>
      <tr>
        <td>用戶名稱</td>
        <td>
          <input
            type="text"
            name="userName"
            class="form-control"
            v-model="data.UserName"
          />
        </td>
      </tr>
      <tr>
        <td>用戶備註</td>
        <td>
          <input
            type="text"
            name="noteText"
            class="form-control"
            v-model="data.NoteText"
          />
        </td>
      </tr>
      <tr>
        <td>用戶資訊</td>
        <td>
          <input
            type="text"
            name="userInfo"
            class="form-control"
            v-model="data.UserInfo"
          />
        </td>
      </tr>
      <tr v-if="isRoot">
        <td>經銷商</td>
        <td>
          <input
            type="text"
            name="distributor"
            class="form-control"
            v-model="data.Distributor"
          />
        </td>
      </tr>
      <tr>
        <td>透支額度</td>
        <td>
          <input
            type="text"
            name="overdraft"
            class="form-control"
            v-model="data.Overdraft"
          />
        </td>
      </tr>
      <tr>
        <td>費率表</td>
        <td>
          <span v-if="!isRoot">{{ data.RateGroupID }}</span>
          <select v-else class="form-control" v-model="data.RateGroupID">
            <option :value="0"></option>
            <option
              v-for="(item, index) in options.rateGroups"
              :key="index"
              :value="item.RateGroupID"
            >
              {{ item.RateGroupName }}
            </option>
          </select>
        </td>
      </tr>
      <tr>
        <td>剩餘點數</td>
        <td>
          <div class="form-inline">
            <div class="form-group">
              <span>現有</span><span>{{ data.Balance || 0 }}</span
              ><span>點</span>
            </div>
          </div>
          <div class="input-group">
            <span class="input-group-addon">新增</span>
            <input
              type="text"
              name="balance"
              class="form-control num-only"
              v-model="data.AddBalance"
            />
            <span class="input-group-addon">點</span>
          </div>
        </td>
      </tr>
      <tr>
        <td>*每日可使用時間</td>
        <td>
          <date-time-picker type="timepicker" :time.sync="data.StartTime" />
        </td>
      </tr>
      <tr>
        <td>*每日自動啟用使用時間</td>
        <td>
          <date-time-picker type="timepicker" :time.sync="data.AutoStartTime" />
        </td>
      </tr>
      <tr>
        <td>*每日自動關閉時間</td>
        <td>
          <date-time-picker
            type="timepicker"
            :time.sync="data.StoAutoStopTimepTime"
          />
        </td>
      </tr>
      <tr>
        <td>*每日結束時間</td>
        <td>
          <date-time-picker type="timepicker" :time.sync="data.StopTime" />
        </td>
      </tr>
      <tr>
        <td>[ 自動 ]停止秒數</td>
        <td>
          <div class="form-inline">
            <input
              type="text"
              name="callWaitingTime"
              class="form-control num-only"
              v-model="data.CallWaitingTime"
            />
            <span>(0 ~ 300) 秒，自動撥號在一段座席滿線後的暫停秒數</span>
          </div>
        </td>
      </tr>
      <tr>
        <td>*掃號每日可使用時間</td>
        <td>
          <date-time-picker
            type="timepicker"
            :time.sync="data.SearchStartTime"
          />
        </td>
      </tr>
      <tr>
        <td>*掃號每日自動啟用使用時間</td>
        <td>
          <date-time-picker
            type="timepicker"
            :time.sync="data.SearchAutoStartTime"
          />
        </td>
      </tr>
      <tr>
        <td>*掃號每日自動關閉時間</td>
        <td>
          <date-time-picker
            type="timepicker"
            :time.sync="data.SearchAutoStopTime"
          />
        </td>
      </tr>
      <tr>
        <td>*掃號每日結束時間</td>
        <td>
          <date-time-picker
            type="timepicker"
            :time.sync="data.SearchStopTime"
          />
        </td>
      </tr>
      <tr>
        <td>總路由線路數</td>
        <td>
          <input
            type="text"
            class="form-control"
            v-model.lazy="data.MaxRoutingCalls"
          />
        </td>
      </tr>
      <tr>
        <td>掃號路由線數</td>
        <td>
          <input
            type="text"
            class="form-control"
            v-model.lazy="data.MaxSearchCalls"
          />
        </td>
      </tr>
      <tr>
        <td>節費路由線數</td>
        <td>
          <input
            type="text"
            class="form-control"
            v-model.lazy="data.MaxRegularCalls"
          />
        </td>
      </tr>
      <tr>
        <td>自動撥號路由線數</td>
        <td>
          <input
            type="text"
            class="form-control"
            v-model.lazy="data.MaxCalls"
          />
        </td>
      </tr>
      <tr>
        <td>直屬上司</td>
        <td>
          <select class="form-control" v-model="data.ParentID">
            <option
              v-for="(item, index) in options.parents"
              :key="index"
              :value="item.value"
            >
              {{ item.name }}
            </option>
          </select>
        </td>
      </tr>

      <tr v-if="isRoot">
        <td>分機管理權限</td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="data.CanSwitchExtension" />
            <div class="slider round"></div>
          </label>
        </td>
      </tr>

      <tr v-if="isRoot">
        <td>權限設定</td>
        <td>
          <label class="switch">
            <input type="checkbox" v-model="data.PermissionControl" />
            <div class="slider round"></div>
          </label>
        </td>
      </tr>

      <tr v-if="isRoot || (!isSelf && hasPermission)">
        <td>選單設定</td>
        <td>
          <ul style="display: inline-block">
            <li
              style="float: left; margin: 5px 15px 5px 10px"
              v-for="(item, index) in options.menus"
              :key="index"
            >
              <label>
                <input
                  type="checkbox"
                  style="margin-right: 0.3rem"
                  :value="item.value"
                  v-model="data.MenuList"
                />{{ item.name }}
              </label>
            </li>
          </ul>
        </td>
      </tr>

      <tr>
        <td></td>
        <td>
          <!-- 一般用戶不可以編輯自己，不可以新增 -->
          <input
            :disabled="!(isRoot || (isUpdate && isSelf))"
            class="btn btn-primary form-control"
            type="button"
            :value="isUpdate ? '更新' : '新增'"
            @click="isUpdate ? doUpdate() : doCreate()"
          />
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import DateTimePicker from "../components/DateTimePicker";
import CommonMixins from "mixins/Common";

export default {
  mixins: [CommonMixins],
  components: {
    DateTimePicker,
  },
  data: () => ({
    data: {},
    options: {
      parents: [],
      rateGroups: [],
      menus: [],
    },
  }),
  watch: {
    isAutoStartError(val) {
      if (val) {
        alertify.alert("自動啟動時間不得早於可用時間");
        this.data.AutoStartTime = this.data.StartTime;
      }
    },
    isAutoStopError(val) {
      if (val) {
        alertify.alert("自動停止時間不得晚於停用時間");
        this.data.AutoStopTime = this.data.StopTime;
      }
    },
    "data.MaxRoutingCalls"(newVal, oldVal) {
      if (this.isCallsRuleError) {
        this.data.MaxRoutingCalls = oldVal;
        this.callRuleAlert();
      }
    },
    "data.MaxSearchCalls"(newVal, oldVal) {
      if (this.isCallsRuleError) {
        this.data.MaxSearchCalls = oldVal;
        this.callRuleAlert();
      }
    },
    "data.MaxRegularCalls"(newVal, oldVal) {
      if (this.isCallsRuleError) {
        this.data.MaxRegularCalls = oldVal;
        this.callRuleAlert();
      }
    },
    "data.MaxCalls"(newVal, oldVal) {
      if (this.isCallsRuleError) {
        this.data.MaxCalls = oldVal;
        this.callRuleAlert();
      }
    },
  },
  methods: {
    async getOptions() {
      const res = await Promise.all([
        $.callApi.post("userInfo/parentOptions", {
          userID: this.userID,
        }),
        $.callApi.post("rate/list"),
        $.callApi.post("user/menus"),
      ]);
      const { 0: parents, 1: rateGroups, 2: menus } = res;
      this.options.parents = parents.data;
      this.options.rateGroups = rateGroups.data;
      this.options.menus = menus.data;
    },
    async getData() {
      const res = await $.callApi.post("user/detail", {
        userID: this.userID,
      });
      this.data = res.data;
    },
    async doCreate() {
      await $.callApi.post("user/create", this.editData);
      redirect("userInfo/userList");
    },
    async doUpdate() {
      await $.callApi.post("user/update", this.editData);
      redirect("userInfo/userList");
    },
    callRuleAlert() {
      alert(
        "總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號路由線數"
      );
    },
  },
  computed: {
    userID() {
      const url = new URLSearchParams(location.search);
      return url.get("userId");
    },
    isUpdate() {
      return !!this.userID;
    },
    isCreate() {
      return !this.userID;
    },
    isSelf() {
      return this.choice == this.userID;
    },
    hasPermission() {
      return !!permission;
    },
    isAutoStartError() {
      return (
        this.data.AutoStartTime &&
        this.data.StartTime &&
        this.data.AutoStartTime > this.data.StartTime
      );
    },
    isAutoStopError() {
      return (
        this.data.AutoStopTime &&
        this.data.StopTime &&
        this.data.AutoStopTime < this.data.StopTime
      );
    },
    isCallsRuleError() {
      return (
        this.data.MaxRoutingCalls <
        this.data.MaxSearchCalls + this.data.MaxRegularCalls + this.MaxCalls
      );
    },
  },
  mounted() {
    this.getOptions();
    this.isUpdate && this.getData();
  },
};
</script>