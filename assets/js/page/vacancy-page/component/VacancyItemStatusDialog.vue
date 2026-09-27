<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        title="Update status"
    >
        <template #text>
            <v-card-text>
                Update resume status
            </v-card-text>
            <vacancy-item-status-form
                ref="form"
                :id="item.id"
                @form:done="onFormDone"
            />
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {useVacancyPageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import VacancyItemResumeSentForm from "./VacancyItemResumeSentForm.vue";
    import {useTemplateRef} from "vue";
    import VacancyDetailsForm from "./VacancyDetailsForm.vue";
    import VacancyItemStatusForm from "./VacancyItemStatusForm.vue";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const vacancyPageStore = useVacancyPageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });
    const form = useTemplateRef('form');

    const onConfirmed = () => {
        form.value?.onSubmit();
    };

    const onFormDone = () => {
        flashStore.addSuccess({text:'Vacancy was successfully updated'});
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
