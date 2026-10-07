<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        title="Add Vacancy Details"
        :btn-confirm-active="btnConfirmActive"
        min-width="700"
        min-height="550"
    >
        <template #text>

            <vacancy-details-form
                ref="form"
                :id="item.id"
                @form:done="onFormDone"
            />
<!--            <v-sheet class="d-flex" elevation="2">-->
<!--                &lt;!&ndash; vertical&ndash;&gt;-->
<!--                <v-tabs v-model="tab"-->
<!--                        direction="horizontal"-->
<!--                        inset-->
<!--                        class="mb-2"-->
<!--                >-->
<!--                    <v-tab value="viewed">Viewed</v-tab>-->
<!--                    <v-tab value="note">Note</v-tab>-->
<!--                    <v-tab value="sentAt">Sent At</v-tab>-->
<!--                    <v-tab value="status">Status</v-tab>-->
<!--                </v-tabs>-->

<!--                <v-tabs-window v-model="tab" class="flex-fill">-->
<!--                    <v-tabs-window-item value="viewed">-->
<!--                        <v-sheet class="pa-5">-->
<!--                            viewed-->
<!--                        </v-sheet>-->
<!--                    </v-tabs-window-item>-->

<!--                    <v-tabs-window-item value="note">-->
<!--                        <v-sheet class="pa-5">-->
<!--                            <vacancy-item-note-form-->
<!--                                ref="form"-->
<!--                                :id="item?.id as string"-->
<!--                                @form:done="onFormDone"-->
<!--                                @form:unableLoad="btnConfirmActive = false"-->
<!--                            />-->
<!--                        </v-sheet>-->
<!--                    </v-tabs-window-item>-->

<!--                    <v-tabs-window-item value="sentAt">-->
<!--                        <v-sheet class="pa-5">-->
<!--                            sentAt-->
<!--                        </v-sheet>-->
<!--                    </v-tabs-window-item>-->

<!--                    <v-tabs-window-item value="status">-->
<!--                        <v-sheet class="pa-5">-->
<!--                            status-->
<!--                        </v-sheet>-->
<!--                    </v-tabs-window-item>-->

<!--                </v-tabs-window>-->
<!--            </v-sheet>-->


        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {ref, useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import VacancyDetailsForm from "../form/VacancyDetailsForm.vue";

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
