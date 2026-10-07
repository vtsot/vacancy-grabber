<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Update the "${item?.name}" source`'
        :btn-confirm-active="btnConfirmActive"
    >
        <template #text>
            <source-details-form
                ref="form"
                :id="item?.id"
                @form:done="onFormDone"
                @form:unableLoad="btnConfirmActive = false"
            />
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {ref, useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../../model/source/SourceInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import SourceDetailsForm from "../form/SourceDetailsForm.vue";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });
    const form = useTemplateRef('form');

    const btnConfirmActive = ref(true);

    const onConfirmed = () => {
        form.value?.onSubmit();
    };

    const onFormDone = () => {
        flashStore.addSuccess({text:'Source was successfully created'});
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
