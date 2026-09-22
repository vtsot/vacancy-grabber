<template>

    <v-card-text v-if="formErrors.message">
        <form-global-error :errors="[formErrors.message as string]" />
    </v-card-text>

    <v-progress-linear indeterminate v-if="loading"></v-progress-linear>

    <v-card-text v-if="formView">

        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                :disabled="loading"
                @submit.prevent="onSubmit"
            >

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.name"
                            :rules="formRules.name"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            label="Vacancy Name"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.url"
                            :rules="formRules.url"
                            :loading="loading"
                            clearable
                            label="URL"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-select
                            v-model="formData.active"
                            :rules="formRules.active"
                            :loading="loading"
                            :items="ItemsActiveOptions"
                            clearable
                            label="Active"
                        />
                    </v-col>
                </v-row>
            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef} from "vue";
    import {usePageStore} from "../store";
    import {ItemsActiveOptions} from "../../../utils/ItemsActiveOptions";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import handleApiResponse, {clearFormErrors, normalizeFormToRequest, processApiResponse} from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";
    import VacancyDetailsFactory from "../../../model-factory/vacancy/VacancyDetailsFactory";
    import VacancyDetails from "../../../model/vacancy/VacancyDetails";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";

    const flashMessageStore = useAppFlashMessageStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const props = defineProps({
        id: {type: String, required: false}
    });

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        name: null as string || null,
        url: null as string || null,
        active: null as boolean | true,
    });
    const formErrors = ref({
        message: null,
        fields: {
            name: null,
            url: null,
            active: null,
        }
    });
    const formRules = computed(() => ({
        name: [() => formErrors.value.fields.name ?? null],
        url: [() => formErrors.value.fields.url ?? null],
        active: [() => formErrors.value.fields.active ?? null],
    }))

    onMounted(async () => {
        if (props.id) {

            formView.value = true;
            clearFormErrors(formErrors.value);

            // get details
            await processApiResponse(pageStore.loadDetails(props.id), formErrors.value, new VacancyDetailsFactory())
                .then((data: VacancyDetails) => {
                    formView.value = true;
                    formData.value.name = data.name;
                    formData.value.url = data.url;
                    formData.value.active = !!data.active;
                }, (response: ErrorResponse) => {
                    formView.value = false;
                    emit('form:unable-load');
                }).finally(() => loading.value = false);
        }
    });

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
        const data = normalizeFormToRequest(formData.value);
        const result = props.id
            ? await handleApiResponse(pageStore.updateDetails(props.id, data), formErrors.value)
            : await handleApiResponse(pageStore.createDetails(data), formErrors.value);

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
