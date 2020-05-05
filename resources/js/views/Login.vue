<template>
    <div>
        <v-card class="elevation-12">
            <v-form
                ref="form"
                @submit.prevent="onSubmit"
                @keydown="form.errors.clear($event.target.name)"
            >
                <v-toolbar color="primary" dark flat>
                    <v-toolbar-title>Login form</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-card-text>
                    <input type="hidden" name="_token" :value="csrf_token" />
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
                    <v-btn type="submit" color="primary" @click="onSubmit">Login</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </div>
</template>

<script>
export default {
    // inject: ['$validator'],
    data: () => ({
        show: false,
        form: new Form({ email: "", password: "" })
    }),
    computed: {
        csrf_token() {
            let token = document.head.querySelector('meta[name="csrf-token"]');
            return token.content;
        }
    },
    methods: {
        onSubmit() {
            this.$store
                .dispatch("auth/login", this.form)
                .then(() => {
                    this.$router.push({ name: "home" });
                })
                .catch(err => {
                    alert(err);
                });
        }
    }
};
</script>