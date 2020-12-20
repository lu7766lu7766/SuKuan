<template>
  <select
    class="form-control"
    v-model="data"
    @change="
      $emit('input', data);
      $emit('change');
    "
    @focus="$emit('focus')"
    @blur="$emit('blur')"
  >
    <option
      v-for="calls in _.range(1, maxCalls + 1).reverse()"
      :key="calls"
      :value="calls"
    >
      {{ `每 1 秒 ${calls} 通` }}
    </option>
  </select>
</template>

<script>
export default {
  props: {
    maxCalls: {
      type: Number,
      default: 30,
    },
    value: {
      type: Number,
      required: true,
    },
  },
  data: () => ({
    data: "",
  }),
  watch: {
    value: {
      immediate: true,
      handler() {
        this.data = this.value;
      },
    },
  },
};
</script>