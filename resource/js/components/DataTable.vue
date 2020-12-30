<template>
  <table class="table table-h table-striped table-hover">
    <thead>
      <slot name="thead"></slot>
    </thead>
    <tbody>
      <tr>
        <th v-if="allChecked">
          <slot name="allChecked"></slot>
        </th>
        <th v-if="startIndex">編號</th>
        <template v-for="(item, index) in columns">
          <th
            :key="index"
            v-if="!(item.show === false)"
            style="text-align: center; border-left: 1px solid #333"
            :class="{
              [item.thClass]: true,
              sorting: item.sortable,
              sorting_asc:
                item.sortable && sort.key == item.key && sort.type == 'asc',
              sorting_desc:
                item.sortable && sort.key == item.key && sort.type == 'desc',
            }"
            :width="item.width"
            @click="item.sortable ? changeSort(item.key) : ''"
          >
            {{ item.name }}
          </th>
        </template>
      </tr>
      <tr v-for="(data, di) in datas" :key="'data' + di" :style="trStyle(data)">
        <td v-if="allChecked">
          <slot name="checked" :data="data"></slot>
        </td>
        <td v-if="startIndex">{{ startIndex + di }}</td>
        <template v-for="(item, index) in columns">
          <td
            v-if="!(item.show === false)"
            :key="index"
            :class="{ [item.className]: true }"
          >
            <slot :name="item.key" :data="data">{{ data[item.key] }}</slot>
          </td>
        </template>
      </tr>
    </tbody>
    <tfoot>
      <slot name="tfoot"></slot>
    </tfoot>
  </table>
</template>

<script>
export default {
  props: {
    datas: {
      type: Array,
      required: true,
    },
    columns: {
      type: Array,
      required: true,
    },
    allChecked: {
      type: Boolean,
      default: false,
    },
    startIndex: {
      type: Number,
      default: 0,
    },
    trStyle: {
      type: Function,
      default: () => ({}),
    },
    sort: {
      type: Object,
      data: () => ({}),
    },
  },
  methods: {
    changeSort(key) {
      this.$emit("changeSort", key);
    },
  },
  computed: {
    isAllChecked: {
      get() {
        return this.datas.every((x) => x.cehcked);
      },
      set() {
        this.$emit(
          "update:datas",
          this.datas.map((x) => ((x.checked = true), x))
        );
      },
    },
  },
};
</script>