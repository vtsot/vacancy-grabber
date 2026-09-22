<template>

    <source-list-filters
        @filters:update="onFiltersUpdate"
        @filters:submit="onFiltersSubmit"
        @create:button="handleDialogActivator('create', null)"
    />

    <v-data-table-server
        v-model:page="sourceListPageStore.filters.page as number"
        v-model:items-per-page="sourceListPageStore.filters.perPage as number"
        v-model:sort-by="sortBy"
        v-model:items-length="sourceListPageStore.paginator.total as number"
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
                v-model="item.active"
                :color="item.active ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="handleDialogActivator('active', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-grab="{ item, index }">
            <v-btn
                v-if="item.active"
                text="Grab"
                class="ml-1"
                color="secondary"
                variant="tonal"
                @click.prevent="handleDialogActivator('grab', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-edit="{ item, index }">
            <v-btn
                text="Edit"
                class="ml-1"
                color="primary"
                variant="tonal"
                @click.prevent="handleDialogActivator('edit', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-delete="{ item, index }">
            <v-btn
                text="Delete"
                class="ml-1"
                color="error"
                variant="tonal"
                @click.prevent="handleDialogActivator('delete', sourceListPageStore.items[index])"
            />
        </template>
    </v-data-table-server>

    <source-item-active-dialog
        v-if="dialogs.active.model"
        v-model:dialog="dialogs.active.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('active', true)"
        @canceled="handleDialogDeActivator('active', false)"
    />

    <source-item-grab-dialog
        v-if="dialogs.grab.model"
        v-model:dialog="dialogs.grab.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('grab', true)"
        @canceled="handleDialogDeActivator('grab', false)"
    />

    <source-item-create-dialog
        v-if="dialogs.create.model"
        v-model:dialog="dialogs.create.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('create', true)"
        @canceled="handleDialogDeActivator('create', false)"
    />

    <source-item-edit-dialog
        v-if="dialogs.edit.model"
        v-model:dialog="dialogs.edit.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('edit', true)"
        @canceled="handleDialogDeActivator('edit', false)"
    />

    <source-item-delete-dialog
        v-if="dialogs.delete.model"
        v-model:dialog="dialogs.delete.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('delete', true)"
        @canceled="handleDialogDeActivator('delete', false)"
    />

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useSourceListPageStore} from "../store";
    import {ItemsPerPageOptions} from "../../../utils/ItemsPerPageOptions";
    import SourceListFilters from "./SourceListFilters.vue";
    import SourceItemActiveDialog from "./SourceItemActiveDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import SourceItemGrabDialog from "./SourceItemGrabDialog.vue";
    import SourceItemEditDialog from "./SourceItemEditDialog.vue";
    import SourceItemDeleteDialog from "./SourceItemDeleteDialog.vue";
    import SourceItemCreateDialog from "./SourceItemCreateDialog.vue";
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";

    const sourceListPageStore = useSourceListPageStore();
    const loading = computed(() => sourceListPageStore.loading);
    const loadSourceCollection = _debounce(sourceListPageStore.loadSourceCollection, 300);

    onMounted(() => sourceListPageStore.filters.pushUrlParams());

    const headers = computed(() => [
        {title: 'Name', key: 'name', sortable: sourceListPageStore.hasSortBy('name'), width: '200px'},
        {title: 'URL', key: 'url', sortable: sourceListPageStore.hasSortBy('url')},
        {title: 'Active', key: 'active', sortable: sourceListPageStore.hasSortBy('active'), width: '100px'},
        {title: '', key: 'action-grab', sortable: false, width: '100px'},
        {title: 'Actions', key: 'action-edit', sortable: false, width: '100px'},
        {title: '', key: 'action-delete', sortable: false, width: '100px'},
    ]);

    const item = ref(null as SourceInfo | null);
    const items = ref(sourceListPageStore.items);
    watch(
        () => sourceListPageStore.items,
        () => items.value = _cloneDeep(sourceListPageStore.items),
        {deep: true}
    );

    const onFiltersUpdate = (): void => {
        sourceListPageStore.filters.pushUrlParams();
        loadSourceCollection();
    };

    const onFiltersSubmit = (): void => {
        sourceListPageStore.filters.page = 1;
        sourceListPageStore.filters.pushUrlParams();
        loadSourceCollection();
    };

    const sortBy = ref(sourceListPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        sourceListPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;

        sourceListPageStore.filters.pushUrlParams();
        loadSourceCollection();
    };

    // dialogs
    const dialogs = ref({
        active: {model: false},
        grab: {model: false},
        edit: {model: false},
        delete: {model: false},
        create: {model: false},
    });

    const handleDialogActivator = (action: string, sourceItem: SourceInfo | null) => {
        item.value = sourceItem;

        // open dialog
        dialogs.value[action] && (dialogs.value[action].model = true);
    };

    const handleDialogDeActivator = (action: string, confirmed: boolean): void => {
        // restore
        if (!confirmed) {
            item.value = null;
            items.value = _cloneDeep(sourceListPageStore.items);
            dialogs.value[action] && (dialogs.value[action].model = false);

            return;
        }

        setTimeout(() => {
            Promise
                .all([loadSourceCollection()])
                .then(() => {
                    item.value = null;
                    // close dialog
                    dialogs.value[action] && (dialogs.value[action].model = false);
                });
        }, 300);
    };

</script>
