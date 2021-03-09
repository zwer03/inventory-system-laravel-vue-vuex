<template>
  <div id="app">
    <vue-page-transition name="fade" class="h-100">
      <component :is="this.$route.meta.layout">
        <router-view :key="$route.path" />
      </component>
    </vue-page-transition>
  </div>
</template>

<script>
export default {
  name: "Application",
};
</script>

<style>
</style>
