<template>
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
        class="hide-items-per-page-options"
    >

        <template #item.notedAt="{ item }">
            {{ formatDateTime(item.notedAt) }}
        </template>

        <template #item.description="{ item }">

            <v-confirm-edit
                v-if="item.edit"
                v-model="item.description"
                hide-actions
            >
                <template v-slot:default="{ model: proxyModel, save, cancel, isPristine }">

                    <v-textarea
                        v-model="proxyModel.value"
                        :loading="loading"
                        :clearable="false"
                        :autofocus="true"
                        label="Note"
                        type="text"
                    />

                    <v-card-actions>
                        <v-btn
                            color="error"
                            @click="cancel; item.edit=false;"
                            text="Cancel"
                        />
                        <v-spacer></v-spacer>

                        <v-btn
                            color="primary"
                            :disabled="isPristine"
                            @click="onClickSaveNote(item, proxyModel.value)"
                            text="Ok"
                        />
                    </v-card-actions>

                </template>
            </v-confirm-edit>

            <v-row v-else class="mt-5 mb-5">
                <div v-html="nl2br(item.description)"></div>
            </v-row>

        </template>

        <template #item.actions="{ item, index }">
            <v-card-actions>
                <v-btn
                    text="Edit"
                    color="primary"
                    :disabled="item.edit"
                    :prepend-icon="mdiPencil"
                    @click.prevent="item.edit=true"
                />

                <v-btn
                    class="ml-3"
                    text="Delete"
                    color="error"
                    :prepend-icon="mdiDelete"
                    @click.prevent="handleConfirmDelete(item)"
                />
            </v-card-actions>
        </template>

    </v-data-table-server>

</template>

<script setup lang="ts">
    import {computed, onMounted, PropType, ref, watch} from "vue";
    import VacancyInfo from "../../../../model/vacancy/VacancyInfo";
    import VacancyNote from "../../../../model/vacancy/VacancyNote";
    import VacancyNoteFilters from "../../../../model/vacancy/VacancyNoteFilters";
    import VacancyNoteCollectionFactory from "../../../../model-factory/vacancy/VacancyNoteCollectionFactory";
    import ErrorResponse from "../../../../model/transport-response/ErrorResponse";
    import ErrorValidation from "../../../../model/transport-response/ErrorValidation";
    import handleApiResponse, {clearFormErrors, processApiResponse} from "../../../../module/HandleApiResponse";
    import {useVacancyPageStore} from "../../store";
    import {useFlashStore} from "../../../../component/general/app-flash/store";
    import {formatDateTime} from "../../../../utils/DateUtils";
    import {nl2br} from "../../../../utils/HtmlUtils";
    import _cloneDeep from "lodash/cloneDeep";
    import Paginator from "../../../../model/transport-response/Paginator";
    import _debounce from "lodash/debounce";
    import VacancyInfoCollection from "../../../../model/vacancy/VacancyInfoCollection";
    import {itemsPerPageOptions} from "../../../../utils/itemsPerPageOptions";
    import {mdiDelete, mdiPencil} from "@mdi/js";
    import BaseCollectionTableOptions, {SortByInterface} from "../../../../types/BaseCollectionTableOptions";
    import {scrollToDialogText} from "../../../../utils/ScrollUtils";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const vacancyPageStore = useVacancyPageStore();
    const flashStore = useFlashStore();
    const emit = defineEmits(['deleted']);

    const loading = computed(() => vacancyPageStore.loading);
    const items = ref<VacancyNote[]>([] as VacancyNote[]);
    // made a copy, items could be changed separated from original version
    const itemsLoaded = ref([] as VacancyNote[]); // original loaded items
    const cloneItems = () => _cloneDeep(itemsLoaded.value).map(item => ({...item, edit: false}));

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
                itemsLoaded.value = data.items
                paginator.value = data.paginator;
                availableSortBy.value = data.filters?.availableSortBy || [];
                availableFilters.value = {...vacancyPageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => itemsLoaded.value,
        () => items.value = cloneItems(),
        {deep: true}
    );

    onMounted(async () => {
        await loadVacancyNoteCollection();
    });

    const tableSortBy = ref(filters.value.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = async (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        filters.value.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        await loadVacancyNoteCollection();
        scrollToDialogText();
    };

    const headers = computed(() => [
        {title: 'Noted At', key: 'notedAt', sortable: true, width: '150px'},
        {title: 'Note', key: 'description', sortable: false},

        {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const handleConfirmDelete = async (item: VacancyNote) => {
        if (window.confirm("Do you really want to delete the note?")) {
            clearFormErrors(errors.value);
            const result = await handleApiResponse(vacancyPageStore.deleteNote(props.item?.id as string, item.id), errors.value);

            if (!result || errors.value.message) {
                flashStore.addError({text: errors.value.message ?? 'Unable to delete vacancy note '});
                return;
            }

            flashStore.addSuccess({text: 'Vacancy note was successfully deleted'});
            await loadVacancyNoteCollection();
            emit('deleted');
        }
    };

    const onClickSaveNote = async (item, description: string | null) => {
        clearFormErrors(errors.value);
        const data = {properties: ['description'], description: description};
        const result = await handleApiResponse(vacancyPageStore.patchNote(props.item?.id as string, item.id as string, data), errors.value)

        if (!result || errors.value.message) {
            flashStore.addError({text: errors.value.message ?? 'Unable to update vacancy note '});
            return;
        }

        flashStore.addSuccess({text: 'Vacancy note was successfully updated'});
        item.edit = false;
        await loadVacancyNoteCollection();
    };
</script>

<style lang="scss">
    .v-table.hide-items-per-page-options .v-data-table-footer__items-per-page {
        display: none !important;
    }
</style>
