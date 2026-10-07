<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        title="Vacancy notes"
        :btn-confirm-active="btnConfirmActive"
        min-width="700"
        min-height="550"
    >
        <template #text>

            <v-sheet class="d-flex" elevation="2">
                <v-tabs v-model="tab"
                        direction="vertical"
                        inset
                        class="mb-2"
                >
                    <v-tab value="one">Notes List</v-tab>
                    <v-tab value="two">Create a Note</v-tab>
                </v-tabs>

                <v-tabs-window v-model="tab" class="flex-fill">
                    <v-tabs-window-item value="one">
                        <v-sheet class="pa-5">
                            <v-data-table-server
                            />
                        </v-sheet>
<!--                        v-model:page="vacancyPageStore.filters.page as number"-->
<!--                        v-model:items-per-page="vacancyPageStore.filters.perPage as number"-->
<!--                        v-model:sort-by="tableSortBy"-->
<!--                        v-model:items-length="vacancyPageStore.paginator.total as number"-->
<!--                        :loading="loading"-->
<!--                        :must-sort="true"-->
<!--                        :multi-sort="false"-->
<!--                        :headers="headers"-->
<!--                        :items="items"-->
<!--                        :items-per-page-options="itemsPerPageOptions"-->
<!--                        @update:options="onTableOptionsUpdated"-->
                    </v-tabs-window-item>
                    <v-tabs-window-item value="two">
                        <v-sheet class="pa-5">
                            <vacancy-item-note-form
                                ref="form"
                                :id="item?.id as string"
                                @form:done="onFormDone"
                                @form:unableLoad="btnConfirmActive = false"
                            />
                        </v-sheet>
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-sheet>


        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {ref, useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import VacancyItemNoteForm from "../form/VacancyItemNoteForm.vue";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', {type: Boolean, default: false});
    const form = useTemplateRef('form');

    const btnConfirmActive = ref(true);

    const tab = ref('one')

    const onConfirmed = () => {
        form.value?.onSubmit();
    };

    const onFormDone = () => {
        flashStore.addSuccess({text: 'Vacancy was successfully updated'});
        emit('confirmed');
    }

    const onCanceled = () => {
        emit('canceled');
    };
</script>
