<template>
  <section>
    <div class="col-md-12 form-inline">
      <label class="col-md-2">
        <input
          type="radio"
          v-model="editData.NumberMode"
          class="radio"
          :value="Const.RANGE"
        />區段增加</label
      >
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
          :value="Const.SAME"
        />同號測試</label
      >
      <label class="col-md-2">
        <input
          type="radio"
          v-model="editData.NumberMode"
          class="radio"
          :value="Const.VALID"
        />有效號新增</label
      >
      <div class="col-md-4"></div>
    </div>

    <div class="table-responsive col-md-12">
      <validation v-slot="{ invalid }">
        <table class="table table-v">
          <tbody>
            <tr>
              <td class="col-md-2">群呼名稱</td>
              <td class="col-md-2">
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.PlanName"
                />
              </td>
              <template v-if="['0', '2', '3'].includes(editData.NumberMode)">
                <td class="col-md-2 type1">*起始電話</td>
                <td class="col-md-2 type1">
                  <validate rules="required">
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
              <td>座席</td>
              <td>
                <select class="form-control" v-model="editData.CalloutGroupID">
                  <option
                    v-for="item in _.range(1, 5)"
                    :key="item"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </select>
              </td>
              <td>響鈴方式</td>
              <td>
                <select
                  class="form-control"
                  v-model="editData.Calldistribution"
                >
                  <option value="0">分號少的開始配號</option>
                  <option value="1" selected>自動平均分配</option>
                </select>
              </td>
              <td>撥出電話等待秒數</td>
              <td>
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.CallProgressTime"
                />
              </td>
            </tr>
            <tr>
              <td>轉分機等待秒數</td>
              <td>
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.ExtProgressTime"
                />
              </td>

              <td></td>
              <td colspan="3">
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
          { key: 'PlanName', name: '群呼名稱' },
          { key: 'StartCalledNumber', name: '起始電話' },
          { key: 'CalledCount', name: '筆數' },
          { key: 'Calldistribution', name: '響鈴方式' },
          { key: 'UseState', name: '啟用' },
          { key: 'action', name: '操作', width: 100 },
        ]"
      >
        <template v-slot:allChecked>
          <input type="checkbox" class="checkAll" v-model="isAllChecked" />
        </template>

        <template v-slot:checked="{ data }">
          <input type="checkbox" v-model="data.checked" />
        </template>

        <template v-slot:Calldistribution="{ data }">
          <div>
            {{
              data["Calldistribution"] == "1"
                ? "自動平均分配"
                : "分號少的開始配號"
            }}
          </div>
        </template>

        <template v-slot:UseState="{ data }">
          <label class="switch">
            <input
              id="switch"
              disabled
              type="checkbox"
              :checked="data.UseState"
            />
            <div class="slider round"></div>
          </label>
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
import CommonMixins from "mixins/Common";
import ListMixins from "mixins/List";
import LibraryMixins from "mixins/Library";

const RANGE = "0";
const LIST = "1";
const SAME = "2";
const VALID = "3";
export default {
  mixins: [CommonMixins, ListMixins, LibraryMixins],
  data: () => ({
    editData: {
      NumberMode: "0",
      StartCalledNumber: "",
      ExtProgressTime: "15",
      CallProgressTime: "30",
      Calldistribution: "0",
      CalloutGroupID: 1,
    },
    Const: {
      RANGE,
      LIST,
      SAME,
      VALID,
    },
  }),
  watch: {
    "editData.NumberMode"() {
      if (this.editData.NumberMode === VALID) {
        this.editData.StartCalledNumber = "09";
      }
    },
    "editData.StartCalledNumber"() {
      if (
        this.editData.NumberMode === VALID &&
        !this.editData.StartCalledNumber.startsWith("09")
      ) {
        alertify.alert("只接受09開頭的號碼");
        this.editData.StartCalledNumber = "09";
      }
    },
  },
  methods: {
    async getList() {
      const res = await $.callApi.post("api/groupCallSchedule/list");
      this.datas = res.data.map((x) => ({ ...x, checked: false }));
    },
    onFileChange(e) {
      this.editData.list = e.target.files[0];
      evt.target.value = "";
    },
    async doCreate() {
      await $.callApi.post(
        "api/groupCallSchedule/create",
        this.requestFunc.toFormData(this.editData),
        { formData: true }
      );
      this.$swal("新增成功");
      this.getList();
    },
    toUpdate(data) {
      redirect(
        `groupCallSetting/groupCallScheduleModify?UserID=${data["UserID"]}&CallOutID=${data["CallOutID"]}`
      );
    },
    async doDelete() {
      await this.$confirm();
      await $.callApi.post("api/groupCallSchedule/delete", {
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
  mounted() {
    this.getList();
  },
};
</script>