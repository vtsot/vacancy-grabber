<template>

    <grab-log-list-filters
        @filters:update="onFiltersUpdate"
        @filters:submit="onFiltersSubmit"
    />

    <v-data-table-server
        v-model:page="grabLogListPageStore.filters.page as number"
        v-model:items-per-page="grabLogListPageStore.filters.perPage as number"
        v-model:sort-by="sortBy"
        v-model:items-length="grabLogListPageStore.paginator.total as number"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="ItemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >
        <template #item.source="{ item }">
            {{ item.source }}
        </template>

        <template #item.counterCompanyCreated="{ item }">
            <strong>{{ item.counterCompanyCreated }}</strong>
        </template>

        <template #item.counterVacancyCreated="{ item }">
            <strong>{{ item.counterVacancyCreated }}</strong>
        </template>

        <template #item.description="{ item }">
            {{ item.description }}
        </template>

        <template #item.createdAt="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.action-vacancy-list="{ item, index }">
            <v-btn
                text="See Grabbed Vacancies"
                class="ml-1"
                color="primary"
                variant="tonal"
                :href="RouteGenerator.generate('web.vacancy.list', filterUrlParams({startDay: formatDateUrl(item.createdAt), endDay: formatDateUrl(item.createdAt)}))"
            />
        </template>

    </v-data-table-server>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useGrabLogListPageStore} from "../store";
    import {ItemsPerPageOptions} from "../../../utils/ItemsPerPageOptions";
    import grabLogListFilters from "./GrabLogListFilters.vue";
    import GrabLogInfo from "../../../model/grab-log/GrabLogInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {formatDateTime, formatDateUrl} from "../../../utils/DateUtils";
    import {filterUrlParams} from "../../../utils/UrlUtils";

    const grabLogListPageStore = useGrabLogListPageStore();
    const loading = computed(() => grabLogListPageStore.loading);
    const loadGrabLogCollection = _debounce(grabLogListPageStore.loadGrabLogCollection, 300);

    onMounted(() => grabLogListPageStore.filters.pushUrlParams());

    const headers = computed(() => [
        {title: 'Description', key: 'description', sortable: grabLogListPageStore.hasSortBy('description') },
        {title: 'Source', key: 'source', sortable: grabLogListPageStore.hasSortBy('source'), width: '100px'},
        {title: 'Company Created', key: 'counterCompanyCreated', sortable: grabLogListPageStore.hasSortBy('counterCompanyCreated'), width: '200px'},
        {title: 'Vacancy Created', key: 'counterVacancyCreated', sortable: grabLogListPageStore.hasSortBy('counterVacancyCreated'), width: '200px'},
        {title: 'Created At', key: 'createdAt', sortable: grabLogListPageStore.hasSortBy('createdAt'), width: '200px'},
        {title: 'Actions', key: 'action-vacancy-list', sortable: false, width: '100px'},
    ]);

    const item = ref(null as GrabLogInfo | null);
    const items = ref(grabLogListPageStore.items);
    watch(
        () => grabLogListPageStore.items,
        () => items.value = _cloneDeep(grabLogListPageStore.items),
        {deep: true}
    );

    const onFiltersUpdate = (): void => {
        grabLogListPageStore.filters.pushUrlParams();
        loadGrabLogCollection();
    };

    const onFiltersSubmit = (): void => {
        grabLogListPageStore.filters.page = 1;
        grabLogListPageStore.filters.pushUrlParams();
        loadGrabLogCollection();
    };

    const sortBy = ref(grabLogListPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        grabLogListPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;

        grabLogListPageStore.filters.pushUrlParams();
        loadGrabLogCollection();
    };
</script>
