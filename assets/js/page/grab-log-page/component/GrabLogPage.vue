<template>

    <v-row>
        <v-col cols="11">
            <v-expansion-panels :loading="loading">
                <v-expansion-panel :loading="loading">
                    <template #title><h3>Filters</h3></template>
                    <template #text>
                        <grab-log-page-filters
                            @filters:reset="onFiltersReset"
                            @filters:update="onFiltersUpdate"
                            @filters:submit="onFiltersSubmit"
                        />
                    </template>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-col>

        <v-col cols="1">
        </v-col>

    </v-row>

    <v-divider class="mt-5"/>

    <v-data-table-server
        v-model:page="grabLogPageStore.filters.page as number"
        v-model:items-per-page="grabLogPageStore.filters.perPage as number"
        v-model:items-length="grabLogPageStore.paginator.total as number"
        v-model:sort-by="tableSortBy"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >
        <template #item.source="{ item }">
            {{ item.source }}
        </template>

        <template #item.counter_company_created="{ item }">
            <strong>{{ item.counterCompanyCreated }}</strong>
        </template>

        <template #item.counter_vacancy_created="{ item }">
            <strong>{{ item.counterVacancyCreated }}</strong>
        </template>

        <template #item.description="{ item }">
            {{ item.description }}
        </template>

        <template #item.created_at="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.actions="{ item, index }">
            <v-card-actions class="mb-5">
                <v-btn
                    text="See Grabbed Vacancies"
                    class="ml-1"
                    color="primary"
                    variant="tonal"
                    :href="RouteGenerator.generate('web.vacancy', filterUrlParams({startDay: formatDateUrl(item.createdAt), endDay: formatDateUrl(item.createdAt)}))"
                />
            </v-card-actions>
        </template>

    </v-data-table-server>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useGrabLogPageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import grabLogPageFilters from "./GrabLogPageFilters.vue";
    import GrabLogInfo from "../../../model/grab-log/GrabLogInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {formatDateTime, formatDateUrl} from "../../../utils/DateUtils";
    import {filterUrlParams} from "../../../utils/UrlUtils";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import GrabLogInfoCollectionFactory from "../../../model-factory/grab-log/GrabLogInfoCollectionFactory";
    import GrabLogInfoCollection from "../../../model/grab-log/GrabLogInfoCollection";
    import {scrollToDataTable} from "../../../utils/ScrollUtils";

    const grabLogPageStore = useGrabLogPageStore();
    const loading = computed(() => grabLogPageStore.loading);
    const flashStore = useFlashStore();

    const headers = computed(() => [
        //{title: 'Description', key: 'description', sortable: grabLogPageStore.hasSortBy('description') },
        {title: 'Source', key: 'source', sortable: grabLogPageStore.hasSortBy('source'), width: '100px'},
        {title: 'Company Created', key: 'counter_company_created', sortable: grabLogPageStore.hasSortBy('counter_company_created'), width: '200px'},
        {title: 'Vacancy Created', key: 'counter_vacancy_created', sortable: grabLogPageStore.hasSortBy('counter_vacancy_created'), width: '200px'},
        {title: 'Created At', key: 'created_at', sortable: grabLogPageStore.hasSortBy('created_at'), width: '200px'},
        {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const item = ref(null as GrabLogInfo | null);
    const items = ref([] as GrabLogInfo[]);
    // made a copy, items could be changed separated from original version
    const cloneItems = () => _cloneDeep(grabLogPageStore.items).map(item => ({...item, edit: false}));
    const loadCollection = _debounce(async () => {
        clearFormErrors(grabLogPageStore.errors);
        await processApiResponse(grabLogPageStore.loadCollection(), grabLogPageStore.errors, new GrabLogInfoCollectionFactory())
            .then((data: GrabLogInfoCollection) => {
                // update the originally loaded items
                grabLogPageStore.items = data.items
                grabLogPageStore.paginator = data.paginator;
                grabLogPageStore.availableSortBy = data.filters?.availableSortBy || [];
                grabLogPageStore.availableFilters = {...grabLogPageStore.availableFilters.value, ...data.filters?.availableFilters ?? {}};
                // update sort
                grabLogPageStore.filters.sort = data.filters?.sort ?? null;
                tableSortBy.value = grabLogPageStore.filters.sortBy;
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => grabLogPageStore.items,
        () => items.value = cloneItems(),
        {deep: true}
    );

    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        grabLogPageStore.filters.page = 1;
        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
    };
    const tableSortBy = ref(grabLogPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        grabLogPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
        scrollToDataTable();
    };
</script>
