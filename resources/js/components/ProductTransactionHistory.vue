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
          <v-toolbar-title>Product Transaction History</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
        </v-toolbar>
      </template>
      <template v-slot:item.transaction_type="{ item }">{{
        item.transaction.transaction_type
      }}</template>
      <template v-slot:item.status="{ item }">
        <v-chip
          :color="item.transaction.status == 5 ? 'orange' : 'green'"
          dark
          >{{ item.transaction.status == 5 ? "ongoing" : "completed" }}</v-chip
        >
      </template>
      <template v-slot:item.storage_id="{ item }">{{
        item.storage.name
      }}</template>
      <template v-slot:item.quantity="{ item }">{{ item.quantity }}</template>
      <template v-slot:item.processed_by="{ item }">{{
        item.transaction.processed_by.name
      }}</template>
    </v-data-table>
  </div>
</template>


<script>
export default {
  props: ["productId", "storageId"],
  data: () => ({
    dialog: false,
    totalItems: 0,
    items: [],
    loading: true,
    options: {},
    headers: [
      // { text: "Transaction ID", value: "id", sortable: false },
      {
        text: "Type",
        value: "transaction_type",
        sortable: false,
      },
      {
        text: "Status",
        value: "status",
        sortable: false,
      },
      { text: "Storage", value: "storage_id", sortable: false },
      { text: "QTY", value: "quantity", sortable: false },
      { text: "User", value: "processed_by", sortable: false },
      { text: "Date", value: "created_at", sortable: false },
    ],
  }),

  computed: {},

  watch: {
    productId(val) {
      if (val)
        this.getDataFromApi().then((response) => {
          this.items = response.items;
          this.totalItems = response.total;
        });
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

  mounted() {},

  methods: {
    getDataFromApi() {
      this.loading = true;
      return new Promise((resolve, reject) => {
        const { sortBy, sortDesc, page, itemsPerPage } = this.options;
        axios
          .get(
            "getProductTransactions?page=" +
              page +
              "&items_per_page=" +
              itemsPerPage +
              "&product_id=" +
              this.productId +
              "&storage_id=" +
              this.storageId
          )
          .then((response) => {
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
                total,
              });
            }, 1000);
          });
      });
    },
  },
};
</script>