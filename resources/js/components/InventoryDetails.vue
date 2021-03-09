<template>
  <div>
    <v-container class lg="6" offset-lg="3">
      <v-row
        v-if="
          $route.params.transaction_type != 'out' ||
          $route.params.transaction_type != 'transfer'
        "
      >
        <v-col>
          <search-warehouse
            v-if="$route.params.transaction_type"
            :defaultValue="defaultWarehouse ? defaultWarehouse : 1"
            :readOnly="true"
          ></search-warehouse>
        </v-col>
        <v-col>
          <search-storage
            @selectedItem="updateSelStorage"
            :defaultValue="1"
            :autofocus="true"
            :readOnly="this.$route.params.status == 10 ? true : false"
          ></search-storage>
        </v-col>
      </v-row>
      <v-row
        v-if="
          $route.params.transaction_type == 'in' ||
          $route.params.transaction_type == 'out'
        "
      >
        <v-col>
          <search-company
            @selectedItem="updateSelCompany"
            :defaultValue="transaction_company_id"
            :readOnly="this.$route.params.status == 10 ? true : false"
          ></search-company>
        </v-col>
      </v-row>
    </v-container>
    <!-- {{items}} -->
    <v-card class="mb-12">
      <v-card-title>
        Inventory Details
        <v-spacer></v-spacer>
        <!-- <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>-->
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="items"
        :loading="loading"
        :hide-default-footer="true"
        class="elevation-1"
      >
        <template v-slot:item.product="props">
          <v-edit-dialog
            :return-value.sync="props.item.product"
            @save="save"
            @cancel="cancel"
            @open="open"
            @close="close"
          >
            {{ props.item.product }}
            <template v-slot:input>
              <v-text-field
                v-model="props.item.product"
                label="Edit"
                single-line
                counter
              ></v-text-field>
            </template>
          </v-edit-dialog>
        </template>
        <!-- <template v-slot:item.quantity="props">
                    <v-edit-dialog
                        :return-value.sync="props.item.quantity"
                        large
                        persistent
                        @save="save"
                        @cancel="cancel"
                        @open="open"
                        @close="close"
                    >
                        <div>{{ props.item.quantity }}</div>
                        <template v-slot:input>
                            <div class="mt-4 title">Update quantity</div>
                        </template>
                        <template v-slot:input>
                            <v-text-field
                                v-model="props.item.quantity"
                                label="Edit"
                                single-line
                                counter
                                autofocus
                            ></v-text-field>
                        </template>
                    </v-edit-dialog>
                </template> -->
        <template v-slot:top>
          <v-dialog v-model="invDetailsDialog" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <validation-provider rules="required" v-slot="{ errors }">
                        <v-text-field
                          v-model="editedItem.product"
                          label="Product name"
                          :error-messages="errors[0]"
                        ></v-text-field>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                      <!-- <v-text-field v-model="editedItem.location" label="Location"></v-text-field> -->
                      <search-storage
                        @selectedItem="updateSelStorage"
                        :defaultValue="
                          $route.params.transaction_type == 'transfer'
                            ? editedItem.old_storage_id
                            : editedItem.storage_id
                        "
                        :label="
                          $route.params.transaction_type == 'transfer'
                            ? 'Get From'
                            : 'Select Storage'
                        "
                      ></search-storage>
                      <search-storage
                        v-if="$route.params.transaction_type == 'transfer'"
                        @selectedItem="updateSelNewStorage"
                        :defaultValue="editedItem.storage_id"
                        label="Store to"
                      ></search-storage>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="editedItem.unit"
                        label="Unit"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="editedItem.quantity"
                        label="Quantity"
                        :error-messages="qtyError"
                        :autofocus="true"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="invDetailsClose"
                  >Cancel</v-btn
                >
                <v-btn color="blue darken-1" text @click="invDetailsSave"
                  >Update</v-btn
                >
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
        <template v-slot:body.append="{ headers }">
          <tr>
            <td :colspan="headers.length">
              <search-product
                v-if="$route.params.status != 10"
                @selectedItem="updateItems"
                :comboBox="
                  $route.params.transaction_type == 'in' ? true : false
                "
              ></search-product>
            </td>
          </tr>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
          <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
        </template>
      </v-data-table>
    </v-card>
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
import SearchProduct from "./SearchProduct";
import SearchWarehouse from "./SearchWarehouse";
import SearchStorage from "./SearchStorage";
import SearchCompany from "./SearchCompany";
export default {
  components: {
    SearchProduct,
    SearchWarehouse,
    SearchStorage,
    SearchCompany,
    ValidationProvider,
  },
  props: ["defaultWarehouse"],
  data() {
    return {
      invDetailsDialog: false,
      loading: false,
      options: {},
      snack: false,
      snackColor: "",
      snackText: "",
      headers: [
        {
          text: "ID",
          align: "start",
          sortable: false,
          value: "id",
        },
        { text: "Product", value: "product" },
        { text: "Location", value: "location" },
        { text: "QTY", value: "quantity" },
        { text: "Unit", value: "unit" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      // items: [],
      storage: null,
      new_storage: null,
      company: null,
      editedIndex: -1,
      editedItem: {
        product: "",
        location: "",
        old_storage_id: null,
        storage_id: null,
        quantity: 0,
        unit: "",
      },
      defaultItem: {
        product: "",
        location: "",
        old_storage_id: null,
        storage_id: null,
        quantity: 0,
        unit: "",
      },
      qtyError: null,
    };
  },
  mounted() {
    //
  },
  watch: {
    invDetailsDialog(val) {
      val || this.invDetailsClose();
    },
    company(val) {
      if (val) this.$emit("selectedCompany", val);
    },
  },
  methods: {
    save() {
      this.snack = true;
      this.snackColor = "success";
      this.snackText = "Data saved";
    },
    cancel() {
      this.snack = true;
      this.snackColor = "error";
      this.snackText = "Canceled";
    },
    open() {
      this.snack = true;
      this.snackColor = "info";
      this.snackText = "Dialog opened";
    },
    close() {
      // console.log("Dialog closed");
    },
    updateItems(addedItem) {
      const item = {
        id: addedItem.value,
        product: addedItem.text,
        old_storage_id: null,
        location:
          !this.storage == "undefined"
            ? this.storage.text
            : this.storages[0].text,
        storage_id:
          !this.storage == "undefined"
            ? this.storage.value
            : this.storages[0].value,
        quantity: 0,
        unit: "pc",
      };
      if (
        !this.items.some(
          (item) =>
            item.id === addedItem.value && item.product === addedItem.text
        )
      )
        this.$store.dispatch("setup/setInventory", item);
    },
    updateSelStorage(value) {
      this.storage = value;
    },
    updateSelNewStorage(value) {
      this.new_storage = value;
    },
    updateSelCompany(value) {
      this.company = value;
    },
    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.invDetailsDialog = true;
    },

    deleteItem(item) {
      const index = this.items.indexOf(item);
      // confirm("Are you sure you want to delete this item?") &&
      //     this.items.splice(index, 1);
      confirm("Are you sure you want to delete this item?") &&
        this.$store.dispatch("setup/deleteInventory", index);
    },
    invDetailsClose() {
      this.qtyError = null;
      this.invDetailsDialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    invDetailsSave() {
      if (this.editedIndex > -1) {
        if (
          this.$route.params.transaction_type == "out" ||
          this.$route.params.transaction_type == "transfer"
        ) {
          axios
            .get(
              "inventories/checkCurrentQty?product_id=" +
                this.items[this.editedIndex].id +
                "&storage_id=" +
                this.storage.value
            )
            .then((response) => {
              NProgress.done();
              if (
                response.data &&
                response.data.quantity < this.editedItem.quantity
              ) {
                this.qtyError =
                  "Unable to proceed. Your current stock in " +
                  this.storage.text +
                  " is " +
                  response.data.quantity +
                  ". You may get from another storage.";
              } else {
                if (this.$route.params.transaction_type == "transfer") {
                  this.editedItem.old_storage_id = this.storage.value;
                  this.editedItem.location = this.new_storage.text;
                  this.editedItem.storage_id = this.new_storage.value;
                } else {
                  this.editedItem.location = this.storage.text;
                  this.editedItem.storage_id = this.storage.value;
                }
                const data = {};
                data.find = this.items[this.editedIndex];
                data.replace = this.editedItem;
                this.$store.dispatch("setup/updateInventory", data);
                // Object.assign(this.items[this.editedIndex], this.editedItem);
                this.invDetailsClose();
              }
            })
            .catch((error) => {
              NProgress.done();
              alert(error);
            });
        } else {
          this.editedItem.location = this.storage.text;
          this.editedItem.storage_id = this.storage.value;
          const data = {};
          data.find = this.items[this.editedIndex];
          data.replace = this.editedItem;
          this.$store.dispatch("setup/updateInventory", data);
          // Object.assign(this.items[this.editedIndex], this.editedItem);
          this.invDetailsClose();
        }
      } else {
        this.items.push(this.editedItem);
        this.invDetailsClose();
      }
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Item" : "Edit Item";
    },
    ...mapState({
      items: (state) => state.setup.inventories,
      transaction_company_id: (state) => state.setup.transaction.company_id,
      storages: (state) => state.setup.storages,
    }),
  },
};
</script>
