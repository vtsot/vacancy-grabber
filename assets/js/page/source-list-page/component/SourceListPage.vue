<template>

    <source-list-filters
        @filters:updated="onFiltersUpdated"
        @filters:submit="onFiltersSubmit"
    />

    <v-data-table-server
        v-model:page="sourceListPageStore.filters.page"
        v-model:items-per-page="sourceListPageStore.filters.perPage"
        v-model:items-length="sourceListPageStore.paginator.total"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="ItemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >
        <template #item.name="{ item }">
            {{ item.name }}
        </template>

        <template #item.url="{ item }">
            <a :href="item.url" target="_blank">{{ item.url }}</a>
        </template>

        <template #item.active="{ item, index }">
            <v-switch
                v-if="item"
                v-model="items[index].active"
                :color="items[index].active ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="onItemActiveClick(items[index])"
            />
        </template>

        <template #item.action-grab="{ item }">
            <v-btn v-if="item.active" class="ml-1" color="primary" @click.prevent="onItemGrabClick(item)" text="Grab"/>
        </template>

        <template #item.action-edit="{ item }">
            <v-btn class="ml-1" color="secondary" @click.prevent="onItemClickEdit(item)" text="Edit"/>
        </template>

        <template #item.action-delete="{ item }">
            <v-btn class="ml-1" color="error" @click.prevent="onItemDeleteClick(item)" text="Delete"/>
        </template>
    </v-data-table-server>

    <confirm-dialog
        v-if="showItemActiveDialog"
        v-model:show="showItemActiveDialog"
        @confirmed="onItemActiveConfirmed"
        @canceled="onItemActiveCanceled"
        @update:model-value="onItemActiveCanceled"
        @keyup.enter="onItemActiveConfirmed"
    />

    <confirm-dialog
        v-if="showItemGrabDialog"
        v-model:show="showItemGrabDialog"
        @confirmed="onItemGrabConfirmed"
        @canceled="onItemGrabCanceled"
        @update:model-value="onItemGrabCanceled"
        @keyup.enter="onItemGrabConfirmed"
    />

    <confirm-dialog
        v-if="showItemDeleteDialog"
        v-model:show="showItemDeleteDialog"
        @confirmed="onItemDeleteConfirmed"
        @canceled="onItemDeleteCanceled"
        @update:model-value="onItemDeleteCanceled"
        @keyup.enter="onItemDeleteCanceled"
    />

</template>

<script setup>

    import {computed, onMounted, ref, watch} from "vue";
    import {useSourceListPageStore} from "../store";
    import {ItemsPerPageOptions} from "../../../utils/ItemsPerPageOptions";
    import _debounce from 'lodash/debounce';
    import SourceListFilters from "./SourceListFilters.vue";
    import ConfirmDialog from "../../../component/general/dialog/ConfirmDialog.vue";
    import _cloneDeep from 'lodash/cloneDeep';

    const sourceListPageStore = useSourceListPageStore();
    const loadSourceCollection = _debounce(sourceListPageStore.loadSourceCollection, 300);

    onMounted(async() => loadSourceCollection());

    const loading = computed(() => sourceListPageStore.loading);

    const headers = [
        {title: 'Name', key: 'name', sortable: true, width: '200px'},
        {title: 'URL', key: 'url', sortable: true},
        {title: 'Active', key: 'active', sortable: true, width: '100px'},
        {title: '', key: 'action-grab', sortable: false, width: '100px'},
        {title: 'Actions', key: 'action-edit', sortable: false, width: '100px'},
        {title: '', key: 'action-delete', sortable: false, width: '100px'},
    ];

    const items = ref(sourceListPageStore.items);
    watch(
        () => sourceListPageStore.items,
        () => items.value = _cloneDeep(sourceListPageStore.items),
        {deep: true}
    );

    const onFiltersUpdated = () => onFiltersSubmit();
    const onFiltersSubmit = () => {
        sourceListPageStore.filters.page = 1;
        loadSourceCollection();
    };

    /**
     * @param {BaseCollectionTableOptions} options
     */
    const onTableOptionsUpdated = (options) => {
        sourceListPageStore.filters.page = options.page;
        sourceListPageStore.filters.perPage = options.itemsPerPage;

        const [sortBy] = options.sortBy || [];
        sourceListPageStore.filters.sort = sortBy?.key && sortBy?.order
            ? `${sortBy.key} ${sortBy.order}`
            : null;

        loadSourceCollection();
    };

    const onItemClickEdit = (item) => {
    };

    // active action
    const showItemActiveDialog = ref(false);
    const actionActiveItem = ref(null);
    const onItemActiveClick = (item) => {
        showItemActiveDialog.value = true;
        actionActiveItem.value = item;
    };
    const onItemActiveCanceled = () => {
        showItemActiveDialog.value = false;
        actionActiveItem.value = null;
        // restore
        items.value = _cloneDeep(sourceListPageStore.items);
    };
    const onItemActiveConfirmed = () => {
        if (actionActiveItem.value) {
            sourceListPageStore.itemActive(actionActiveItem.value.id, actionActiveItem.value.active);
            loadSourceCollection();
        }
        // hide dialog
        showItemActiveDialog.value = false;
        actionActiveItem.value = null;
    };

    // grab action
    const showItemGrabDialog = ref(false);
    const actionGrabItem = ref(null);
    const onItemGrabClick = (item) => {
        showItemGrabDialog.value = true;
        actionGrabItem.value = item;
    };
    const onItemGrabCanceled = () => {
        showItemGrabDialog.value = false;
        actionGrabItem.value = null;
    };
    const onItemGrabConfirmed = () => {
        if (actionGrabItem.value) {
            sourceListPageStore.itemGrab(actionGrabItem.value.id);
            loadSourceCollection();
        }
        // hide dialog
        showItemGrabDialog.value = false;
        actionGrabItem.value = null;
    };

    // delete action
    const showItemDeleteDialog = ref(false);
    const actionDeleteItem = ref(null);
    const onItemDeleteClick = (item) => {
        showItemDeleteDialog.value = true;
        actionDeleteItem.value = item;
    };
    const onItemDeleteCanceled = () => {
        showItemDeleteDialog.value = false;
        actionDeleteItem.value = null;
    };
    const onItemDeleteConfirmed = () => {
        if (actionDeleteItem.value?.id) {
            sourceListPageStore.itemDelete(actionDeleteItem.value.id);
            loadSourceCollection();
        }
        // hide dialog
        showItemDeleteDialog.value = false;
        actionDeleteItem.value = null;
    };
</script>
