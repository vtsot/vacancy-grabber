<template>
    <confirm-dialog
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
    import ConfirmDialog from "../../../component/general/dialog/ConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = () => {
        const active = !!props.item?.active; // convert to boolean
        sourceListPageStore.itemActive(props.item?.id as string, !active); //send the opposite active value
        emit('confirmed');
        // dialog.value = false;

        // sourceListPageStore.loadSourceCollection();

        return;
        // dialog.value = false;

        Promise.all([
            sourceListPageStore.itemActive(props.item?.id as string, !active), //send the opposite active value
            sourceListPageStore.loadSourceDetails(props.item?.id as string),
            sourceListPageStore.loadSourceCollection(),
        ])
            .then(() => {
                emit('confirmed');
                //dialog.value = false;
            });

    };

    const onCanceled = () => {
        emit('canceled');
        //dialog.value = false;
    };
</script>
