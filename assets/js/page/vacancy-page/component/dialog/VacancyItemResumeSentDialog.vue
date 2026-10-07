<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Mark vacancy as "${item?.viewed ? "none resume sent" : "resume sent"}"?`'
    >
        <template #text>
            <v-card-text>
                Are you sure you want to mark the vacancy as {{ item?.viewed ? '"none resume sent"' : '"resume sent"' }}?
            </v-card-text>
            <vacancy-item-resume-sent-form
                ref="form"
                :id="item.id"
                @form:done="onFormDone"
            />
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {useVacancyPageStore} from "../../store";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import VacancyItemResumeSentForm from "../form/VacancyItemResumeSentForm.vue";
    import {useTemplateRef} from "vue";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const vacancyPageStore = useVacancyPageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });
    const form = useTemplateRef('form');

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
