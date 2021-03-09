<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" app>
      <v-list dense>
        <router-link to="/" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-monitor-dashboard</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <router-link to="/inventories" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-home-analytics</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Inventory Control</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <router-link to="/products" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-cart</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Products</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <router-link to="/companies" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-face-agent</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Supplier/Customer</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <router-link to="/warehouses" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-warehouse</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Warehouses</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <router-link to="/users" tag="v-list-item" link>
          <v-list-item-action>
            <v-icon>mdi-account</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Users</v-list-item-title>
          </v-list-item-content>
        </router-link>
        <!-- <v-list-item @click="logout">
                    <v-list-item-action>
                        <v-icon>mdi-logout</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item-content>
        </v-list-item>-->
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app color="indigo" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>Inventory System</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu bottom left>
        <template v-slot:activator="{ on, attrs }">
          <v-btn dark icon v-bind="attrs" v-on="on">
            <v-icon>mdi-bell</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item v-for="(item, i) in items" :key="i">
            <v-list-item-title>{{ item.product_id }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-btn @click="logout" text> <v-icon>mdi-logout</v-icon>Logout </v-btn>
    </v-app-bar>

    <v-content>
      <v-container>
        <vue-page-transition name="fade" class="h-100">
          <router-view :key="$route.path" />
        </vue-page-transition>
      </v-container>
    </v-content>
    <v-footer color="indigo" app>
      <span class="white--text">&copy; 2019</span>
    </v-footer>
  </v-app>
</template>

<script>
import { mapState } from "vuex";
export default {
  mounted() {
    // Echo.private("transaction-validated").listen(
    //   "TransactionValidated",
    //   (e) => {
    //     console.log(e);
    //     console.log("laravel echo listener here");
    //   }
    // );

    this.$store.dispatch("setup/getCriticalLevelProducts");
  },
  props: {
    source: String,
  },
  data: () => ({
    drawer: null,
    // items: [
    //   { title: "Click Me" },
    //   { title: "Click Me" },
    //   { title: "Click Me" },
    //   { title: "Click Me 2" },
    // ],
  }),

  methods: {
    logout() {
      this.$store.dispatch("authenthication/logout");
    },
  },
  computed: {
    ...mapState({
      items: (state) => state.setup.at_critical_level_products,
    }),
  },
};
</script>