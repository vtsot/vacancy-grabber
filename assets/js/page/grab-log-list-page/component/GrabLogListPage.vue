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
        <template #item.description="{ item }">
            {{ item.description }}
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

    const grabLogListPageStore = useGrabLogListPageStore();
    const loading = computed(() => grabLogListPageStore.loading);
    const loadGrabLogCollection = _debounce(grabLogListPageStore.loadGrabLogCollection, 300);

    onMounted(() => grabLogListPageStore.filters.pushUrlParams());

    const headers = computed(() => [
        {title: 'Description', key: 'description', sortable: grabLogListPageStore.hasSortBy('description'), width: '200px'},
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
