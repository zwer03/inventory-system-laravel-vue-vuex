<template>
    <div>
        <v-data-table
            :headers="(withAction?headers:headersNoAction)"
            :items="items"
            :options.sync="options"
            :server-items-length="totalItems"
            :loading="loading"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>My Inventory</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialog" max-width="600px" v-if="withAction">
                        <!-- <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on">New Item</v-btn>
                        </template>-->
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-tabs v-model="tab">
                                    <v-tab >Enter Adjustment/Lost</v-tab>
                                    <v-tab >Transaction Details</v-tab>
                                </v-tabs>
                                <v-tabs-items v-model="tab">
                                    <v-tab-item>
                                        <v-card flat>
                                            <v-card-text>
                                                <v-container>
                                                    <form
                                                        @submit.prevent="save"
                                                        @keydown="form.errors.clear($event.target.name)"
                                                        novalidate
                                                    >
                                                        <v-container>
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model.number="form.quantity"
                                                                        label="Quantity"
                                                                        :error-messages="form.errors.get('quantity')"
                                                                    ></v-text-field>
                                                                </v-col>
                                                            </v-row>
                                                        </v-container>
                                                    </form>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                    <v-tab-item>
                                        <v-card flat>
                                            <v-card-text>
                                                <product-transaction-history :productId="form.product_id" :storageId="form.storage_id"></product-transaction-history>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                </v-tabs-items>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.product_id="{ item }">{{item.product.name}}</template>
            <template v-slot:item.storage_id="{ item }">{{item.storage.name}}</template>
            <template v-slot:item.alert="{ item }">
                <v-chip
                    :color="(item.quantity==0?'red':item.alert?'orange':'green' )"
                    dark
                >{{ (item.quantity==0?'Out of Stock':item.alert?'Nearly Out of Stock':'On stock' )}}</v-chip>
            </template>

            <template v-slot:item.actions="{ item }" v-if="withAction">
                <v-btn @click="editItem(item)" text><v-icon medium class="mr-2">mdi-pencil</v-icon>Edit</v-btn>
                <!-- <v-icon small @click="deleteItem(item)">mdi-delete</v-icon> -->
            </template>
        </v-data-table>
    </div>
</template>


<script>
import productTransactionHistory from './ProductTransactionHistory'
export default {
    components: {productTransactionHistory},
    props: {
        withAction: {
            type: Boolean,
            default: false
        }
    },
    data: () => ({
        tab: 0,
        dialog: false,
        totalItems: 0,
        items: [],
        loading: true,
        options: {},
        headers: [
            {
                text: "Product",
                align: "start",
                value: "product_id",
                optional: false
            },
            { text: "Storage", value: "storage_id", optional: false },
            { text: "Quantity", value: "quantity", optional: false },
            { text: "Status", value: "alert", optional: false },
            { text: "Actions", value: "actions", optional: true }
        ],
        editedIndex: -1,
        form: new Form({
            id: "",
            quantity: 0,
        })
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New Item" : "Edit Item";
        },
        editableFields(value) {
            return this.headers.filter(function(h) {
                return h.sortable !== false;
            });
        },
        headersNoAction() {
            return this.headers.filter(function(h) {
                return h.optional === false;
            });
        }
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
        }
    },

    mounted() {
        // this.getDataFromApi().then(response => {
        //     this.items = response.items;
        //     this.totalItems = response.total;
        //     if(response.total > 0){
        //       this.$emit('completed', response.total)
        //     }
        // });
    },

    methods: {
        getDataFromApi() {
            this.loading = true;
            return new Promise((resolve, reject) => {
                const { sortBy, sortDesc, page, itemsPerPage } = this.options;
                axios
                    .get(
                        "/inventories?page=" +
                            page +
                            "&items_per_page=" +
                            itemsPerPage +
                            (sortDesc.length && sortDesc
                                ? "&sort_by=" +
                                  sortBy +
                                  "&sort_desc=" +
                                  sortDesc
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
            this.form = new Form(Object.assign({}, item));
            this.dialog = true;
        },

        deleteItem(item) {
            const index = this.items.indexOf(item);
            confirm("Are you sure you want to delete this item?") &&
                this.items.splice(index, 1);
        },

        close() {
            this.dialog = false;
            this.tab = 0;
            setTimeout(() => {
                this.form.reset();
                this.editedIndex = -1;
            }, 300);
        },

        save() {
            if (this.editedIndex > -1) {
                this.form.put("/inventories/" + this.form.id).then(response => {
                    Object.assign(this.items[this.editedIndex], response.result);
                    this.close();
                });
            } else {
                this.form.post("/inventories").then(response => {
                    this.items.unshift(response);
                    this.totalItems = this.items.length;
                    this.close();
                });
            }
        }
    }
};
</script>