<template>
  <div>
    <!-- {{items}} -->
    <validation-provider rules="required" v-slot="{ errors }">
      <v-combobox
        v-model="model"
        :items="items"
        :auto-select-first="true"
        :label="label ? label : 'Select storage'"
        :autofocus="autofocus"
        :error-messages="errors[0]"
        :readonly="readOnly"
      ></v-combobox>
    </validation-provider>
  </div>
</template>

<script>
import { ValidationProvider, extend } from "vee-validate";
import { required } from "vee-validate/dist/rules";

extend("required", {
  ...required,
  message: "This field is required",
});
import { mapState } from "vuex";
export default {
  components: { ValidationProvider },
  props: ["defaultValue", "label", "autofocus", "readOnly"],
  data() {
    return {
      model: null,
      // items: [],
    };
  },
  watch: {
    model(val, prev) {
      this.$emit("selectedItem", val);
    },
    defaultValue(val) {
      this.model = this.items.find((item) => {
        return item.value == val;
      });
    },
  },
  mounted() {
    this.model = this.items.find((item) => {
      return item.value == this.defaultValue;
    });
  },
  computed: {
    ...mapState({
      items: (state) => state.setup.storages,
    }),
  },
};
</script>
