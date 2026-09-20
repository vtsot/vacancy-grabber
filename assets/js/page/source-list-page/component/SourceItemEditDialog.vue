<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
    />
</template>

<script setup lang="ts">
    import {useSourceListPageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        //await sourceListPageStore.itemDelete(props.item?.id as string);

        if (sourceListPageStore.errors.message) {
            flashMessageStore.addErrorFlash(sourceListPageStore.errors.message);
            emit('canceled');
            return;
        }

        flashMessageStore.addSuccessFlash('Source was successfully updated');

        emit('confirmed');
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
