<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title="(item?.active ? 'Deactivate' : 'Activate') + ' the vacancy: ' + item?.name"
    />
</template>

<script setup lang="ts">
    import {usePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const active = !(!!props.item?.active); // convert to boolean, then set the opposite as was before
        const errors = new ErrorValidation();
        const result = await handleApiResponse(pageStore.itemActive(props.item?.id as string, active), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to update vacancy'});
            emit('canceled');
            return;
        }

        const message = active ? 'Vacancy was successfully activated' : 'Vacancy was successfully deactivated';
        flashStore.addSuccess({text: message});
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
