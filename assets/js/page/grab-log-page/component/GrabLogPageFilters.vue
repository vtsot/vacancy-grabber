<template>

    <v-card-text>
        <div class="v-col-md-12 v-col-lg-9 v-col-xl-8 v-col-xxl-6">
            <v-form
                ref="form"
                validate-on="submit"
                @submit.prevent="onSubmit"
                :loading="loading"
            >
                <v-row>
                    <v-col cols="2">
                        <v-text-field
                            v-model="pageStore.filters.startDay"
                            :rules="formRules.startDay"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="pageStore.filters.endDay"
                            :rules="formRules.endDay"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                    </v-col>
                </v-row>

                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="pageStore.filters.query"
                            :rules="formRules.query"
                            :loading="loading"
                            :disabled="loading"
                            clearable
                            label="Search"
                            placeholder="Search"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="4">
                        <div v-if="formErrors.message" class="text-red">
                            {{ formErrors.message }}
                        </div>
                    </v-col>

                    <v-col cols="1" class="pl-10">
                        <v-btn
                            text="Search"
                            :loading="loading"
                            :disabled="loading"
                            color="secondary"
                            class="ml-5 text-right"
                            type="submit"
                        />
                    </v-col>

                    <v-col cols="1" class="pl-10">
                    </v-col>

                </v-row>

            </v-form>
        </div>
    </v-card-text>

</template>

<script setup lang="ts">
    import {computed, ref, useTemplateRef, watch} from "vue";
    import {usePageStore} from "../store";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import {clearFormErrors, parseErrorValidation} from "../../../module/HandleApiResponse";

    const flashMessageStore = useAppFlashMessageStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['filters:submit', 'filters:update', 'create:button'])
    const loading = computed(() => pageStore.loading);

    const form = useTemplateRef('form');
    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
        active: [() => formErrors.value.fields.active ?? null],
        startDay: [() => formErrors.value.fields.startDay ?? null],
        endDay: [() => formErrors.value.fields.endDay ?? null],
    }));

    const formErrors = ref({
        message: null,
        fields: {
            query: null,
            active: null,
            startDay: null,
            endDay: null,
        },
    });
    watch(
        () => pageStore.errors,
        async () => {
            parseErrorValidation(pageStore.errors, formErrors.value);
            await form.value.validate();
        },
        {deep: true},
    );

    const onUpdate = async () => {
        clearFormErrors(formErrors.value);
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            return;
        }

        emit('filters:update');
    };

    const onSubmit = async () => {
        form.value.resetValidation();
        const {valid: formIsValid} = await form.value.validate();

        if (!formIsValid) {
            flashMessageStore.addErrorFlash(formErrors.value.message);
            return;
        }

        emit('filters:submit');
    };

</script>
