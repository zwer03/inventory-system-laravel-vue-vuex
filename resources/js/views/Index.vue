<template>
    <div>
        <public v-if="!isLogged"></public>
        <baseline v-else></baseline>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import Baseline from "../layout/Baseline";
import Public from "../layout/Public";
export default {
    components: {
        Baseline,
        Public
    },
    computed: {
        ...mapGetters("auth", {
            isLogged: "isLogged",
        })
    },
    mounted() {
    }
};
</script>
