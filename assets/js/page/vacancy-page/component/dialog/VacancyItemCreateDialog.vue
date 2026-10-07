<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        title="Create a new vacancy"
        min-width="700"
        min-height="700"
    >
        <template #text>
            <vacancy-item-form
                ref="form"
                @form:done="onFormDone"
            />
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import vacancyItemForm from "../form/VacancyItemForm.vue";

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

    const onConfirmed = () => {
        form.value?.onSubmit();
    };

    const onFormDone = () => {
        flashStore.addSuccess({text:'Vacancy was successfully created'});
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
