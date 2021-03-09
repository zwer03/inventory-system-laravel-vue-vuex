<template>
  <div>
    <v-snackbar
      v-model="snackBar"
      :top="true"
      :color="snackBarColor"
      :timeout="6000"
    >
      {{ snackBarTxt }}
      <v-btn dark text @click="snackBar = false">Close</v-btn>
    </v-snackbar>
    <v-dialog v-model="dialog" :fullscreen="true" :persistent="true">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title
          >Get Started</v-card-title
        >

        <v-card-text>
          <v-stepper v-model="e1">
            <v-stepper-header>
              <v-stepper-step :complete="e1 > 1" step="1">
                Your Warehouse
                <small
                  >Define your warehouse(s). Just click "New Item"
                  button.</small
                >
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step :complete="e1 > 2" step="2">
                Your Suppliers/Customers
                <small>Define your supplier(s) and customer(s).</small>
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step :complete="e1 > 3" step="3">
                Your Products.
                <small>Define your product(s).</small>
              </v-stepper-step>

              <v-divider></v-divider>

              <v-stepper-step step="4">Initial Inventory</v-stepper-step>
            </v-stepper-header>

            <v-stepper-items>
              <v-stepper-content step="1">
                <v-card class="mb-12" color="grey lighten-1">
                  <crud-warehouse />
                </v-card>

                <v-btn color="primary" @click="updateStep(2)">Continue</v-btn>

                <!-- <v-btn text>Cancel</v-btn> -->
              </v-stepper-content>

              <v-stepper-content step="2">
                <v-card class="mb-12" color="grey lighten-1">
                  <!-- {{products}} -->
                  <crud-company />
                </v-card>

                <v-btn color="primary" @click="updateStep(3)">Continue</v-btn>

                <v-btn @click="updateStep(1)" text>Back</v-btn>
              </v-stepper-content>

              <v-stepper-content step="3">
                <v-card class="mb-12" color="grey lighten-1">
                  <!-- {{products}} -->
                  <crud-product />
                </v-card>

                <v-btn color="primary" @click="updateStep(4)">Continue</v-btn>

                <v-btn @click="updateStep(2)" text>Back</v-btn>
              </v-stepper-content>

              <v-stepper-content step="4">
                <inventory-details></inventory-details>
                <v-btn
                  color="primary"
                  :disabled="inventories.length ? false : true"
                  @click="saveInventory"
                  >Finish</v-btn
                >

                <!-- <v-btn text>Cancel</v-btn> -->
              </v-stepper-content>
            </v-stepper-items>
          </v-stepper>
        </v-card-text>

        <!-- <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="dialog = false">Finish</v-btn>
                </v-card-actions>-->
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import CrudWarehouse from "../components/CrudWarehouse";
import CrudCompany from "../components/CrudCompany";
import CrudProduct from "../components/CrudProduct";
import InventoryDetails from "../components/InventoryDetails";
export default {
  components: {
    CrudWarehouse,
    CrudCompany,
    CrudProduct,
    InventoryDetails,
  },
  data() {
    return {
      snackBar: false,
      snackBarTxt: "",
      snackBarColor: null,
      dialog: false,
      e1: 1,
      transaction: {},
    };
  },
  watch: {
    freshSetup(val, oldVal) {
      this.dialog = val;
    },
  },
  mounted() {},
  methods: {
    updateStep(step) {
      if (step == 2) {
        axios.get("/warehouses/get").then((response) => {
          const warehouses = response.data;
          this.$store.dispatch("setup/setWarehouse", warehouses);
        });
        axios.get("/storages/get").then((response) => {
          const storages = response.data;
          this.$store.dispatch("setup/setStorage", storages);
          if (!storages.length) {
            this.snackBar = true;
            this.snackBarColor = "error";
            this.snackBarTxt = "You must add warehouse and storage first.";
            this.e1 = 1;
          }
        });
      } else if (step == 4) {
        axios.get("/products/get").then((response) => {
          const products = response.data;
          this.$store.dispatch("setup/setProduct", products);
        });
      }
      this.e1 = step;
    },
    saveInventory() {
      let filtered_inv = this.inventories.filter(function (inv) {
        return inv.quantity == 0;
      });
      if (filtered_inv.length == 0) {
        this.transaction["transaction_type"] = "adjustment";
        this.transaction["inventories"] = this.inventories;
        this.transaction["status"] = 10;
        axios
          .post("/transactions", this.transaction)
          .then((response) => {
            this.$store.dispatch("setup/resetInventory");
            this.dialog = false;
            this.snackBar = true;
            this.snackBarColor = "info";
            this.snackBarTxt = response.data.message;
          })
          .catch((error) => {
            this.snackBar = true;
            this.snackBarColor = "info";
            this.snackBarTxt = error.response.data.errors;
          });
      } else {
        this.snackBar = true;
        this.snackBarColor = "error";
        this.snackBarTxt = "You must enter item quantity.";
      }
    },
  },
  computed: {
    ...mapState({
      inventories: (state) => state.setup.inventories,
      freshSetup: (state) => state.setup.fresh,
    }),
  },
};
</script>
