<template>

    <grab-log-page-filters
        @filters:update="onFiltersUpdate"
        @filters:submit="onFiltersSubmit"
    />

    <v-data-table-server
        v-model:page="grabLogPageStore.filters.page as number"
        v-model:items-per-page="grabLogPageStore.filters.perPage as number"
        v-model:sort-by="sortBy"
        v-model:items-length="grabLogPageStore.paginator.total as number"
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

    const grabLogPageStore = useGrabLogPageStore();
    const loading = computed(() => grabLogPageStore.loading);
    const loadCollection = _debounce(grabLogPageStore.loadCollection, 300);

    onMounted(() => grabLogPageStore.filters.pushUrlParams());

    const headers = computed(() => [
        {title: 'Description', key: 'description', sortable: grabLogPageStore.hasSortBy('description') },
        {title: 'Source', key: 'source', sortable: grabLogPageStore.hasSortBy('source'), width: '100px'},
        {title: 'Company Created', key: 'counterCompanyCreated', sortable: grabLogPageStore.hasSortBy('counterCompanyCreated'), width: '200px'},
        {title: 'Vacancy Created', key: 'counterVacancyCreated', sortable: grabLogPageStore.hasSortBy('counterVacancyCreated'), width: '200px'},
        {title: 'Created At', key: 'createdAt', sortable: grabLogPageStore.hasSortBy('createdAt'), width: '200px'},
        {title: 'Actions', key: 'actions', sortable: false, width: '100px'},
    ]);

    const item = ref(null as GrabLogInfo | null);
    const items = ref(grabLogPageStore.items);
    watch(
        () => grabLogPageStore.items,
        () => items.value = _cloneDeep(grabLogPageStore.items),
        {deep: true}
    );

    const onFiltersUpdate = (): void => {
        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        grabLogPageStore.filters.page = 1;
        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const sortBy = ref(grabLogPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        grabLogPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;

        grabLogPageStore.filters.pushUrlParams();
        loadCollection();
    };
</script>
