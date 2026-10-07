<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        :title='`Delete the "${item?.name}" source?`'
        :text='`Are you sure you want to delete the "${item?.name}" source?`'
        leave-open
        delete
    />
</template>

<script setup lang="ts">
    import {useSourcePageStore} from "../../store";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../../model/source/SourceInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import ErrorValidation from "../../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../../module/HandleApiResponse";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const sourcePageStore = useSourcePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(sourcePageStore.deleteItem(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to delete source'});
            emit('canceled');
            return;
        }

        flashStore.addSuccess({text:'Source was successfully deleted'});
        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
