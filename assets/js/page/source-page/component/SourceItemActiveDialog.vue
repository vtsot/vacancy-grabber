<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title="(item?.active ? 'Deactivate' : 'Activate') + ' the source: ' + item?.name"
    />
</template>

<script setup lang="ts">
    import {usePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const active = !(!!props.item?.active); // convert to boolean, then set the opposite as was before
        const errors = new ErrorValidation();
        const result = await handleApiResponse(pageStore.itemActive(props.item?.id as string, active), errors);

        if (!result || errors.message) {
            flashMessageStore.addErrorFlash(errors.message ?? 'Unable to update source');
            emit('canceled');
            return;
        }

        const message = active ? 'Source was successfully activated' : 'Source was successfully deactivated';
        flashMessageStore.addSuccessFlash(message);
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
