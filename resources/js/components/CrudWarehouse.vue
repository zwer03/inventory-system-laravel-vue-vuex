<template>
    <div>
        <!-- {{items}} -->
        <v-data-table
            :headers="headers"
            :items="items"
            :options.sync="options"
            :server-items-length="totalItems"
            :loading="loading"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>My Warehouse</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialog" max-width="800px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on">New Item</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-tabs v-model="tab">
                                    <v-tab :disabled="afterSave">Warehouse</v-tab>
                                    <v-tab :disabled="editedIndex === -1">Storage</v-tab>
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
                                                        <v-row>
                                                            <v-col
                                                                cols="12"
                                                                v-for="field in editableFields"
                                                                :key="field.value"
                                                            >
                                                                <v-switch
                                                                    v-if="field.value == 'enabled'"
                                                                    v-model="form[field.value]"
                                                                    label="Enabled"
                                                                />
                                                                <v-text-field
                                                                    v-else
                                                                    v-model="form[field.value]"
                                                                    :label="field.text"
                                                                    :error-messages="form.errors.get(field.value)"
                                                                ></v-text-field>
                                                            </v-col>
                                                        </v-row>
                                                    </form>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                    <v-tab-item>
                                        <v-card flat>
                                            <v-card-text>
                                                <crud-storage :warehouseId="warehouseId"></crud-storage>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                </v-tabs-items>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                <v-btn
                                    v-if="tab === 0 && editedIndex === -1"
                                    color="blue darken-1"
                                    text
                                    @click="next"
                                >Next</v-btn>
                                <v-btn v-else color="blue darken-1" text @click="save">Save</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.enabled="{ item }"><v-switch v-model="item.enabled" @change="toggleItem(item)"></v-switch></template>
            <template v-slot:item.actions="{ item }">
                <v-icon medium class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                <!-- <v-icon small @click="deleteItem(item)">mdi-delete</v-icon> -->
            </template>
        </v-data-table>
    </div>
</template>


<script>
import CrudStorage from "./CrudStorage";
export default {
    components: {
        CrudStorage
    },
    data: () => ({
        tab: null,
        dialog: false,
        totalItems: 0,
        items: [],
        loading: true,
        options: {},
        headers: [
            { text: "ID", value: "id", sortable: false },
            {
                text: "Warehouse Name",
                align: "start",
                value: "name"
            },
            {
                text: "Short Name",
                value: "short_name"
            },
            {
                text: "Enabled",
                value: "enabled"
            },
            // { text: "Address", value: "address" },
            { text: "Actions", value: "actions", sortable: false }
        ],
        editedIndex: -1,
        warehouseId: null,
        afterSave: false,
        form: new Form({
            id: "",
            name: "",
            short_name: "",
            // address: ""
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
                        "warehouses?page=" +
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
            this.warehouseId = item.id;
            this.editedIndex = this.items.indexOf(item);
            this.form = new Form(Object.assign({}, item));
            this.dialog = true;
        },
        toggleItem(item) {
            this.editedIndex = this.items.indexOf(item);
            this.form = new Form(Object.assign({}, item));
            this.form.put("warehouses/" + this.form.id).then(response => {
                Object.assign(this.items[this.editedIndex], response);
                this.editedIndex = -1;
            });
        },
        deleteItem(item) {
            const index = this.items.indexOf(item);
            confirm("Are you sure you want to delete this item?") &&
                this.items.splice(index, 1);
        },

        close() {
            this.dialog = false;
            this.afterSave = false;
            this.tab = null;
            setTimeout(() => {
                this.form.reset();
                this.editedIndex = -1;
            }, 300);
        },

        save() {
            if (this.editedIndex > -1) {
                this.form.put("/warehouses/" + this.form.id).then(response => {
                    Object.assign(this.items[this.editedIndex], response);
                    this.close();
                });
            } else {
                this.close();
            }
        },

        next() {
            this.form.post("/warehouses").then(response => {
                this.items.unshift(response);
                this.totalItems = this.items.length;
                this.tab++;
                this.afterSave = true;
                this.warehouseId = response.id;
            });
        }
    }
};
</script>