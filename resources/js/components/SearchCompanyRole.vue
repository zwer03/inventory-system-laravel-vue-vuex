<template>
    <div>
        <!-- {{items}} -->
        <v-combobox
            v-model="model"
            :items="items"
            :auto-select-first="true"
            :error-messages="errorMessage"
            label="Select Company Role"
        >
        </v-combobox>
    </div>
</template>

<script>
export default {
    props: ['defaultValue', 'errorMessage'],
    data() {
        return {
            model: null,
            items: [
                {text: 'Supplier', value: 'Supplier'},
                {text: 'Customer', value: 'Customer'},
            ]
        };
    },
    watch: {
        model(val, prev) {
            this.$emit("selectedItem", val);
        }
    },
    mounted() {
        this.model = this.defaultValue
    }
};
</script>
