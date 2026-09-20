<template>
    <v-card-text v-if="formErrors.global">
        <form-global-error :errors="formErrors.global" />
    </v-card-text>

    <v-card-text>
        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                :disabled="loading"
                @submit.prevent="onSubmit"
            >
                <v-row>
                    <v-col>
                        <v-select
                            v-model="formData.environment"
                            label="Environment"
                            :rules="formValidationRules.environment"
                            :items="customerEnvironmentSelectOptions"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-input
                            :rules="formValidationRules.avatar"
                        >
                            <v-badge v-if="formData.avatar && !loading" location="top right">
                                <template #badge>
                                    <v-icon
                                        :icon="mdiDelete"
                                        @click="formData.avatar = null"
                                    />
                                </template>
                                <v-img :src="formData.avatar" width="150" />
                            </v-badge>

                            <file-upload-input
                                v-else
                                ref="fileUploadInput"
                                file-types="image/jpeg, image/png, image/gif, image/webp"
                                :file-endpoint="runtimeConfig.public.imageUploaderUrl"
                                :min-amount="0"
                                :max-amount="1"
                                label="Avatar"
                                @complete="onFileUploadComplete"
                            />
                        </v-input>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.firstName"
                            :rules="formValidationRules.firstName"
                            :autofocus="true"
                            clearable
                            :clear-icon="mdiCloseCircleOutline"
                            label="First Name"
                            type="text"
                            tabindex="1"
                            variant="outlined"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.lastName"
                            :rules="formValidationRules.lastName"
                            clearable
                            :clear-icon="mdiCloseCircleOutline"
                            label="Last Name"
                            type="text"
                            tabindex="1"
                            variant="outlined"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.email"
                            :rules="formValidationRules.email"
                            disabled
                            autocomplete="off"
                            clearable
                            :clear-icon="mdiCloseCircleOutline"
                            label="E-mail"
                            type="email"
                            tabindex="1"
                            variant="outlined"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.phone"
                            :rules="formValidationRules.phone"
                            clearable
                            :clear-icon="mdiCloseCircleOutline"
                            label="Phone"
                            type="text"
                            tabindex="1"
                            variant="outlined"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-btn
                            :text="props.customerId ? 'Update' : 'Create'"
                            block
                            type="submit"
                            :loading="loading ? 'onPrimary' : false"
                            :disabled="loading"
                        />
                    </v-col>

                    <v-col>
                        <v-btn
                            text="Cancel"
                            to="/customers"
                            :prepend-icon="mdiAccountCancel"
                            class="mr-3 text-error"
                            variant="outlined"
                            block
                            :disabled="loading"
                        />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-card-text>
</template>

<script setup lang="ts">
    // import type { ClientValidationErrorInterface, ServerValidationErrorInterface } from '~/types/validationErrorTypes';
    // import { mdiAccountCancel, mdiCloseCircleOutline, mdiDelete } from '@mdi/js';
    // import type ErrorResponse from '~/model/transport-response/ErrorResponse';
    // import type CustomerDetails from '~/model/customer/CustomerDetails';
    // import { CustomerEnvironment, customerEnvironmentSelectOptions } from '~/types/customer/CustomerEnvironment';
    //
    // const runtimeConfig = useRuntimeConfig();
    //
    import {onMounted, ref, useTemplateRef} from "vue";
    import SourceDetails from "../../../model/source/SourceDetails";
    import {ClientValidationErrorInterface} from "../../../types/ValidationErrorTypes";
    import {clearFormErrors} from "../../../utils/FormErrors";
    import {useSourceListPageStore} from "../store";

    const sourceListPageStore = useSourceListPageStore();

    const props = defineProps({
        sourceId: {
            type: String,
            default: null,
        },
    });

    const loading = ref(false);
    const source = ref({} as SourceDetails);

    const form = useTemplateRef('form');
    const formData = ref({
        name: null as string | null,
        url: null as string | null,
        active: null as boolean | null,
    });

    const formErrors = ref<ClientValidationErrorInterface>({
        global: null,
        fields: {
            name: null,
            url: null,
            active: null,
        },
    });

    const formValidationRules = ref({
        name: [
            () => formErrors.value.fields!.name ?? true,
        ],
        url: [
            () => formErrors.value.fields!.url ?? true,
        ],
        active: [
            () => formErrors.value.fields!.active ?? true,
        ],
        // firstName: [
        //     () => formErrors.value.fields!.firstName ?? true,
        //     (value: undefined) => !!value || 'First Name is required',
        // ],
        // lastName: [
        //     () => formErrors.value.fields!.lastName ?? true,
        //     (value: undefined) => !!value || 'Last Name is required',
        // ],
        // email: [
        //     () => formErrors.value.fields!.email ?? true,
        //     // (value: undefined) => !!value || 'E-mail is required',
        // ],
        // phone: [
        //     () => formErrors.value.fields!.phone ?? true,
        // ],
    });

    const onSubmit = async () => {
        loading.value = true;

        clearFormErrors(formErrors);
        const { valid: formIsValid } = await form.value!.validate();

        if (!formIsValid) {
            loading.value = false;

            return;
        }

        // try {
        //     if (fileUploader.value!.getFilesAmount() > 0) {
        //         await fileUploader.value!.upload();
        //     }
        // } catch (error) {
        //     if (error instanceof Error) {
        //         formErrors.value.fields!.avatar = error.message;
        //         const { valid: formIsValid } = await form.value!.validate();
        //
        //         if (!formIsValid) {
        //             loading.value = false;
        //
        //             return;
        //         }
        //     }
        // }

        // // update
        // await useApi()
        //     .putCustomer(props.customerId, {
        //         environment: formData.value.environment,
        //         avatar: formData.value.avatar,
        //         first_name: formData.value.firstName,
        //         last_name: formData.value.lastName,
        //         email: formData.value.email,
        //         phone: formData.value.phone,
        //     }).then(() => {
        //         navigateTo('/customers');
        //     }, (error: ServerValidationErrorInterface) => {
        //         parseFormErrors(error, formErrors);
        //         form.value!.validate();
        //         formErrors.value.global = [error.message ?? 'Error try later'];
        //     }).finally(() => {
        //         loading.value = false;
        //     });
    };

    // const onFileUploadComplete = (result: string[]) => {
    //     formData.value.avatar = result[0];
    // };

    onMounted(() => {
        if (props.sourceId) {
            loadCustomerDetails(props.sourceId);
        }
    });

    const loadCustomerDetails = (sourceId: string) => {
        loading.value = true;
        clearFormErrors(formErrors);

        sourceListPageStore.loadSourceDetails(sourceId);

        // useApi()
        //     .getCustomerDetails(props.customerId as string)
        //     .then((data: CustomerDetails) => {
        //         customer.value = data;
        //
        //         formData.value.environment = customer.value.environment;
        //         formData.value.firstName = customer.value.firstName;
        //         formData.value.lastName = customer.value.lastName;
        //         formData.value.email = customer.value.email;
        //         formData.value.phone = customer.value.phone;
        //         formData.value.avatar = customer.value.avatar;
        //     }, (error: ErrorResponse) => {
        //         formErrors.value.global = [error.message ?? 'Error try later'];
        //         customer.value = {} as CustomerDetails;
        //     })
        //     .finally(() => {
        //         loading.value = false;
        //     });
    };

</script>
