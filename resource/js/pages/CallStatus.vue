<template>
  <section>
    <div class="form-inline">
      <div class="form-group">
        <label>自動撥號線數上限:</label>
        <input
          type="text"
          size="5"
          :class="{ 'form-control': true, 'num-only': true, readonly: !isRoot }"
          v-model="maxCallsLimit"
          @focus="setTmpValue"
          @change="(e) => changeMaxCallsLimit(e)"
        />
      </div>
      <div class="form-group">
        <label>自動撥號當前線數:</label>
        <input
          type="text"
          size="5"
          class="form-control num-only"
          v-model="user.MaxCalls"
          @focus="setTmpValue"
          @change="(e) => changeMaxCalls(e)"
        />
      </div>
      <div class="form-group">
        <label>自動停止秒數:</label>
        <input
          type="text"
          size="5"
          class="form-control num-only"
          v-model="user.CallWaitingTime"
          @change="(e) => changeCallWaitingTime(e)"
        />
      </div>
      <div class="form-group">
        <label>撥號分配模式:</label>
        <select
          class="form-control"
          v-model="user.PlanDistribution"
          @change="(e) => changePlanDistribution(e)"
        >
          <option value="0">輪詢</option>
          <option value="1">待發數多優先</option>
          <option value="2">第一筆開始優先分配</option>
          <option value="3">最後一筆開始優先分</option>
          <option value="4">待發數少的優先</option>
        </select>
      </div>

      <div class="form-group">
        <input
          type="button"
          :class="{
            btn: true,
            'btn-success': user.isSuspend,
            'btn-danger': !user.isSuspend,
          }"
          :value="user.isSuspend ? '啟動' : '停止'"
          @click="(e) => changeSuspend(e)"
        />
      </div>
    </div>
    <div class="form-inline">
      <div class="form-group">
        <label>總筆數:</label>
        <label>{{ subData.totalCalledCount }}</label>
      </div>
      <div class="form-group">
        <label>總待發數:</label>
        <label>{{ subData.totalWaitCall }}</label>
      </div>
      <div class="form-group">
        <label>剩餘點數:</label>
        <label id="balance" v-text="subData.balance"></label>
      </div>
    </div>
    <br />

    <div class="col-md-4 col-xs-12">
      <div class="panel panel-info">
        <div class="panel-heading text-center">
          執行中(<span v-text="subData.data1.length"></span>)
        </div>
        <div style="height: 300px; overflow: auto">
          <table class="table table-h table-striped panel-footer">
            <tbody>
              <tr>
                <th>號</th>
                <th>目的端號碼</th>
                <th>掛</th>
              </tr>
              <tr v-for="(data, index) in subData.data1" :key="index">
                <td>{{ index }}</td>
                <td>
                  <span v-text="data.CalledId"></span>
                  <span v-if="data.NormalCall" class="label label-danger"
                    >節費</span
                  >
                </td>
                <td>
                  <input
                    type="button"
                    value="掛"
                    class="btn btn-info"
                    @click="(e) => doHangUp(subData.data1, index, e)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-8 col-xs-12">
      <div class="panel panel-info">
        <div class="panel-heading text-center">
          等待分機(<span
            id="waitExtensionNoCount"
            v-text="subData.waitExtensionNoCount"
          ></span
          >) 分機(<span
            id="extensionNoCount"
            v-text="subData.extensionNoCount"
          ></span
          >)
        </div>
        <div style="height: 300px; overflow: auto">
          <table class="table table-h table-striped panel-footer">
            <tbody>
              <tr>
                <th>號</th>
                <th>分機</th>
                <th>目的端號碼</th>
                <th>座</th>
                <th>時間</th>
                <th>Ping</th>
                <th>掛</th>
              </tr>
              <tr v-for="(data, index) in subData.data2" :key="index">
                <td>{{ index + 1 }}</td>
                <td>
                  <span v-text="data.ExtensionNo"></span>
                  <span v-if="data.NormalCall" class="label label-danger"
                    >節費</span
                  >
                </td>
                <td>
                  <span v-text="data.CalledId"></span>
                  <span v-if="data.OnMonitor" class="label label-danger"
                    >監聽</span
                  >
                </td>
                <td v-text="data.CalloutGroupID"></td>
                <td v-text="data.CallDuration"></td>
                <td v-text="data.PingTime"></td>
                <td>
                  <input
                    type="button"
                    value="掛"
                    class="btn btn-info"
                    @click="(e) => doHangUp(subData.data2, index, e)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="table-responsive col-md-12">
      <table class="table table-h table-striped table-hover">
        <tbody>
          <tr>
            <th>編號</th>
            <th>群呼名稱</th>
            <th>號碼區段</th>
            <th>筆數</th>
            <th>待發</th>
            <th>執行</th>
            <th>接聽數</th>
            <th>接通率</th>
            <th>失敗下載</th>
            <th>未接</th>
            <th>撥號速度</th>
            <th>座席</th>
            <th style="width: 80px">開關</th>
            <th>刪除</th>
          </tr>
          <tr v-for="(data, index) in subData.data3" :key="index">
            <td>{{ index + 1 }}</td>
            <!--編號-->
            <td v-text="data.PlanName"></td>
            <!--群呼名稱-->
            <td v-text="data.StartCalledNumber_txt"></td>
            <!--號碼區段-->
            <td>
              <a
                target="_blank"
                :href="data.downloadCalledCount"
                v-text="data.CalledCount"
              ></a>
            </td>
            <!--筆數-->
            <td>
              <a
                target="_blank"
                :href="data.downloadWaitCall"
                v-text="data.WaitCall"
              ></a>
            </td>
            <!--待發-->
            <td>
              <a
                target="_blank"
                :href="data.downloadCalloutCount"
                v-text="data.CalloutCount"
              ></a>
            </td>
            <!--執行-->
            <td>
              <a
                target="_blank"
                :href="data.downloadCallConCount"
                v-text="data.CallConCount"
              ></a>
            </td>
            <!--接聽數-->
            <td v-text="data.CallConCount_txt"></td>
            <!--接通率-->
            <td>
              <a target="_blank" :href="data.downloadFaild" class="btn btn-info"
                >下載</a
              >
            </td>
            <td>
              <a
                target="_blank"
                :href="data.downloadMissed"
                class="btn btn-info"
                >未接</a
              >
            </td>
            <td>
              <concurrent-calls-select
                v-model="data.ConcurrentCalls"
                @change="
                  changeConcurrentCalls(data.ConcurrentCalls, data.CallOutID)
                "
                @focus="stopUpdate"
                @blur="startUpdate"
              />
            </td>
            <td>
              <select
                v-model="data.CalloutGroupID"
                class="form-control"
                @change="
                  changeCalloutGroupID(data.CalloutGroupID, data.CallOutID)
                "
                @focus="stopUpdate"
                @blur="startUpdate"
              >
                <option v-for="val in _.range(1, 5)" :key="val" :value="val">
                  {{ val }}
                </option>
              </select>
            </td>
            <td>
              <label class="switch">
                <input
                  class="useState_switch"
                  type="checkbox"
                  value="1"
                  v-model="data.UseState"
                  @change="changeUseState(subData.data3[index])"
                />

                <div class="slider round"></div>
              </label>
            </td>
            <td>
              <input
                type="button"
                value="刪"
                class="btn btn-danger"
                @click="doDelete(index, $event)"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
