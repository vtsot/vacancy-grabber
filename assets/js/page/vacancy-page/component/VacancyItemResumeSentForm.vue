<template>

    <v-card-text v-if="formErrors.message">
        <form-global-error :errors="[formErrors.message as string]"/>
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
                            v-model="formData.resumeSentAt"
                            :rules="formRules.resumeSentAt"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            label="Resume Sent At"
                            type="date"
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
    import {itemsActiveOptions} from "../../../utils/itemsActiveOptions";
    import handleApiResponse, {
        clearFormErrors,
        normalizeFormToRequest,
        processApiResponse
    } from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";
    import VacancyDetailsFactory from "../../../model-factory/vacancy/VacancyDetailsFactory";
    import VacancyDetails from "../../../model/vacancy/VacancyDetails";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {formatDateUrl} from "../../../utils/DateUtils";

    const pageStore = usePageStore();
    const emit = defineEmits(['form:done', 'form:unable-load'])
    const loading = ref(false);

    const props = defineProps({
        id: {type: String, required: true}
    });

    const form = useTemplateRef('form');
    const formView = ref(true);
    const formData = ref({
        resumeSentAt: null as string || null,
    });
    const formErrors = ref({
        message: null,
        fields: {
            resumeSentAt: null,
        }
    });
    const formRules = computed(() => ({
        resumeSentAt: [() => formErrors.value.fields.resumeSentAt ?? null],
    }))

    onMounted(async () => {
        formView.value = true;
        clearFormErrors(formErrors.value);

        // get details
        await processApiResponse(pageStore.loadDetails(props.id as string), formErrors.value, new VacancyDetailsFactory())
            .then((data: VacancyDetails) => {
                formView.value = true;
                formData.value.resumeSentAt = data.resumeSentAt ? formatDateUrl(data.resumeSentAt) : null;
            }, (response: ErrorResponse) => {
                formView.value = false;
                emit('form:unable-load');
            }).finally(() => loading.value = false);
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
        const data = {properties: ['resume_sent_at'], resume_sent_at: formData.value.resumeSentAt};
        const result = await handleApiResponse(pageStore.patch(props.id as string, data), formErrors.value);

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
