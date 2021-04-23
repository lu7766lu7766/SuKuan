<template>
  <div class="table-responsive">
    <data-table
      :datas="sortDatas"
      :columns="[
        { key: 'UserID', name: '登入帳號(編輯)', sortable: true },
        { key: 'StatusCount', name: '狀態', sortable: true },
        { key: 'Count', name: '線數', sortable: true },
      ]"
      :sort="sort"
      @changeSort="(key) => changeSort(key)"
    >
      <template v-slot:tfoot>
          <tr>
            <td>合計</td>
            <td>{{ totalData.StatusCount }}</td>
            <td>{{ totalData.Count }}</td>
          </tr>
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
      const res = await $.callApi.post("api/callStatus/callStatistics");
      this.datas = res.data;
    },
  },
  computed: {
    totalData() {
      return {
        StatusCount: _.sumBy(this.datas, x => +x.StatusCount),
        Count: _.sumBy(this.datas, x => +x.Count)
      }
    }
  },
  mounted() {
    this.getList();
    const GET_DATA_INTERVAL = 5000;
    const timer = setInterval(this.getList, GET_DATA_INTERVAL);
    this.$once("hook:destoryed", () => clearInterval(timer));
  },
};
</script>