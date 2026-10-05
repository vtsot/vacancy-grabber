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
                            :rules="formRules.name as string[]"
                            :loading="loading"
                            :autofocus="true"
                            clearable
                            label="Source Name"
                            type="text"
                        />
                    </v-col>
                </v-row>

                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="formData.url"
                            :rules="formRules.url as string[]"
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
                            :rules="formRules.active as string[]"
                            :loading="loading"
                            :items="itemsActiveFormOptions"
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
    import {useSourcePageStore} from "../store";
    import {itemsActiveFormOptions} from "../../../utils/itemsActiveOptions";
    import handleApiResponse, {clearFormErrors, normalizeFormToRequest, processApiResponse} from "../../../module/HandleApiResponse";
    import FormGlobalError from "../../../component/general/FormGlobalError.vue";
    import SourceDetailsFactory from "../../../model-factory/source/SourceDetailsFactory";
    import SourceDetails from "../../../model/source/SourceDetails";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";

    const sourcePageStore = useSourcePageStore();
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
        active: null as boolean || false,
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
            await processApiResponse(sourcePageStore.loadDetails(props.id), formErrors.value, new SourceDetailsFactory())
                .then((data: SourceDetails) => {
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
            ? await handleApiResponse(sourcePageStore.update(props.id, data), formErrors.value)
            : await handleApiResponse(sourcePageStore.create(data), formErrors.value);

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
