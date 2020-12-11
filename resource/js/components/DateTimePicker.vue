<template>
  <div>
    <input
      v-if="type == 'datepicker'"
      type="text"
      class="form-control"
      :value="date"
      ref="datePicker"
    />
    <div class="input-group" v-else-if="type == 'datetimepicker'">
      <input type="text" class="form-control" :value="date" ref="datePicker" />
      <span class="input-group-addon"> </span>
      <input type="text" class="form-control" :value="time" ref="timePicker" />
    </div>
    <input
      v-else-if="type == 'timepicker'"
      type="text"
      class="form-control"
      :value="time"
      ref="timePicker"
    />
  </div>
</template>

<script>
// need Bundle::addLink("datetime");
export default {
  props: ["date", "time", "type"],
  mounted: function () {
    $(this.$refs.datePicker).datetimepicker({
      timepicker: false,
      format: "Y/m/d",
      onChangeDateTime: function (currentTime, $input) {
        //console.log('update:date', $input.val())
        this.$emit("update:date", $input.val());
      }.bind(this),
      scrollMonth: false,
      scrollInput: false,
    });
    $(this.$refs.timePicker).datetimepicker({
      datepicker: false,
      format: "H:i:00",
      step: 30,
      onChangeDateTime: function (currentTime, $input) {
        //console.log('update:time', $input.val())
        this.$emit("update:time", $input.val());
      }.bind(this),
      scrollMonth: false,
      scrollInput: false,
    });
  },
};
</script>