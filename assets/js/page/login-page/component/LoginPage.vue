<template>
    <v-row justify="center">
        <v-col cols="12" md="6">
            <v-card variant="outlined" class="mx-auto" min-height="350px" max-width="500px">
                <v-card-title class="text-center mt-5">Login</v-card-title>
                <v-card-item>
                    <login-form/>
                </v-card-item>
            </v-card>
        </v-col>
    </v-row>
</template>

<script setup lang="ts">
    import LoginForm from "./loginForm.vue";
</script>
