<template>

    <v-row>
        <v-col cols="11">
            <v-expansion-panels :loading="loading">
                <v-expansion-panel :loading="loading">
                    <template #title><h3>Filters</h3></template>
                    <template #text>
                        <note-page-filters
                            @filters:reset="onFiltersReset"
                            @filters:update="onFiltersUpdate"
                            @filters:submit="onFiltersSubmit"
                        />
                    </template>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-col>
    </v-row>

    <v-divider class="mt-5"/>

    <v-data-table-server
        v-model:page="notePageStore.filters.page as number"
        v-model:items-per-page="notePageStore.filters.perPage as number"
        v-model:items-length="notePageStore.paginator.total as number"
        v-model:sort-by="tableSortBy"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >

        <template #item.company="{ item }">
            <div class="mb-5 mt-5">
                <a v-if="item.companyUrl" :href="item.companyUrl" target="_blank">{{ item.companyName }}</a>
                <template v-else>{{ item.companyName }}</template>

            </div>
        </template>

        <template #item.description="{ item }">
            <div
                style="max-height: 300px; overflow-y: auto; max-width: 800px; overflow-wrap: break-word; white-space: normal;">
                {{ item.description }}
            </div>
        </template>

        <template #item.url="{ item }">
            <div style="max-width: 300px; overflow-wrap: break-word; white-space: normal;">
                <a :href="item.url" target="_blank">{{ item.url }}</a>
            </div>
        </template>

        <template #item.created_at="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.actions="{ item, index }">
            <v-card-actions>
                <v-btn
                    text="Vacancies"
                    class="ml-1"
                    color="primary"
                    variant="tonal"
                    :href="RouteGenerator.generate('web.vacancy', filterUrlParams({company: item.companyId}))"
                />
                <v-btn
                    text="Notes"
                    class="ml-1"
                    color="primary"
                    variant="tonal"
                    :href="RouteGenerator.generate('web.note', filterUrlParams({company: item.companyId}))"
                />
            </v-card-actions>
        </template>

    </v-data-table-server>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useNotePageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import NotePageFilters from "./NotePageFilters.vue";

    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import {formatDateTime, formatDateUrl} from "../../../utils/DateUtils";
    import NoteInfo from "../../../model/note/NoteInfo";
    import NoteInfoCollectionFactory from "../../../model-factory/note/NoteInfoCollectionFactory";
    import NoteInfoCollection from "../../../model/note/NoteInfoCollection";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {filterUrlParams} from "../../../utils/UrlUtils";


    const notePageStore = useNotePageStore();
    const loading = computed(() => notePageStore.loading);
    const flashStore = useFlashStore();

    const headers = computed(() => [
        {title: 'Company', key: 'company', sortable: false, width: '200px'},
        {title: 'Note', key: 'description', sortable: false},

        {title: 'Created At', key: 'created_at', sortable: notePageStore.hasSortBy('created_at'), width: '100px'},
        {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const item = ref(null as NoteInfo | null);
    const items = ref([] as NoteInfo[]);
    // made a copy, items could be changed separated from original version
    const cloneItems = () => _cloneDeep(notePageStore.items).map(item => ({...item, edit: false}));
    const loadCollection = _debounce(async () => {
        clearFormErrors(notePageStore.errors);
        await processApiResponse(notePageStore.loadCollection(), notePageStore.errors, new NoteInfoCollectionFactory())
            .then((data: NoteInfoCollection) => {
                // update the originally loaded items
                notePageStore.items = data.items
                notePageStore.paginator = data.paginator;
                notePageStore.availableSortBy = data.filters?.availableSortBy || [];
                notePageStore.availableFilters = {...notePageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
                // update sort
                notePageStore.filters.sort = data.filters?.sort ?? null;
                tableSortBy.value = notePageStore.filters.sortBy;
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => notePageStore.items,
        () => items.value = cloneItems(),
        {deep: true}
    );

    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        notePageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        notePageStore.filters.page = 1;
        notePageStore.filters.pushUrlParams();
        loadCollection();
    };

    const tableSortBy = ref(notePageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        notePageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        notePageStore.filters.pushUrlParams();
        loadCollection();
    };

</script>
