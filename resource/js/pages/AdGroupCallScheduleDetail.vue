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
            <td>使用金額</td>
            <td>
              {{ editData.TotalFee }}
            </td>
          </tr>
          <tr>
            <td>掃號名稱</td>
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
            <td>起始電話</td>
            <td>
              <div class="input-group">
                <input
                  readonly
                  type="text"
                  class="form-control"
                  :value="editData.StartCalledNumber"
                />
                <span class="input-group-addon">筆數</span>
                <input
                  readonly
                  type="text"
                  class="form-control"
                  :value="editData.CalledCount"
                />
              </div>
            </td>
          </tr>
          <tr>
            <td>撥出電話等待秒數</td>
            <td>
              <validate rules="required|min_value:5|max_value:300">
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control"
                    v-model="editData.CallProgressTime"
                  />
                  <span class="input-group-addon">(5~300)秒，等待接通時間</span>
                </div>
              </validate>
            </td>
          </tr>
          <tr>
            <td>自動撥號速度</td>
            <td>
              <select class="form-control" v-model="editData.ConcurrentCalls">
                <option
                  v-for="call in _.concat(_.range(1, 101), [
                    125,
                    150,
                    175,
                    200,
                  ])"
                  :key="call"
                  :value="call"
                >
                  每一秒{{ call }}通
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td>開始日期</td>
            <td>
              <date-time-picker
                type="datepicker"
                :date.sync="editData.StartDate"
              />
            </td>
          </tr>
          <tr>
            <td>開始時間1</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StartTime1"
              />
            </td>
          </tr>
          <tr>
            <td>開始時間2</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StartTime2"
              />
            </td>
          </tr>
          <tr>
            <td>開始時間3</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StartTime3"
              />
            </td>
          </tr>
          <tr>
            <td>結束日期</td>
            <td>
              <date-time-picker
                type="datepicker"
                :date.sync="editData.StopDate"
              />
            </td>
          </tr>
          <tr>
            <td>結束時間1</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StopTime1"
              />
            </td>
          </tr>
          <tr>
            <td>結束時間2</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StopTime2"
              />
            </td>
          </tr>
          <tr>
            <td>結束時間3</td>
            <td>
              <date-time-picker
                type="timepicker"
                :time.sync="editData.StopTime3"
              />
            </td>
          </tr>
          <tr>
            <td>語音檔名</td>
            <td>
              <select
                class="form-control"
                v-if="options.VoiceFiles.length"
                v-model="editData.FileName1"
              >
                <option value=""></option>
                <option
                  v-for="(voiceFile, index) in options.VoiceFiles"
                  :key="index"
                  :value="voiceFile"
                >
                  {{ voiceFile }}
                </option>
              </select>
              <span v-else>查無語音檔</span>
            </td>
          </tr>
          <tr>
            <td>結束語音檔名</td>
            <td>
              <select
                class="form-control"
                v-if="options.VoiceFiles.length"
                v-model="editData.FileName2"
              >
                <option value=""></option>
                <option
                  v-for="(voiceFile, index) in options.VoiceFiles"
                  :key="index"
                  :value="voiceFile"
                >
                  {{ voiceFile }}
                </option>
              </select>
              <span v-else>查無語音檔</span>
            </td>
          </tr>
          <tr>
            <td>失敗重撥次數</td>
            <td>
              <select class="form-control" v-model="editData.CallRetry">
                <option v-for="val in [0, 1, 2, 3]" :key="val" :value="val">
                  {{ val }}
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td>重撥間隔秒數</td>
            <td>
              <select class="form-control" v-model="editData.RetryTime">
                <option
                  v-for="val in [180, 300, 600, 1800]"
                  :key="val"
                  :value="val"
                >
                  {{ val }}
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td>停撥接通數</td>
            <td>
              <input
                type="text"
                class="form-control"
                v-model="editData.StopOnConCount"
              />
            </td>
          </tr>
          <tr>
            <td>啟用</td>
            <td>
              <Switcher v-model="editData.UseState" />
            </td>
          </tr>
          <tr>
            <td>紀錄按鍵</td>
            <td>
              <Switcher v-model="editData.WaitDTMF" />
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
import Switcher from "../components/Switcher";
import DateTimePicker from "../components/DateTimePicker";

const RANGE = "0";
const LIST = "1";

export default {
  mixins: [CommonMixins],
  components: { Switcher, DateTimePicker },
  data: () => ({
    editData: {
      User: {},
    },
    Const: {
      RANGE,
      LIST,
    },
    options: {
      VoiceFiles: [],
    },
  }),
  methods: {
    async getOptions() {
      const res = await $.callApi.post("api/adGroupCallSchedule/options");
      this.options.VoiceFiles = res.data;
    },
    async getData() {
      const res = await $.callApi.post("api/adGroupCallSchedule/detail", {
        UserID: this.userID,
        CallOutID: this.callOutID,
      });
      this.editData = res.data;
    },
    async doUpdate() {
      await $.callApi.post("api/adGroupCallSchedule/update", this.editData);
      redirect("adCallSetting/adCallSchedule");
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
    this.getOptions();
    this.getData();
  },
};
</script>