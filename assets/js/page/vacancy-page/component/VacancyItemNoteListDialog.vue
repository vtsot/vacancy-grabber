<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onCanceled"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onCanceled"
        leave-open
        title="Vacancy Notes"
        :btn-confirm-active="btnConfirmActive"
        min-width="800"
        min-height="700"
    >
        <template #text>
            <v-data-table-server
                v-model:page="filters.page as number"
                v-model:items-per-page="filters.perPage as number"
                v-model:items-length="paginator.total as number"
                v-model:sort-by="tableSortBy"
                :loading="loading"
                :must-sort="true"
                :multi-sort="false"
                :headers="headers"
                :items="items"
                :items-per-page-options="itemsPerPageOptions"
                @update:options="onTableOptionsUpdated"
            >
                <!-- hide bottom -->
                <!-- <template #bottom></template>-->

                <template #item.notedAt="{ item }">
                    {{ formatDateTime(item.notedAt) }}
                </template>

                <template #item.description="{ item }">
                    <div class="mt-5 mb-5" v-html="nl2br(item.description)"></div>
                </template>

                <template #item.action-delete="{ item, index }">
                    <v-btn
                        text="Delete"
                        class="ml-1"
                        color="error"
                        variant="tonal"
                        :prepend-icon="mdiDelete"
                        @click.prevent="handleConfirmDelete(item)"
                    />
                </template>

            </v-data-table-server>
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {computed, onMounted, ref, useTemplateRef} from "vue";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {formatDateTime, formatDateUrl} from "../../../utils/DateUtils";
    import {useVacancyPageStore} from "../store";
    import VacancyNoteCollectionFactory from "../../../model-factory/vacancy/VacancyNoteCollectionFactory";
    import VacancyNoteCollection from "../../../model/vacancy/VacancyNoteCollection";
    import handleApiResponse, {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import VacancyNote from "../../../model/vacancy/VacancyNote";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import _debounce from "lodash/debounce";
    import VacancyInfoCollectionFactory from "../../../model-factory/vacancy/VacancyInfoCollectionFactory";
    import VacancyInfoCollection from "../../../model/vacancy/VacancyInfoCollection";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import Paginator from "../../../model/transport-response/Paginator";
    import VacancyInfoFilters from "../../../model/vacancy/VacancyInfoFilters";
    import VacancyNoteFilters from "../../../model/vacancy/VacancyNoteFilters";
    import {nl2br} from "../../../utils/HtmlUtils";
    import {mdiDelete} from "@mdi/js";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const vacancyPageStore = useVacancyPageStore();
    const flashStore = useFlashStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', {type: Boolean, default: false});
    const form = useTemplateRef('form');
    const btnConfirmActive = ref(false);

    const loading = computed(() => vacancyPageStore.loading);
    const items = ref<VacancyNote[]>([] as VacancyNote[]);

    const filters = ref<VacancyNoteFilters>(new VacancyNoteFilters(1, 5));
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref({} as object | null);
    const appliedFilters = ref({} as object | null);

    const loadVacancyNoteCollection = _debounce(async () => {
        clearFormErrors(errors.value);
        await processApiResponse(vacancyPageStore.loadVacancyNoteCollection(props.item?.id as string, filters.value.normalizeUrlParams()), errors.value, new VacancyNoteCollectionFactory())
            .then((data: VacancyInfoCollection) => {
                // update the originally loaded items
                items.value = data.items
                paginator.value = data.paginator;
                availableSortBy.value = data.filters?.availableSortBy || [];
                availableFilters.value = {...vacancyPageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);


    onMounted(async () => {
        await loadVacancyNoteCollection();
    });

    // const onConfirmed = () => {
    //     form.value?.onSubmit();
    // };
    //
    // const onFormDone = () => {
    //     flashStore.addSuccess({text:'Vacancy was successfully updated'});
    //     emit('confirmed');
    // }

    const onCanceled = () => {
        emit('canceled');
    };

    const tableSortBy = ref(filters.value.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        filters.value.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        loadVacancyNoteCollection();
    };

    const headers = computed(() => [
        {title: 'Noted At', key: 'notedAt', sortable: true, width: '150px'},
        {title: 'Note', key: 'description', sortable: false},

        //{title: 'Actions', key: 'action-edit', sortable: false, width: '100px'},
        {title: 'Actions', key: 'action-delete', sortable: false, width: '100px'},
    ]);


    const handleConfirmDelete = async (item: VacancyNote) => {
        if (window.confirm("Do you really want to delete the note?")) {
            clearFormErrors(errors.value);
            const result = await handleApiResponse(vacancyPageStore.deleteNote(props.item?.id as string, item.id), errors.value);

            if (!result || errors.value.message) {
                flashStore.addError({text: errors.value.message ?? 'Unable to delete vacancy note '});
                return;
            }

            flashStore.addSuccess({text:'Vacancy note was successfully deleted'});
            await loadVacancyNoteCollection();
        }
    };

</script>
