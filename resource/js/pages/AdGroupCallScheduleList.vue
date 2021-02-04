<template>
  <section>
    <div class="col-md-12 form-inline">
      <label class="col-md-2">
        <input
          type="radio"
          v-model="editData.NumberMode"
          class="radio"
          :value="Const.LIST"
        />名單上傳</label
      >
      <label class="col-md-2">
        <input
          type="radio"
          v-model="editData.NumberMode"
          class="radio"
          :value="Const.RANGE"
        />區段增加</label
      >
    </div>

    <div class="table-responsive col-md-12">
      <validation v-slot="{ invalid }">
        <table class="table table-v">
          <tbody>
            <tr>
              <td class="col-md-2">廣告名稱</td>
              <td class="col-md-2">
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.PlanName"
                />
              </td>
              <template v-if="['0'].includes(editData.NumberMode)">
                <td class="col-md-2 type1">*起始電話</td>
                <td class="col-md-2 type1">
                  <validate
                    :rules="{ required: true, regex: StartCalledNumberRegex }"
                  >
                    <input
                      v-model="editData.StartCalledNumber"
                      type="text"
                      name="startCalledNumber"
                      class="form-control"
                    />
                  </validate>
                </td>
                <td class="col-md-2 type1">*筆數</td>
                <td class="col-md-2 type1">
                  <validate rules="required">
                    <input
                      type="text"
                      class="form-control"
                      v-model="editData.CalledCount"
                    />
                  </validate>
                </td>
              </template>
              <template v-if="['1'].includes(editData.NumberMode)">
                <td class="col-md-2 type2">上傳名單</td>
                <td class="col-md-6 type2" colspan="3">
                  <input
                    type="file"
                    name="list"
                    accept=".txt"
                    @change="(e) => onFileChange(e)"
                  />
                </td>
              </template>
            </tr>
            <tr>
              <td>*撥出電話等待秒數</td>
              <td>
                <validate rules="required|min_value:5|max:300">
                  <input
                    type="text"
                    placeholder="5~300"
                    class="form-control"
                    v-model="editData.CallProgressTime"
                  />
                </validate>
              </td>
              <td>自動撥號速度</td>
              <td>
                <select class="form-control" v-model="editData.ConcurrentCalls">
                  <option
                    v-for="call in _.concat([3, 5, 7], _.range(10, 101), [
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
              <td class="col-md-2">開始日期</td>
              <td class="col-md-2">
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
              <td>開始時間2</td>
              <td>
                <date-time-picker
                  type="timepicker"
                  :time.sync="editData.StartTime2"
                />
              </td>
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
              <td>結束時間1</td>
              <td>
                <date-time-picker
                  type="timepicker"
                  :time.sync="editData.StopTime1"
                />
              </td>
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
              <td>紀錄按鍵</td>
              <td>
                <Switcher v-model="editData.WaitDTMF" />
              </td>
              <td>亂數排序</td>
              <td>
                <Switcher v-model="editData.random" />
              </td>

              <td></td>
              <td>
                <input
                  class="form-control btn btn-primary"
                  type="button"
                  :disabled="invalid"
                  value="新增"
                  @click="doCreate()"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </validation>

      <button type="button" class="btn btn-danger" @click="doDelete()">
        Delete
      </button>

      <data-table
        allChecked
        :datas="datas"
        :columns="[
          { key: 'UserID', name: '用戶' },
          { key: 'PlanName', name: '掃號名稱' },
          { key: 'StartCalledNumber', name: '起始電話' },
          { key: 'CalledCount', name: '筆數' },
          { key: 'UseState', name: '啟用' },
          { key: 'action', name: '操作', width: 100 },
        ]"
        :trStyle="
          (data) => ({
            'background-color': !data.Count ? '#ffbbbb' : '',
          })
        "
      >
        <template v-slot:allChecked>
          <input type="checkbox" class="checkAll" v-model="isAllChecked" />
        </template>

        <template v-slot:checked="{ data }">
          <input type="checkbox" v-model="data.checked" />
        </template>

        <template v-slot:UseState="{ data }">
          <Switcher disabled v-model="data.UseState" />
        </template>

        <template v-slot:action="{ data }">
          <button type="button" class="btn btn-info" @click="toUpdate(data)">
            編輯
          </button>
        </template>
      </data-table>
    </div>
  </section>
</template>

<script>
import DateTimePicker from "../components/DateTimePicker";
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import LibraryMixins from "mixins/Library";

const RANGE = "0";
const LIST = "1";
export default {
  mixins: [CommonMixins, ListMixins, LibraryMixins],
  components: {
    DateTimePicker,
  },
  data: () => ({
    editData: {
      NumberMode: "1",
      StartCalledNumber: "",
      CalledCount: "",
      ConcurrentCalls: 10,
      StartDate: "",
      StartTime1: "",
      StartTime2: "",
      StartTime3: "",
      StopDate: "",
      StopTime1: "",
      StopTime2: "",
      StopTime3: "",
      FileName1: "",
      FileName2: "",
      CallRetry: 0,
      RetryTime: 600,
      StopOnConCount: 0,
      CallProgressTime: 20,
      ConcurrentCalls: 10,
      WaitDTMF: 0,
      random: "0",
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
    async getList() {
      const res = await $.callApi.post("api/adGroupCallSchedule/list");
      this.datas = res.data.map((x) => ({ ...x, checked: false }));
    },
    onFileChange(e) {
      this.editData.list = e.target.files[0];
    },
    async doCreate() {
      await $.callApi.post(
        "api/adGroupCallSchedule/create",
        this.requestFunc.toFormData(this.editData),
        { formData: true }
      );
      this.$swal("新增成功");
      this.getList();
    },
    toUpdate(data) {
      redirect(
        `adCallSetting/adCallScheduleModify?UserID=${data["UserID"]}&CallOutID=${data["CallOutID"]}`
      );
    },
    async doDelete() {
      await this.$confirm();
      await $.callApi.post("api/adGroupCallSchedule/delete", {
        datas: this.datas
          .filter((x) => x.checked)
          .map((x) => ({
            UserID: x.UserID,
            CallOutID: x.CallOutID,
          })),
      });
      this.getList();
    },
  },
  computed: {
    StartCalledNumberRegex() {
      switch (this.editData.NumberMode) {
        case RANGE:
          return /^[0-9]+$/;
          break;
      }
    },
  },
  mounted() {
    this.getOptions();
    // const timer = setInterval(this.getList, 10000)
    // this.$once("hook:destroyed", () => clearInterval(timer))
    this.getList();
  },
};
</script>