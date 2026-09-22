<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        :title='`Delete the "${item?.name}" vacancy?`'
        :text='`Are you sure you want to delete the "${item?.name}" vacancy?`'
        leave-open
        delete
    />
</template>

<script setup lang="ts">
    import {usePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../module/HandleApiResponse";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(pageStore.itemDelete(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashMessageStore.addErrorFlash(errors.message ?? 'Unable to delete vacancy');
            emit('canceled');
            return;
        }

        flashMessageStore.addSuccessFlash('Vacancy was successfully deleted');
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
