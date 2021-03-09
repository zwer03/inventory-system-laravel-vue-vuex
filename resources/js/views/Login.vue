<template>
  <div>
    <v-card class="elevation-12">
      <v-form
        ref="form"
        @submit.stop.prevent="onSubmit()"
        @keydown="form.errors.clear($event.target.name)"
      >
        <v-toolbar color="primary" dark flat>
          <v-toolbar-title>IMS</v-toolbar-title>
          <v-spacer></v-spacer>
        </v-toolbar>
        <v-card-text>
          <v-text-field
            v-model="form.email"
            :error-messages="form.errors.get('email')"
            :autofocus="true"
            label="Email"
            prepend-icon="mdi-account"
          ></v-text-field>
          <v-text-field
            v-model="form.password"
            :error-messages="form.errors.get('password')"
            :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
            :type="show ? 'text' : 'password'"
            @click:append="show = !show"
            label="Password"
            name="password"
            prepend-icon="mdi-lock"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn type="submit" color="primary" :disabled="isLoading">
            {{ isLoading ? "loading..." : "Login" }}</v-btn
          >
        </v-card-actions>
      </v-form>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: false,
      isLoading: false,
      form: new Form({ email: "", password: "" }),
    };
  },
  methods: {
    async onSubmit() {
      try {
        this.isLoading = true;
        const response = await this.form.post("login", this.form);
        await this.$store.dispatch("authentication/login", response);
        this.$router.push({ name: "home" });
        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
  },
};
</script>