<template>
  <ul class="pager responsive">
    <li><a href="#" @click="pageChange(1)">第一頁</a></li>
    <li>
      <a href="#" v-if="page != 1" @click="pageChange(page - 1)">上一頁</a>
    </li>
    <li>
      <a
        href="#"
        v-if="lastPage > 0 && page != lastPage"
        @click="pageChange(page + 1)"
        >下一頁</a
      >
    </li>
    <li><a href="#" @click="pageChange(lastPage)">最後一頁</a></li>
    <li>
      <a href="#"
        >第
        <select :value="page" @change="pageChange($event.target.value)">
          <option
            v-for="(page, index) in pageSelector"
            :key="index"
            :value="page"
          >
            {{ page }}
          </option>
        </select>
        頁</a
      >
    </li>
    <li>
      <a href="#">(共{{ lastPage }}頁，{{ total }}筆資料)</a>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    page: {
      required: true,
    },
    total: {
      required: true,
    },
    per_page: {
      default: 100,
    },
  },
  methods: {
    pageChange(page) {
      this.$emit("change", page);
    },
  },
  computed: {
    lastPage() {
      return Math.ceil(this.total / this.per_page);
    },
    pageSelector() {
      return _.range(1, this.lastPage + 1);
    },
  },
};
</script>