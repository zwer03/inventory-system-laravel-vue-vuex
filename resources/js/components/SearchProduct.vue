<template>
  <v-container fluid>
    <!-- {{items}} -->
    <v-combobox
      v-if="comboBox"
      v-model="model"
      :filter="filter"
      :hide-no-data="!search"
      :items="items"
      :search-input.sync="search"
      :clearable="true"
      hide-selected
      label="Select products"
      multiple
      small-chips
    >
      <template v-slot:no-data>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>
              No results matching "
              <strong>{{ search }}</strong
              >". Press <kbd>enter</kbd> to create a new one
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:selection="{ attrs, item, parent, selected }">
        <v-chip
          v-if="item === Object(item)"
          v-bind="attrs"
          :input-value="selected"
          label
          small
        >
          <span class="pr-2">{{ item.text }}</span>
          <v-icon small @click="parent.selectItem(item)">X</v-icon>
        </v-chip>
      </template>
      <template v-slot:item="{ index, item }">
        <v-text-field
          v-if="editing === item"
          v-model="editing.text"
          autofocus
          flat
          background-color="transparent"
          hide-details
          solo
          @keyup.enter="edit(index, item)"
        ></v-text-field>
        <v-chip v-else dark label small>{{ item.text }}</v-chip>
        <v-spacer></v-spacer>
        <v-list-item-action @click.stop>
          <v-btn icon @click.stop.prevent="edit(index, item)">
            <v-icon>{{ editing !== item ? "mdi-pencil" : "mdi-check" }}</v-icon>
          </v-btn>
        </v-list-item-action>
      </template>
    </v-combobox>
    <v-autocomplete
      v-else
      v-model="model"
      :items="items"
      :clearable="true"
      label="Select products"
      persistent-hint
      multiple
      small-chips
    >
    </v-autocomplete>
  </v-container>
</template>

<script>
import { mapState } from "vuex";
export default {
  props: ["comboBox"],
  data() {
    return {
      // items: [],
      activator: null,
      attach: null,
      editing: null,
      index: -1,
      nonce: 1,
      menu: false,
      model: [],
      x: 0,
      search: null,
      y: 0,
    };
  },
  watch: {
    model(val, prev) {
      if (val.length === prev.length) return;

      this.model = val.map((v) => {
        if (typeof v === "string") {
          v = {
            text: v,
          };

          this.items.push(v);

          this.nonce++;
        }
        let selectedItem = null;
        if (!this.comboBox) {
          selectedItem = this.items.find((item) => {
            return item.value == v;
          });
        }
        this.$emit("selectedItem", selectedItem ? selectedItem : v);
        return v;
      });
    },
  },
  mounted() {
    // axios.get("/products/get").then(response => {
    //     this.items = response.data;
    // });
    // this.items = this.$store.state.setup.products
  },
  methods: {
    edit(index, item) {
      if (!this.editing) {
        this.editing = item;
        this.index = index;
      } else {
        this.editing = null;
        this.index = -1;
      }
    },
    filter(item, queryText, itemText) {
      if (item.header) return false;

      const hasValue = (val) => (val != null ? val : "");

      const text = hasValue(itemText);
      const query = hasValue(queryText);

      return (
        text.toString().toLowerCase().indexOf(query.toString().toLowerCase()) >
        -1
      );
    },
  },
  computed: {
    ...mapState({
      items: (state) => state.setup.products,
    }),
  },
};
</script>
