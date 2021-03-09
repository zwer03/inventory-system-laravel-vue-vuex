<template>
  <div>
    <!-- {{items}} -->
    <v-combobox
      v-model="model"
      :items="items"
      :auto-select-first="true"
      :error-messages="errorMessage"
      :readonly="readOnly"
      label="Select Supplier/Customer"
    >
    </v-combobox>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  props: ["defaultValue", "errorMessage", "readOnly"],
  data() {
    return {
      model: null,
      // items: []
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
      items: (state) => state.setup.companies,
    }),
  },
};
</script>
