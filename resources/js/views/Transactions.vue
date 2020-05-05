<template>
    <div>
        <!-- {{items}} -->
        <v-snackbar v-model="snackBar" :top="true" :color="snackBarColor" :timeout="6000">
            {{ snackBarTxt }}
            <v-btn dark text @click="snackBar = false">Close</v-btn>
        </v-snackbar>
        <v-data-table
            :headers="headers"
            :items="items"
            :options.sync="options"
            :server-items-length="totalItems"
            :loading="loading"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-container>
                    <v-row>
                        <v-col>
                            <v-toolbar flat>
                                <v-toolbar-title
                                    class="text-capitalize"
                                >{{$route.params.transaction_type}} Transactions</v-toolbar-title>
                            </v-toolbar>
                        </v-col>
                        <v-col>
                            <v-select v-model="tx_status" :items="status_list" label="Status"></v-select>
                        </v-col>
                        <v-col>
                            <v-menu
                                v-model="start_date"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="tx_filter.start_date"
                                        label="Starting on"
                                        prepend-icon="mdi-calendar-range"
                                        readonly
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="tx_filter.start_date"
                                    @input="start_date = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col>
                            <v-menu
                                v-model="end_date"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="tx_filter.end_date"
                                        label="Ending on"
                                        prepend-icon="mdi-calendar-range"
                                        readonly
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="tx_filter.end_date"
                                    @input="end_date = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-container>
            </template>
            <template v-slot:item.company_id="{ item }">{{(item.company?item.company.name:null)}}</template>
            <template v-slot:item.status="{ item }">
                <v-chip
                    :color="item.status == 5?'orange':'green'"
                    dark
                >{{ item.status == 5?'ongoing':'completed' }}</v-chip>
            </template>
            <template v-slot:item.processed_by="{ item }">{{item.processed_by.name}}</template>
            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                <!-- <v-icon small @click="validateTx(item)">mdi-check</v-icon> -->
            </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="800px">
            <template v-slot:activator="{ on }">
                <v-btn absolute dark fab bottom right color="pink" v-on="on">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-card-title class="text-capitalize">
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                    <v-container>
                        <inventory-details
                            @selectedCompany="selectedCompany"
                            :defaultWarehouse="$route.params.warehouse_id"
                        ></inventory-details>
                    </v-container>
                </v-card-text>

                <v-card-actions v-if="tx_status == 5">
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="save"
                        v-if="$route.params.status != 10"
                    >Save</v-btn>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="validate"
                        v-if="$route.params.status != 10"
                    >Validate</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>


