<template>
  <label class="switch">
    <input
      :disabled="disabled"
      type="checkbox"
      :true-value="on"
      :false-value="off"
      v-model="data"
    />
    <div class="slider round"></div>
  </label>
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
    on: {
      default: "1",
    },
    off: {
      default: "0",
    },
    value: {},
  },
  watch: {
    data(newVal, oldVal) {
      if (oldVal != null) {
        this.$emit("input", newVal);
        this.$emit("change", newVal);
      }
    },
    value: {
      immediate: true,
      handler(val) {
        this.data = val;
      },
    },
  },
  data: () => ({
    data: null,
  }),
};
</script>