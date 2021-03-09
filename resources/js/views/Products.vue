<template>
  <div>
    <crud-product></crud-product>
  </div>
</template>

<script>
import CrudProduct from "../components/CrudProduct";
export default {
  components: {
    CrudProduct,
  },
  name: "products",
};
</script>