<script>
import { mapState } from "vuex";
import SearchWarehouse from "../components/SearchWarehouse";
import InventoryDetails from "../components/InventoryDetails";
import moment from "moment";
export default {
    components: { SearchWarehouse, InventoryDetails },
    props: ["warehouseId"],
    data: () => ({
        snackBar: false,
        snackBarTxt: "",
        snackBarColor: null,
        dialog: false,
        totalItems: 0,
        items: [],
        loading: true,
        options: {},
        headers: [
            { text: "ID", value: "id", sortable: false },
            // {
            //     text: "Transaction type",
            //     align: "start",
            //     value: "transaction_type"
            // },
            {
                text: "Supplier/Customer",
                value: "company_id"
            },
            {
                text: "Status",
                value: "status"
            },
            {
                text: "Processed By",
                value: "processed_by"
            },
            {
                text: "Transaction Date",
                value: "created_at"
            },
            { text: "Actions", value: "actions", sortable: false }
        ],
        editedIndex: -1,
        company: null,
        transaction: {},
        isValidate: false,
        start_date: false,
        end_date: false,
        tx_status: 5,
        tx_filter: {
            start_date: moment()
                .subtract(7, "d")
                .format("YYYY-MM-DD"),
            end_date: moment().format("YYYY-MM-DD")
        },
        status_list: [
            {
                text: "Ongoing",
                value: 5
            },
            {
                text: "Completed",
                value: 10
            }
        ]
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1
                ? "New " + this.$route.params.transaction_type + " Transaction"
                : "Edit " +
                      this.$route.params.transaction_type +
                      " Transaction";
        },
        ...mapState({
            inventories: state => state.setup.inventories
        })
    },

    watch: {
        dialog(val) {
            val || this.close();
        },
        options: {
            handler() {
                this.getDataFromApi().then(response => {
                    this.items = response.items;
                    this.totalItems = response.total;
                });
            },
            deep: true
        },
        tx_filter: {
            handler() {
                this.getDataFromApi().then(response => {
                    this.items = response.items;
                    this.totalItems = response.total;
                });
            },
            deep: true
        },
        tx_status(val) {
            this.$router.push({
                name: "transactions",
                params: {
                    transaction_type: this.$route.params.transaction_type,
                    warehouse_id: this.$route.params.warehouse_id,
                    status: val
                }
            });
            this.getDataFromApi().then(response => {
                this.items = response.items;
                this.totalItems = response.total;
            });
        }
    },

    mounted() {
        axios.get("/warehouses/get").then(response => {
            const warehouses = response.data;
            this.$store.dispatch("setup/setWarehouse", warehouses);
        });
        axios.get("/storages/get").then(response => {
            const storages = response.data;
            this.$store.dispatch("setup/setStorage", storages);
        });
        axios
            .get("/companies/get?type=" + this.$route.params.transaction_type)
            .then(response => {
                const companies = response.data;
                this.$store.dispatch("setup/setCompany", companies);
            });
        axios.get("/products/get").then(response => {
            const products = response.data;
            this.$store.dispatch("setup/setProduct", products);
        });
    },

    methods: {
        getDataFromApi() {
            this.loading = true;
            return new Promise((resolve, reject) => {
                const { sortBy, sortDesc, page, itemsPerPage } = this.options;
                axios
                    .get(
                        "/transactions?page=" +
                            page +
                            "&items_per_page=" +
                            itemsPerPage +
                            (sortDesc.length && sortDesc
                                ? "&sort_by=" +
                                  sortBy +
                                  "&sort_desc=" +
                                  sortDesc
                                : "") +
                            "&type=" +
                            this.$route.params.transaction_type +
                            "&warehouse_id=" +
                            this.$route.params.warehouse_id +
                            "&status=" +
                            this.$route.params.status +
                            (this.tx_filter.start_date
                                ? "&start_date=" + this.tx_filter.start_date
                                : "") +
                            (this.tx_filter.end_date
                                ? "&end_date=" + this.tx_filter.end_date
                                : "")
                    )
                    .then(response => {
                        // this.$store.dispatch('setup/setProduct', response.data)
                        let items = response.data.data;
                        const total = response.data.total;
                        // if (sortBy.length === 1 && sortDesc.length === 1) {
                        //     items = items.sort((a, b) => {
                        //         const sortA = a[sortBy[0]];
                        //         const sortB = b[sortBy[0]];

                        //         if (sortDesc[0]) {
                        //             if (sortA < sortB) return 1;
                        //             if (sortA > sortB) return -1;
                        //             return 0;
                        //         } else {
                        //             if (sortA < sortB) return -1;
                        //             if (sortA > sortB) return 1;
                        //             return 0;
                        //         }
                        //     });
                        // }

                        // if (itemsPerPage > 0) {
                        //     items = items.slice(
                        //         (page - 1) * itemsPerPage,
                        //         page * itemsPerPage
                        //     );
                        // }

                        setTimeout(() => {
                            this.loading = false;
                            resolve({
                                items,
                                total
                            });
                        }, 1000);
                    });
            });
        },
        editItem(item) {
            this.editedIndex = this.items.indexOf(item);
            NProgress.start();
            axios
                .get("/transactions/" + item.id + "/edit")
                .then(response => {
                    this.$store.dispatch("setup/setTransaction", response.data);
                    NProgress.done();
                })
                .catch(error => {
                    NProgress.done();
                    // alert(error);
                    this.snackBar = true;
                    this.snackBarColor = "error";
                    this.snackBarTxt = error;
                });
            this.dialog = true;
        },

        deleteItem(item) {
            const index = this.items.indexOf(item);
            confirm("Are you sure you want to delete this item?") &&
                this.items.splice(index, 1);
        },

        close() {
            this.$store.dispatch("setup/resetInventory");
            this.dialog = false;
            this.isValidate = false;
            setTimeout(() => {
                this.editedIndex = -1;
            }, 300);
        },
        validate() {
            this.isValidate = true;
            this.save();
        },
        save() {
            let filtered_inv = this.inventories.filter(function(inv) {
                return inv.quantity == 0;
            });
            this.transaction[
                "transaction_type"
            ] = this.$route.params.transaction_type;
            this.transaction["company_id"] = this.company;
            this.transaction["status"] = this.isValidate ? 10 : 5;
            this.transaction["warehouse_id"] = this.$route.params.warehouse_id;

            this.transaction["inventories"] = this.inventories;
            if (filtered_inv.length == 0) {
                if (this.editedIndex > -1) {
                    axios
                        .put(
                            "/transactions/" + this.items[this.editedIndex].id,
                            this.transaction
                        )
                        .then(response => {
                            this.snackBar = true;
                            this.snackBarColor = "info";
                            this.snackBarTxt = response.data.message;
                            this.getDataFromApi().then(response => {
                                this.items = response.items;
                                this.totalItems = response.total;
                            });
                            this.close();
                        })
                        .catch(error => {
                            this.snackBar = true;
                            this.snackBarColor = "error";
                            this.snackBarTxt = response.data.message;
                        });
                } else {
                    axios
                        .post("/transactions", this.transaction)
                        .then(response => {
                            this.snackBar = true;
                            this.snackBarColor = "info";
                            this.snackBarTxt = response.data.message;
                            this.getDataFromApi().then(response => {
                                this.items = response.items;
                                this.totalItems = response.total;
                            });
                            this.close();
                        })
                        .catch(error => {
                            this.snackBar = true;
                            this.snackBarColor = "error";
                            this.snackBarTxt = error;
                        });
                }
            } else {
                this.snackBar = true;
                this.snackBarColor = "error";
                this.snackBarTxt = "You must enter item quantity.";
            }
        },
        selectedCompany(data) {
            this.company = data.value;
        }
    }
};
</script>