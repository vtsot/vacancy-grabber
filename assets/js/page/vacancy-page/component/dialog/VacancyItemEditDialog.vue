<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        title="Update the vacancy"
        :btn-confirm-active="btnConfirmActive"
        min-width="700"
        min-height="700"
    >
        <template #text>
            <vacancy-item-form
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
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import VacancyItemForm from "../form/VacancyItemForm.vue";

    const props = defineProps({
        item: {
            type: VacancyInfo,
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
        flashStore.addSuccess({text:'Vacancy was successfully updated'});
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
