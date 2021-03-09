<template>
  <div>
    <public v-if="!user"></public>
    <baseline v-else></baseline>
  </div>
</template>

<script>
import Baseline from "../layout/Baseline";
import Public from "../layout/Public";
export default {
  components: {
    Baseline,
    Public,
  },
  computed: {
    user() {
      return this.$store.state.authentication.user;
    },
  },
  mounted() {},
};
</script>
