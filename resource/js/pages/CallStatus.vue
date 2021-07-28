<template>
  <section>
    <div class="form-inline">
      <div class="form-group">
        <label>自動撥號路由線數:</label>
        <input
          type="text"
          size="5"
          :class="{ 'form-control': true, 'num-only': true, readonly: !isRoot }"
          v-model="user.MaxCalls"
          @focus="setTmpValue"
          @change="(e) => changeMaxCalls(e)"
        />
      </div>
      <div class="form-group">
        <label>自動撥號速度:</label>
        <select
          class="form-control"
          v-model="user.ConcurrentCallsAmp"
          @change="(e) => changeConcurrentCallsAmp(e)"
        >
          <option v-for="item in _.range(1, 6)" :key="item" :value="item">
            {{ item }}
          </option>
        </select>
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
        <label id="balance" class="text-red">{{ subData.balance }}</label>
      </div>
    </div>
    <br />

    <div class="col-md-12 col-xs-12">
      <div class="panel panel-info">
        <div class="panel-heading text-center">
          執行中({{ subData.data1.length }})
        </div>
        <div style="height: 300px; overflow: auto">
          <table class="table table-h table-striped panel-footer">
            <tbody>
              <tr>
                <th>號</th>
                <th>撥打號碼</th>
                <th>狀態</th>
                <th>分機</th>
                <th>座</th>
                <th>時間</th>
                <th>Ping</th>
                <th>掛</th>
              </tr>
              <tr v-for="(data, index) in subData.data1" :key="index">
                <td>{{ index + 1 }}</td>
                <td>
                  <span>{{ data.CalledId }}</span>
                  <span v-if="data.NormalCall" class="label label-danger">
                    節費
                  </span>
                </td>
                <td>
                  {{ data.status == Const.DIALING ? "撥號中" : "通話中" }}
                </td>
                <td>{{ data.ExtensionNo }}</td>
                <td>{{ data.CalloutGroupID }}</td>
                <td>{{ data.CallDuration }}</td>
                <td>{{ data.PingTime }}</td>
                <td>
                  <input
                    type="button"
                    value="掛"
                    class="btn btn-info"
                    @click="(e) => doHangUp(index, e)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="table-responsive col-md-12 col-xs-12">
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
            <th>未接</th>
            <th style="min-width: 70px">座席</th>
            <th style="width: 80px">開關</th>
            <th>刪除</th>
          </tr>
          <tr v-for="(data, index) in subData.data3" :key="index">
            <td>{{ index + 1 }}</td>
            <!--編號-->
            <td>{{ data.PlanName }}</td>
            <!--群呼名稱-->
            <td>{{ data.StartCalledNumber_txt }}</td>
            <!--號碼區段-->
            <td>
              <a
                href="javascript:;"
                @click="
                  downloadNumberList(
                    { CallOutID: data.CallOutID },
                    data.StartCalledNumber
                  )
                "
                >{{ data.CalledCount }}</a
              >
            </td>
            <!--筆數-->
            <td>
              <a
                href="javascript:;"
                @click="
                  downloadWaitCall(
                    { CallOutID: data.CallOutID },
                    data.StartCalledNumber
                  )
                "
                >{{ data.WaitCall }}</a
              >
            </td>
            <!--待發-->
            <td>
              <a
                href="javascript:;"
                @click="
                  downloadCallOut(
                    { CallOutID: data.CallOutID },
                    data.StartCalledNumber
                  )
                "
                >{{ data.CalloutCount }}</a
              >
            </td>
            <!--執行-->
            <td>
              <a
                href="javascript:;"
                @click="
                  downloadCallCon(
                    { CallOutID: data.CallOutID },
                    data.StartCalledNumber
                  )
                "
                >{{ data.CallConCount }}</a
              >
            </td>
            <!--接聽數-->
            <td :class="{
              'text-success': parseFloat(data.CallConCount_txt) < 20
            }">{{ data.CallConCount_txt }}%</td>
            <!--接通率-->
            <td>
              <a
                href="javascript:;"
                @click="
                  downloadCallMissed(
                    { CallOutID: data.CallOutID },
                    data.StartCalledNumber
                  )
                "
                class="btn btn-info"
                >未接</a
              >
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
              <Switcher
                v-model="data.UseState"
                :on="true"
                :off="false"
                @change="changeUseState(subData.data3[index])"
              />
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
import LibraryMixins from "mixins/Library";
import Switcher from "../components/Switcher";

