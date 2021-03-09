<template>
  <div>
    <!-- {{items}} -->
    <v-combobox
      v-model="model"
      :items="items"
      :auto-select-first="true"
      :error-messages="errorMessage"
      :readonly="readOnly"
      label="Select warehouse"
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
  },
  mounted() {
    this.model = this.items.find((item) => {
      return item.value == this.defaultValue;
    });
    // axios.get("/warehouses/get").then(response => {
    //     this.items = response.data
    //     this.model = this.items[0]
    // });
  },
  computed: {
    ...mapState({
      items: (state) => state.setup.warehouses,
    }),
  },
};
</script>
