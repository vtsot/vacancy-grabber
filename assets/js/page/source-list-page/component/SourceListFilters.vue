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
                            v-model="formData.startDay"
                            :rules="formRules.startDay"
                            :loading="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field
                            v-model="formData.endDay"
                            :rules="formRules.endDay"
                            :loading="loading"
                            clearable
                            type="date"
                            label="Start Day"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="formData.active"
                            :rules="formRules.active"
                            :loading="loading"
                            :items="ItemsActiveOptions"
                            clearable
                            label="Active"
                            placeholder="All"
                            @update:model-value="onUpdate()"
                        />
                    </v-col>
                </v-row>

                <v-divider class="my-2"/>

                <v-row>
                    <v-col cols="6">
                        <v-text-field
                            v-model="formData.query"
                            :rules="formRules.query"
                            :loading="loading"
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

                    <v-col cols="1" class="pr-10">
                        <v-btn
                            text="Search"
                            :loading="loading"
                            :disabled="loading"
                            color="secondary"
                            class="ml-5 text-right"
                            type="submit"
                        />
                    </v-col>

                    <v-col cols="1" class="pr-10">
                        <v-btn
                            text="Create"
                            :loading="loading"
                            :disabled="loading"
                            color="primary"
                            class="ml-5 text-right"
                            @click="$emit('create:button')"
                        />
                    </v-col>

                </v-row>

            </v-form>
        </div>
    </v-card-text>

</template>

<script setup>
    import {computed, ref, useTemplateRef, watch} from "vue";
    import {useSourceListPageStore} from "../store";
    import {ItemsActiveOptions} from "../../../utils/ItemsActiveOptions";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import {clearFormErrors, parseErrorValidation} from "../../../module/HandleApiResponse";

    const flashMessageStore = useAppFlashMessageStore();
    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['filters:submit', 'create:button'])
    const loading = computed(() => sourceListPageStore.loading);

    const form = useTemplateRef('form');
    const formData = ref({
        query: null,
        active: null,
        startDay: null,
        endDay: null,
    });
    const formErrors = ref({
        message: null,
        fields: {
            query: null,
            active: null,
            startDay: null,
            endDay: null,
        },
    });
    const formRules = computed(() => ({
        query: [() => formErrors.value.fields.query ?? null],
        active: [() => formErrors.value.fields.active ?? null],
        startDay: [() => formErrors.value.fields.startDay ?? null],
        endDay: [() => formErrors.value.fields.endDay ?? null],
    }))

    watch(
        () => sourceListPageStore.errors,
        async () => {
            parseErrorValidation(sourceListPageStore.errors, formErrors.value);
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

        sourceListPageStore.filters.page = 1;
        sourceListPageStore.filters.query = formData.value.query;
        sourceListPageStore.filters.active = formData.value.active;
        sourceListPageStore.filters.startDay = formData.value.startDay;
        sourceListPageStore.filters.endDay = formData.value.endDay;

        emit('filters:submit');
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