export default {
  mixins: [CommonMixins, LibraryMixins],
  components: { Switcher },
  data: () => ({
    timer: null,
    user: {},
    alert_txt:
      "總路由線路數必須 >= 掃號路由線數 + 節費路由線數 +自動撥號當前線數",
    subData: { data1: [], data3: [] },
    Const: {
      DIALING: 0,
      TALKING: 1,
    },
  }),
  methods: {
    async getBaseData() {
      const res = await $.callApi.post("api/callStatus/base");
      this.user = res.data;
    },
    setTmpValue(e) {
      this.tmp = e.target.value;
    },
    updateSuccess() {
      alertify.alert("已成功修改!");
    },
    async changeMaxCalls(e) {
      if (!this.countCondition) {
        this.user.MaxCalls = this.tmp;
        alert(this.alert_txt);
      } else {
        await $.callApi.post("api/callStatus/update/maxCalls", {
          MaxCalls: e.target.value,
        });
        this.updateSuccess();
      }
    },
    async changeConcurrentCallsAmp(e) {
      await $.callApi.post("api/callStatus/update/concurrentCallsAmp", {
        ConcurrentCallsAmp: e.target.value,
      });
      this.updateSuccess();
    },
    async changeCallWaitingTime(e) {
      await $.callApi.post("api/callStatus/update/callWaitingTime", {
        CallWaitingTime: e.target.value,
      });
      this.updateSuccess();
    },
    async changePlanDistribution(e) {
      await $.callApi.post("api/callStatus/update/planDistribution", {
        PlanDistribution: e.target.value,
      });
      this.updateSuccess();
    },
    async changeCalloutGroupID(value, CallOutID) {
      await $.callApi.post("api/callStatus/modify/calloutGroupID", {
        CalloutGroupID: value,
        CallOutID,
      });
      this.updateSuccess();
    },
    async changeSuspend(e) {
      await this.$confirm("確定要變更狀態?");
      await $.callApi.post("api/callStatus/update/suspend", {
        userId: this.choice,
      });
      this.user.isSuspend = !this.user.isSuspend;
    },
    async changeUseState(item) {
      this.stopUpdate();
      try {
        await $.callApi.post("api/callStatus/modify/useState", {
          CallOutID: item.CallOutID,
          UseState: item.UseState,
        });
        this.updateSuccess();
      } catch (e) {
      } finally {
        this.startUpdate();
      }
    },
    async doHangUp(index, e) {
      this.stopUpdate();
      try {
        const item = this.subData.data1[index];
        await this.$confirm("刪除掛斷");
        await $.callApi.post("api/callStatus/callRelease", {
          Seat: item.Seat,
          CalledID: item.CalledId,
        });
        this.subData.data1.splice(index, 1);
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
        await $.callApi.post("api/callStatus/delete/callPlan", {
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
      const res = await $.callApi.go("sysLookout/ajaxCallStatusContent2", {
        userId: this.choice,
      });

      res.data1 = _(res.data1)
        .map((x) => ({
          ...x,
          status: !x.ExtensionNo ? this.Const.DIALING : this.Const.TALKING,
        }))
        .orderBy(["ExtensionNo", "status"], ["desc", "asc"])
        .value();
      res.data3.forEach(function (x) {
        var WaitCall = x.CalledCount - x.CalloutCount;
        x.WaitCall = WaitCall > 0 ? WaitCall : 0;

        x.ConcurrentCalls = +x.ConcurrentCalls;
        x.CalloutGroupID = +x.CalloutGroupID;
        if (x.NumberMode == 0 && x.CalledCount > 1) {
          const endNumber = (+x.StartCalledNumber + x.CalledCount)
            .toString()
            .padLeft("0", x.StartCalledNumber.length);
          x.StartCalledNumber_txt = `${x.StartCalledNumber}~${endNumber}`;
        } else if (x.NumberMode === 3) {
          if (x.EndCalledNumber) {
            x.StartCalledNumber_txt = `${x.StartCalledNumber}~${x.EndCalledNumber}(有效)`;
          } else {
            x.StartCalledNumber_txt = `${x.StartCalledNumber}(有效)`;
          }
        } else {
          x.StartCalledNumber_txt = x.StartCalledNumber;
        }

        x.CallConCount_txt =
          x.CallConCount == 0
            ? "0"
            : ((x.CallConCount / x.CalloutCount) * 100).toFixed(2);
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
    async downloadNumberList(CallOutID, suffix) {
      this.download("api/callStatus/numberList", CallOutID, suffix);
    },
    async downloadWaitCall(CallOutID, suffix) {
      this.download("api/callStatus/waitCall", CallOutID, suffix);
    },
    async downloadCallOut(CallOutID, suffix) {
      this.download("api/callStatus/callOut", CallOutID, suffix);
    },
    async downloadCallCon(CallOutID, suffix) {
      this.download("api/callStatus/callCon", CallOutID, suffix);
    },
    async downloadCallMissed(CallOutID, suffix) {
      this.download("api/callStatus/callMissed", CallOutID, suffix);
    },
    async download(apiUrl, CallOutID, suffix) {
      const res = await $.callApi.post(apiUrl, {
        CallOutID,
      });
      this.fileFunc.exportTxt(
        res.data.join("\r\n"),
        moment().getDateTime() + `_${choice}_${suffix}.txt`
      );
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
  },
  mounted() {
    this.getBaseData();
    this.update();
    this.startUpdate();
  },
};
</script>