<template>
  <div>
    <v-container>
      <v-row dense v-for="warehouse in warehouses" :key="warehouse.id">
        <!-- <v-col cols="12" md="4">
                    <v-card>
                        <div class="d-flex flex-no-wrap justify-space-between">
                            <div>
                                <v-card-title class="headline">asdfasdf</v-card-title>

                                <v-card-subtitle>asdfa</v-card-subtitle>
                                <v-card-actions>
                                    <router-link
                                        :to="{ name: 'transactions', params: { transaction_type: 'in', warehouse_id: warehouse.text }}"
                                        tag="v-btn"
                                    >
                                        10
                                        <v-divider inset vertical></v-divider>To do
                                    </router-link>
                                </v-card-actions>
                            </div>
                            <div>
                                <v-icon large center>mdi-folder-download</v-icon>
                            </div>
                        </div>
                    </v-card>
                </v-col>-->
        <v-col cols="12" md="4">
          <v-card color="rgb(251, 140, 0)">
            <v-card-title class="headline">Receipts</v-card-title>
            <v-card-subtitle>{{ warehouse.text }}</v-card-subtitle>
            <v-card-actions>
              <v-btn-toggle>
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'in',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                  >{{ getTransaction(warehouse.value, "in") }}</router-link
                >
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'in',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                >
                  <v-divider inset vertical></v-divider>To do
                </router-link>
              </v-btn-toggle>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card color="rgb(76, 175, 80)">
            <v-card-title class="headline">Delivery</v-card-title>
            <v-card-subtitle>{{ warehouse.text }}</v-card-subtitle>
            <v-card-actions>
              <v-btn-toggle>
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'out',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                  >{{ getTransaction(warehouse.value, "out") }}</router-link
                >
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'out',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                >
                  <v-divider inset vertical></v-divider>For delivery
                </router-link>
              </v-btn-toggle>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card color="rgb(0, 202, 227)">
            <v-card-title class="headline">Transfer</v-card-title>
            <v-card-subtitle>{{ warehouse.text }}</v-card-subtitle>
            <v-card-actions>
              <v-btn-toggle>
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'transfer',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                  >{{
                    getTransaction(warehouse.value, "transfer")
                  }}</router-link
                >
                <router-link
                  :to="{
                    name: 'transactions',
                    params: {
                      transaction_type: 'transfer',
                      warehouse_id: warehouse.value,
                      status: 5,
                    },
                  }"
                  tag="v-btn"
                >
                  <v-divider inset vertical></v-divider>For Transfer
                </router-link>
              </v-btn-toggle>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <crud-inventory></crud-inventory>
    </v-container>
    <get-started></get-started>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import GetStarted from "../components/GetStarted";
import CrudInventory from "../components/CrudInventory";

export default {
  components: {
    GetStarted,
    CrudInventory,
  },
  data() {
    return {
      //
    };
  },
  mounted() {
    this.$store.dispatch("setup/getDashboard");
  },
  methods: {
    getTransaction(warehouse_id, transaction_type) {
      let dashboard = this.dashboard.find((tx) => {
        return (
          tx.warehouse_id == warehouse_id &&
          tx.transaction_type === transaction_type
        );
      });
      return dashboard ? dashboard.total : 0;
    },
  },
  computed: {
    ...mapState({
      dashboard: (state) => state.setup.dashboard,
      warehouses: (state) => state.setup.warehouses,
    }),
  },
};
</script>