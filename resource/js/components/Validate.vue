<template>
  <validation-provider v-slot="{ errors, invalid, validate }" v-bind="$attrs">
    <slot :errors="errors" :invalid="invalid" :validate="validate" />
    <span v-if="showError && invalid" v-show="errors[0]" class="error">
      {{ errors[0] }}
    </span>
  </validation-provider>
</template>

<script>
import { ValidationProvider } from "vee-validate";
export default {
  components: {
    ValidationProvider,
  },
  props: {
    showError: {
      default: true,
    },
  },
};
</script>