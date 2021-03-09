<template>
  <b-overlay :show="isLoading">
    <b-container>
      <!-- {{fields}} -->
      <b-form
        @submit.prevent="onSubmit"
        @keydown="form.errors.clear($event.target.name)"
        novalidate
      >
        <b-form-group
          v-for="field in fields"
          :key="field.key"
          :description="field.inputText"
        >
          <b-form-checkbox
            switch
            v-model="form[field.key]"
            v-if="field.type == 'checkbox'"
            value="1"
            unchecked-value="0"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :autofocus="field.autofocus"
          >
            {{ field.placeholder }}
          </b-form-checkbox>
          <b-form-radio-group
            v-model="form[field.key]"
            v-else-if="field.type == 'radio'"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :name="form.name"
            :options="field.options"
            :autofocus="field.autofocus"
            buttons
          />
          <b-form-select
            v-model="form[field.key]"
            v-else-if="field.type == 'select'"
            :placeholder="field.placeholder"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :options="field.options"
            :autofocus="field.autofocus"
          />
          <b-form-file
            v-model="form[field.key]"
            v-else-if="field.type == 'file'"
            :placeholder="field.placeholder"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :autofocus="field.autofocus"
          />
          <b-form-textarea
            v-model="form[field.key]"
            v-else-if="field.type == 'textarea'"
            :placeholder="field.placeholder"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :autofocus="field.autofocus"
          />
          <b-form-input
            v-model="form[field.key]"
            v-else
            :placeholder="field.placeholder"
            :size="field.size"
            :state="form.errors.has(field.key) ? false : null"
            :autofocus="field.autofocus"
          />
          <b-form-invalid-feedback
            :state="form.errors.has(field.key) ? false : null"
            v-text="form.errors.get(field.key)"
          />
        </b-form-group>
        <div class="form-group">
          <input
            type="submit"
            class="form-control btn-primary"
            :disabled="form.errors.any()"
          />
        </div>
      </b-form>
    </b-container>
  </b-overlay>
</template>

<script>
export default {
  props: ["id", "modelName", "fields", "fieldsInit"],
  data() {
    return {
      form: new Form(this.$props.fieldsInit),
      isLoading: false,
    };
  },
  mounted() {
    if (this.$props.id) this.getRecord();
  },
  methods: {
    onSubmit() {
      if (this.$props.id) {
        this.form
          .put("/" + this.$props.modelName + "/" + this.$props.id)
          .then((response) => {
            this.$emit("updated", response);
          });
      } else
        this.form.post("/" + this.$props.modelName).then((response) => {
          this.$emit("completed", response);
        });
    },
    getRecord() {
      this.isLoading = true;
      this.form
        .get("/" + this.$props.modelName + "/" + this.$props.id + "/edit")
        .then((response) => {
          for (let i = 0; i < this.$props.fields.length; i++) {
            this.form[this.$props.fields[i].key] =
              response[this.$props.fields[i].key];
          }
          this.isLoading = false;
        });
    },
  },
};
</script>
