<template>

    <grab-log-page-filters
        @filters:update="onFiltersUpdate"
        @filters:submit="onFiltersSubmit"
    />

    <v-data-table-server
        v-model:page="pageStore.filters.page as number"
        v-model:items-per-page="pageStore.filters.perPage as number"
        v-model:sort-by="sortBy"
        v-model:items-length="pageStore.paginator.total as number"
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

        <template #item.actions="{ item, index }">
            <v-btn
                text="See Grabbed Vacancies"
                class="ml-1"
                color="primary"
                variant="tonal"
                :href="RouteGenerator.generate('web.vacancy', filterUrlParams({startDay: formatDateUrl(item.createdAt), endDay: formatDateUrl(item.createdAt)}))"
            />
        </template>

    </v-data-table-server>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {usePageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import grabLogPageFilters from "./GrabLogPageFilters.vue";
    import GrabLogInfo from "../../../model/grab-log/GrabLogInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {formatDateTime, formatDateUrl} from "../../../utils/DateUtils";
    import {filterUrlParams} from "../../../utils/UrlUtils";

    const pageStore = usePageStore();
    const loading = computed(() => pageStore.loading);
    const loadCollection = _debounce(pageStore.loadCollection, 300);

    onMounted(() => pageStore.filters.pushUrlParams());

    const headers = computed(() => [
        {title: 'Description', key: 'description', sortable: pageStore.hasSortBy('description') },
        {title: 'Source', key: 'source', sortable: pageStore.hasSortBy('source'), width: '100px'},
        {title: 'Company Created', key: 'counterCompanyCreated', sortable: pageStore.hasSortBy('counterCompanyCreated'), width: '200px'},
        {title: 'Vacancy Created', key: 'counterVacancyCreated', sortable: pageStore.hasSortBy('counterVacancyCreated'), width: '200px'},
        {title: 'Created At', key: 'createdAt', sortable: pageStore.hasSortBy('createdAt'), width: '200px'},
        {title: 'Actions', key: 'actions', sortable: false, width: '100px'},
    ]);

    const item = ref(null as GrabLogInfo | null);
    const items = ref(pageStore.items);
    watch(
        () => pageStore.items,
        () => items.value = _cloneDeep(pageStore.items),
        {deep: true}
    );

    const onFiltersUpdate = (): void => {
        pageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        pageStore.filters.page = 1;
        pageStore.filters.pushUrlParams();
        loadCollection();
    };

    const sortBy = ref(pageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        pageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;

        pageStore.filters.pushUrlParams();
        loadCollection();
    };
</script>