import CommonMixins from "mixins/Common";

export default {
  mixins: [CommonMixins],
  components: {
    ConcurrentCallsSelect: require("@/ConcurrentCallsSelect").default,
  },
  data: () => ({
    timer: null,
    user: {},
    alert_txt:
      "總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號當前線數",
    subData: { data1: [], data2: [], data3: [] },
  }),
  methods: {
    async getBaseData() {
      const res = await $.callApi.post("api/call-status/base");
      this.user = res.data;
    },
    setTmpValue(e) {
      this.tmp = e.target.value;
    },
    updateSuccess() {
      alertify.alert("已成功修改!");
    },
    async changeMaxCallsLimit(e) {
      if (!this.countCondition) {
        this.maxCallsLimit = this.tmp;
        alert(this.alert_txt);
      } else {
        await $.callApi.post("api/call-status/update/maxRoutingCalls", {
          MaxRoutingCalls: e.target.value,
        });
        this.updateSuccess();
      }
    },
    async changeMaxCalls(e) {
      if (!this.countCondition) {
        this.user.MaxCalls = this.tmp;
        alert(this.alert_txt);
      } else {
        await $.callApi.post("api/call-status/update/maxCalls", {
          MaxCalls: e.target.value,
        });
        this.updateSuccess();
      }
    },
    async changeCallWaitingTime(e) {
      await $.callApi.post("api/call-status/update/callWaitingTime", {
        CallWaitingTime: e.target.value,
      });
      this.updateSuccess();
    },
    async changePlanDistribution(e) {
      await $.callApi.post("api/call-status/update/planDistribution", {
        PlanDistribution: e.target.value,
      });
      this.updateSuccess();
    },
    async changeConcurrentCalls(value, CallOutID) {
      await $.callApi.post("api/call-status/modify/concurrentCalls", {
        ConcurrentCalls: value,
        CallOutID,
      });
      this.updateSuccess();
    },
    async changeCalloutGroupID(value, CallOutID) {
      await $.callApi.post("api/call-status/modify/calloutGroupID", {
        CalloutGroupID: value,
        CallOutID,
      });
      this.updateSuccess();
    },
    async changeSuspend(e) {
      await this.$confirm("確定要變更狀態?");
      await $.callApi.post("api/call-status/update/suspend", {
        userId: this.choice,
      });
      this.user.isSuspend = !this.user.isSuspend;
    },
    async changeUseState(item) {
      this.stopUpdate();
      try {
        await $.callApi.post("api/call-status/modify/useState", {
          CallOutID: item.CallOutID,
          UseState: item.UseState,
        });
        this.updateSuccess();
      } catch (e) {
      } finally {
        this.startUpdate();
      }
    },
    async doHangUp(datas, index, e) {
      this.stopUpdate();
      try {
        const item = datas[index];
        await this.$confirm("刪除掛斷");
        await $.callApi.post("api/call-status/callRelease", {
          Seat: item.Seat,
          CalledID: item.CalledId,
        });
        datas.splice(index, 1);
      } catch (err) {
      } finally {
        this.startUpdate();
      }
    },
    async doDelete(index, e) {
      this.stopUpdate();
      var item = this.subData.data3[index];
      try {
        await this.$confirm("刪除確認");
        await $.callApi.post("api/call-status/delete/callPlan", {
          CallOutID: item.CallOutID,
        });
        this.subData.data3.splice(index, 1);
      } catch (err) {
      } finally {
        this.startUpdate();
      }
    },
    stopUpdate() {
      clearInterval(this.timer);
    },
    startUpdate() {
      const UPDATE_TIME = 4 * 1000;
      this.timer = setInterval(this.update, UPDATE_TIME);
    },
    async update() {
      // let callApiTimes = 0;
      // const RELOAD_TIMES = 2000;

      // if (++callApiTimes > RELOAD_TIMES) {
      //   this.saveScrollTop();
      //   window.history.go(0);
      //   return;
      // }
      const res = await $.callApi.go("sysLookout/ajaxCallStatusContent2", {
        userId: this.choice,
      });

      res.data3.forEach(function (x) {
        var WaitCall = x.CalledCount - x.CalloutCount;
        x.WaitCall = WaitCall > 0 ? WaitCall : 0;
        x.StartCalledNumber_txt = x.StartCalledNumber;
        x.ConcurrentCalls = +x.ConcurrentCalls;
        x.CalloutGroupID = +x.CalloutGroupID;
        if (x.NumberMode == 0 && x.CalledCount > 1) {
          x.StartCalledNumber_txt +=
            "~" +
            (+x.StartCalledNumber + x.CalledCount)
              .toString()
              .padLeft("0", x.StartCalledNumber.length);
        }
        if (x.NumberMode === 3 && x.EndCalledNumber) {
          if (x.EndCalledNumber) {
            x.StartCalledNumber_txt += "~" + x.EndCalledNumber + "(有效)";
          } else {
            x.StartCalledNumber_txt += "(有效)";
          }
        }

        x.d_params = `?callOutId=${x.CallOutID}&startCalledNumber=${x.StartCalledNumber}`;

        x.downloadCalledCount = `${ctrl_uri}downloadCalledCount${x.d_params}`;
        x.downloadWaitCall = `${ctrl_uri}downloadWaitCall${x.d_params}`;
        x.downloadCalloutCount = `${ctrl_uri}downloadCalloutCount${x.d_params}`;
        x.downloadCallConCount = `${ctrl_uri}downloadCallConCount${x.d_params}`;
        x.downloadFaild = `${ctrl_uri}downloadFaild${x.d_params}`;
        x.downloadMissed = `${ctrl_uri}downloadMissed${x.d_params}`;

        x.CallConCount_txt =
          x.CallConCount == 0
            ? "0%"
            : ((x.CallConCount / x.CalloutCount) * 100).toFixed(2) + "%";
      });
      res.totalCalledCount = _.sumBy(res.data3, "CalledCount");
      res.totalWaitCall = _.sumBy(res.data3, "WaitCall");
      this.user.isSuspend = !!res.suspend;
      // delete data["suspend"];
      const { suspend: __, ...subData } = res;
      this.subData = subData;

      // this.reloadScrollTop();
    },
    saveScrollTop() {
      setCookie("scrollTop", $(window).scrollTop());
    },
    reloadScrollTop() {
      var st = getCookie("scrollTop");
      if (st) {
        $("html,body").scrollTop(st);
        delCookie("scrollTop");
      }
    },
  },
  computed: {
    countCondition() {
      return (
        +this.user.MaxRoutingCalls >=
        +this.user.MaxSearchCalls +
          +this.user.MaxRegularCalls +
          +this.user.MaxCalls
      );
    },
    maxCallsLimit: {
      get() {
        return +this.user.MaxRoutingCalls
          ? +this.user.MaxRoutingCalls -
              +this.user.MaxSearchCalls -
              +this.user.MaxRegularCalls
          : 0;
      },
      set(newValue) {
        this.user.MaxRoutingCalls =
          +newValue + +this.user.MaxSearchCalls + +this.user.MaxRegularCalls;
      },
    },
  },
  mounted() {
    this.getBaseData();
    this.startUpdate();
  },
};
</script>