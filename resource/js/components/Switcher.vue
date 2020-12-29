<template>
  <label class="switch">
    <input
      :disabled="disabled"
      type="checkbox"
      true-value="1"
      false-value="0"
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
    value: {
      required: true,
    },
  },
  watch: {
    data(val) {
      this.$emit("input", val);
      this.$emit("change", val);
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