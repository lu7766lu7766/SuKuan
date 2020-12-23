<template>
  <div class="table-responsive">
    <validation v-slot="{ invalid }">
      <table class="table table-v">
        <tbody>
          <tr>
            <td class="col-md-3">網頁登入帳號</td>
            <td class="col-md-9">{{ editData.User.UserID }}</td>
          </tr>
          <tr>
            <td>用戶名稱</td>
            <td>{{ editData.User.UserName }}</td>
          </tr>
          <tr>
            <td>剩餘點數</td>
            <td>{{ editData.User.Balance }}</td>
          </tr>
          <tr>
            <td>群呼名稱</td>
            <td>
              <input
                type="text"
                name="planName"
                class="form-control"
                v-model="editData.PlanName"
              />
            </td>
          </tr>
          <tr>
            <td>新增模式</td>
            <td>
              <span v-if="editData.NumberMode == Const.RANGE">區段增加</span>
              <span v-else-if="editData.NumberMode == Const.LIST"
                >名單上傳</span
              >
              <span v-else-if="editData.NumberMode == Const.SAME"
                >同號測試</span
              >
              <span v-else-if="editData.NumberMode == Const.VALID"
                >有效號新增</span
              >
            </td>
          </tr>
          <tr>
            <td>顯示主叫</td>
            <td>
              <select class="form-control" v-model="editData.CallerPresent">
                <option value="0">不顯示</option>
                <option value="1" selected>顯示</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>主叫號</td>
            <td>
              <input
                type="text"
                name="callerID"
                class="form-control"
                v-model="editData.CallerID"
              />
            </td>
          </tr>
          <tr>
            <td>座席</td>
            <td>
              <select class="form-control" v-model="editData.CalloutGroupID">
                <option v-for="item in _.range(1, 5)" :key="item" :value="item">
                  {{ item }}
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td>響鈴方式</td>
            <td>
              <select class="form-control" v-model="editData.Calldistribution">
                <option value="0">分號少的開始配號</option>
                <option value="1" selected>自動平均分配</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>撥出電話等待秒數</td>
            <td>
              <validate rules="min_value:5|max_value:300">
                <div class="input-group">
                  <input
                    type="text"
                    name="callProgressTime"
                    class="form-control num-only"
                    v-model="editData.CallProgressTime"
                  />
                  <span class="input-group-addon">(5~300)秒，等待接通時間</span>
                </div>
              </validate>
            </td>
          </tr>
          <tr>
            <td>轉分機等待秒數</td>
            <td>
              <input
                type="text"
                name="extProgressTime"
                class="form-control num-only"
                v-model="editData.ExtProgressTime"
              />
            </td>
          </tr>
          <tr>
            <td>啟用</td>
            <td>
              <label class="switch">
                <input
                  id="switch"
                  type="checkbox"
                  :checked="editData.UseState"
                />
                <div class="slider round"></div>
              </label>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <button
                class="btn btn-primary form-control"
                type="button"
                :disabled="invalid"
                @click="doUpdate()"
              >
                修改
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </validation>
  </div>
</template>

<script>
import CommonMixins from "mixins/Common";

const RANGE = "0";
const LIST = "1";
const SAME = "2";
const VALID = "3";

export default {
  mixins: [CommonMixins],
  data: () => ({
    editData: {
      User: {},
    },
    Const: {
      RANGE,
      LIST,
      SAME,
      VALID,
    },
  }),
  methods: {
    async getData() {
      const res = await $.callApi.post("api/groupCallSchedule/detail", {
        userID: this.userID,
        callOutID: this.callOutID,
      });
      this.editData = res.data;
    },
    async doUpdate() {
      await $.callApi.post("api/groupCallSchedule/update", this.editData);
      redirect("groupCallSetting/groupCallSchedule");
    },
  },
  computed: {
    url() {
      return new URLSearchParams(location.search);
    },
    userID() {
      return this.url.get("UserID");
    },
    callOutID() {
      return this.url.get("CallOutID");
    },
  },
  mounted() {
    this.getData();
  },
};
</script>