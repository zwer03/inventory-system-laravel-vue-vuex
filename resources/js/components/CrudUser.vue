<template>
  <div>
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
          <v-toolbar-title>Users</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on"
                >New Item</v-btn
              >
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <form
                  @submit.prevent="save"
                  @keydown="form.errors.clear($event.target.name)"
                  novalidate
                >
                  <v-container>
                    <v-row>
                      <v-col cols="12">
                        <v-text-field
                          v-model="form.name"
                          label="Name"
                          :error-messages="form.errors.get('name')"
                        ></v-text-field>
                        <v-text-field
                          v-model="form.email"
                          label="Email"
                          :error-messages="form.errors.get('email')"
                        ></v-text-field>
                        <v-text-field
                          v-model="form.password"
                          label="Password"
                          :append-icon="showpw ? 'mdi-eye' : 'mdi-eye-off'"
                          :type="showpw ? 'text' : 'password'"
                          :error-messages="form.errors.get('password')"
                          @click:append="showpw = !showpw"
                        ></v-text-field>
                        <v-text-field
                          v-model="form.password_confirmation"
                          label="Password Confirm"
                          :append-icon="showpwc ? 'mdi-eye' : 'mdi-eye-off'"
                          :type="showpwc ? 'text' : 'password'"
                          :error-messages="
                            form.errors.get('password_confirmation')
                          "
                          @click:append="showpwc = !showpwc"
                        ></v-text-field>
                        <v-switch v-model="form.enabled" label="Enabled" />
                      </v-col>
                    </v-row>
                  </v-container>
                </form>
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
      <template v-slot:item.enabled="{ item }">
        <v-switch v-model="item.enabled" @change="toggleItem(item)"></v-switch>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon medium class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
        <!-- <v-icon small @click="deleteItem(item)">mdi-delete</v-icon> -->
      </template>
    </v-data-table>
  </div>
</template>


<script>
export default {
  data: () => ({
    dialog: false,
    showpw: false,
    showpwc: false,
    totalItems: 0,
    items: [],
    loading: true,
    options: {},
    headers: [
      { text: "ID", value: "id", sortable: false },
      {
        text: "Name",
        align: "start",
        value: "name",
      },
      { text: "Email", value: "email" },
      {
        text: "Enabled",
        value: "enabled",
      },
      { text: "Actions", value: "actions", sortable: false },
    ],
    editedIndex: -1,
    form: new Form({
      id: "",
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      enabled: "",
    }),
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Item" : "Edit Item";
    },
    editableFields(value) {
      return this.headers.filter(function (h) {
        return h.sortable !== false;
      });
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi().then((response) => {
          this.items = response.items;
          this.totalItems = response.total;
        });
      },
      deep: true,
    },
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
            "users?page=" +
              page +
              "&items_per_page=" +
              itemsPerPage +
              (sortDesc.length && sortDesc
                ? "&sort_by=" + sortBy + "&sort_desc=" + sortDesc
                : "")
          )
          .then((response) => {
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
                total,
              });
            }, 1000);
          });
      });
    },
    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      item.password = "";
      item.password_confirmation = "";
      this.form = new Form(Object.assign({}, item));
      this.dialog = true;
    },
    toggleItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.form.id = item.id;
      this.form.enabled = item.enabled;
      this.form.put("user/allow").then((response) => {
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
      setTimeout(() => {
        this.form.reset();
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      if (this.editedIndex > -1) {
        this.form.put("/users/" + this.form.id).then((response) => {
          Object.assign(this.items[this.editedIndex], response);
          this.close();
        });
      } else {
        this.form.post("/users").then((response) => {
          this.items.unshift(response);
          this.totalItems = this.items.length;
          this.close();
        });
      }
    },
  },
};
</script>