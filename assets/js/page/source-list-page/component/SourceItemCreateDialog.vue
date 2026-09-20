<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
    >
        <template #title>Create a new Source</template>
        <template #text>
            <source-details-form
                ref="details-form-ref"
                @form:done="onDetailsFormDone"
            />
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {ref, useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import SourceDetailsForm from "./SourceDetailsForm.vue";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });
    const detailsFormRef = useTemplateRef('details-form-ref');

    const onConfirmed = () => {
        detailsFormRef.value?.onSubmit();
    };

    const onDetailsFormDone = () => {
        flashMessageStore.addSuccessFlash('Source was successfully created');
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
