<template>
  <div>
    <div class="row">
      <div class="form-group">
        <label>
          <input type="radio" v-model="editData.Status" value="1" />顯示
        </label>
        <label>
          <input type="radio" v-model="editData.Status" value="0" />關閉
        </label>
      </div>
      <div class="form-group">
        <textarea
          rows="10"
          class="form-control"
          v-model="editData.Content"
        ></textarea>
      </div>
      <div class="form-group">
        <button
          type="button"
          class="btn btn-primary"
          @click="doInsertOrUpdate()"
        >
          送出
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    editData: {
      Status: "1",
      Content: "",
    },
  }),
  methods: {
    async getData() {
      const res = await $.callApi.post("api/system/bulletinBoard/detail");
      this.editData = res.data || this.editData;
    },
    async doInsertOrUpdate() {
      await $.callApi.post(
        "api/system/bulletinBoard/insertOrUpdate",
        this.editData
      );
      this.$swal("更新成功");
    },
  },
  mounted() {
    this.getData();
  },
};
</script>