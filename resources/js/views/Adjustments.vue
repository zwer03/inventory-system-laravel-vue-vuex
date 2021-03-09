<template>
  <div>
    <crud-inventory :editableQty="true"></crud-inventory>
  </div>
</template>

<script>
import CrudInventory from "../components/CrudInventory";
export default {
  components: {
    CrudInventory,
  },
  name: "inventories",
};
</script>
