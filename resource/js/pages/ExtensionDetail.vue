<template>
  <validation v-slot="{ invalid }">
    <table class="table table-v">
      <tbody>
        <tr>
          <td class="col-md-3 col-xs-5">用戶</td>
          <td class="col-md-9 col-xs-7">
            <validate rules="required">
              <select
                class="form-control"
                v-if="isCreate"
                v-model="editData.UserID"
              >
                <option
                  v-for="(item, index) in options.empSelect"
                  :key="index"
                  :value="item.value"
                >
                  {{ item.name }}
                </option>
              </select>
              <span v-else>{{ editData.UserID }}</span>
            </validate>
          </td>
        </tr>
        <tr>
          <td>分機號碼</td>
          <td>
            <div class="form-inline" v-if="isCreate">
              <validate rules="required|numeric">
                <input
                  type="text"
                  class="form-control"
                  v-model="editData.ExtensionNo"
                />
              </validate>
              <span>～</span>
              <input
                type="text"
                class="form-control"
                v-model="editData.ExtensionNos"
              />
            </div>
            <span v-else>{{ editData.ExtensionNo }}</span>
          </td>
        </tr>
        <tr>
          <td>分機名稱</td>
          <td>
            <input
              type="text"
              class="form-control"
              v-model="editData.ExtName"
            />
          </td>
        </tr>
        <tr>
          <td>手動撥號主叫</td>
          <td>
            <validate rules="numeric">
              <input
                type="text"
                class="form-control"
                :disabled="!isRoot"
                v-model="editData.OffNetCli"
              />
            </validate>
          </td>
        </tr>
        <tr>
          <td>註冊密碼</td>
          <td>
            <input
              type="text"
              name="customerPwd"
              class="form-control num-only"
              v-model="editData.CustomerPwd"
            />
          </td>
        </tr>
        <template v-if="isUpdate">
          <tr>
            <td>錄音:</td>
            <td>
              <Switcher v-model="editData.StartRecorder" />
            </td>
          </tr>
          <tr>
            <td>狀態:</td>
            <td>
              <Switcher v-model="editData.Suspend" on="0" off="1" />
            </td>
          </tr>
          <tr v-if="isRoot">
            <td>是否啟用:</td>
            <td>
              <Switcher v-model="editData.UseState" />
            </td>
          </tr>
        </template>
        <tr>
          <td>座席</td>
          <td>
            <select class="form-control" v-model="editData.CalloutGroupID">
              <option></option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input
              class="btn btn-primary form-control"
              type="button"
              :disabled="invalid"
              :value="isUpdate ? '編輯' : '新增'"
              @click="isUpdate ? doUpdate() : doCreate()"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </validation>
</template>
<script>
import EmpMixins from "mixins/Emp";
import ListMixins from "mixins/List";
import CommonMixins from "mixins/Common";

export default {
  mixins: [EmpMixins, ListMixins, CommonMixins],
  methods: {
    async getDetail() {
      const res = await $.callApi.post("api/extensionManage/detail", {
        UserID: this.UserID,
        ExtensionNo: this.ExtensionNo,
      });
      this.editData = res.data;
    },
    async doCreate() {
      await $.callApi.post("api/extensionManage/create", this.editData);
      redirect("extensionInfo/extensionManage");
    },
    async doUpdate() {
      await $.callApi.post("api/extensionManage/update", this.editData);
      history.back();
    },
  },
  computed: {
    UserID() {
      return this.$url.get("UserID");
    },
    ExtensionNo() {
      return this.$url.get("ExtensionNo");
    },
    isUpdate() {
      return !!(this.UserID && this.ExtensionNo);
    },
    isCreate() {
      return !this.isUpdate;
    },
  },
  mounted() {
    this.getEmpSelect();
    this.isUpdate && this.getDetail();
    if (this.isCreate) {
      this.editData.CustomerPwd = _.range(0, 10)
        .map((x) => _.random(0, 9))
        .join("");
    }
  },
};
</script>