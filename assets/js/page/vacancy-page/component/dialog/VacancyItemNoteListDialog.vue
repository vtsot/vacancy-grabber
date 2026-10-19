<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onCanceled"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter=""
        leave-open
        title="Vacancy Notes"
        :btn-confirm-active="btnConfirmActive"
        min-width="900"
        min-height="500"
    >
        <template #text>
            <vacancy-item-vacancy-notes-list :item="item"/>
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {ref} from "vue";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import {useVacancyPageStore} from "../../store";
    import VacancyItemVacancyNotesList from "../vacancy-item-notes/VacancyItemVacancyNotesList.vue";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const vacancyPageStore = useVacancyPageStore();
    const flashStore = useFlashStore();
    const emit = defineEmits(['confirmed', 'canceled', 'deleted']);
    const dialog = defineModel('dialog', {type: Boolean, default: false});
    const btnConfirmActive = ref(false);

    const onCanceled = () => {
        emit('canceled');
    };

</script>
