<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        title="Delete the vacancy?"
        text="Are you sure you want to delete the vacancy?"
        leave-open
        delete
    />
</template>

<script setup lang="ts">
    import {useVacancyPageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../module/HandleApiResponse";

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

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(vacancyPageStore.deleteItem(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to delete vacancy'});
            emit('canceled');
            return;
        }

        flashStore.addSuccess({text:'Vacancy was successfully deleted'});
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
