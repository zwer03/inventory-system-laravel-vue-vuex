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
                </v-list-item> -->
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app color="indigo" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-toolbar-title>Inventory System</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn @click="logout" text><v-icon>mdi-logout</v-icon>Logout</v-btn>
        </v-app-bar>

        <v-content>
            <v-container>
                <vue-page-transition name="fade">
                    <router-view></router-view>
                </vue-page-transition>
            </v-container>
        </v-content>
        <v-footer color="indigo" app>
            <span class="white--text">&copy; 2019</span>
        </v-footer>
    </v-app>
</template>

<script>
export default {
    props: {
        source: String
    },
    data: () => ({
        drawer: null
    }),

    methods: {
        logout() {
            this.$store.dispatch("auth/logout");
        }
    }
};
</script>