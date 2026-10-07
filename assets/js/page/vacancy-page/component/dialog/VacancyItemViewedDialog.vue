<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Mark vacancy as "${item?.viewed ? "none viewed" : "viewed"}"?`'
    >
        <template #text>
            Are you sure you want to mark the vacancy as {{ item?.viewed ? '"none viewed"' : '"viewed"' }}?
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {useVacancyPageStore} from "../../store";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import handleApiResponse from "../../../../module/HandleApiResponse";
    import ErrorValidation from "../../../../model/transport-response/ErrorValidation";

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
        const viewed = !(!!props.item?.viewed); // convert to boolean, then set the opposite as was before
        const errors = new ErrorValidation();
        const data = {properties: ['viewed'], viewed};
        const result = await handleApiResponse(vacancyPageStore.patch(props.item?.id as string, data), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to update the vacancy'});
            emit('canceled');
            return;
        }

        const message = viewed ? 'Vacancy was successfully marked as "viewed"' : 'Vacancy was successfully marked as "none viewed"';
        flashStore.addSuccess({text: message});
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
