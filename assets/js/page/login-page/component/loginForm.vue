<template>

    <v-card-text v-if="formErrors.message">
        <form-global-error :errors="[formErrors.message as string]" />
    </v-card-text>

    <v-progress-linear indeterminate v-if="loading"></v-progress-linear>

    <v-card-text v-if="formView">

        <h1>name="login-form"</h1>
        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                :disabled="loading"
                @submit.prevent="onSubmit"
                name="login-form"
            >

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.username"
                            :rules="formRules.username as string[]"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            name="username"
                            label="Username"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.password"
                            :rules="formRules.password as string[]"
                            :loading="loading"
                            clearable
                            name="password"
                            label="Password"
                            type="password"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-btn
                            :prepend-icon="mdiLoginVariant"
                            :loading="loading"
                            :disabled="loading"
                            type="submit"
                            color="primary"
                            text="Login"
                            class="px-4"
                            variant="tonal"
                            @click="onSubmit"
                        />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, ref, useTemplateRef} from "vue";
    import {useLoginPageStore} from "../store";
    import {clearFormErrors} from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";
    import {mdiLoginVariant} from "@mdi/js";

    const loginPageStore = useLoginPageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        username: null as string || null,
        password: null as string || null,
    });
    const formErrors = ref({
        message: null,
        fields: {
            username: null,
            password: null,
        }
    });
    const formRules = computed(() => ({
        username: [() => formErrors.value.fields.username ?? null],
        password: [() => formErrors.value.fields.password ?? null],
    }))

    const onSubmit = async () => {
        loading.value = true;
        clearFormErrors(formErrors.value);
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            loading.value = true;
            return;
        }

        loading.value = true;
        const result = await loginPageStore.apiLogin(formData.value.username, formData.value.password, formErrors.value);

        if (!result || formErrors.value.message) {
            await form.value.validate();
            loading.value = false;
            return;
        }

        loading.value = false;
        emit('form:done');
    };

    // Explicitly expose the method to the parent
    defineExpose({
        onSubmit,
    });

</script>
