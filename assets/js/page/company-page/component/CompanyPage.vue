<template>

    <v-row>
        <v-col cols="11">
            <v-expansion-panels :loading="loading">
                <v-expansion-panel :loading="loading">
                    <template #title><h3>Filters</h3></template>
                    <template #text>
                        <company-page-filters
                            @filters:reset="onFiltersReset"
                            @filters:update="onFiltersUpdate"
                            @filters:submit="onFiltersSubmit"
                        />
                    </template>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-col>

        <!--        <v-col cols="1">-->
        <!--            <v-btn-->
        <!--                text="Create"-->
        <!--                :loading="loading"-->
        <!--                :disabled="loading"-->
        <!--                color="primary"-->
        <!--                class="ml-5 text-right"-->
        <!--                @click="handleDialogActivator('create', null)"-->
        <!--            />-->
        <!--        </v-col>-->
    </v-row>

    <v-divider class="mt-5"/>

    <v-data-table-server
        v-model:page="companyPageStore.filters.page as number"
        v-model:items-per-page="companyPageStore.filters.perPage as number"
        v-model:items-length="companyPageStore.paginator.total as number"
        v-model:sort-by="tableSortBy"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >

        <template #item.name="{ item }">
            {{ item.name }}
        </template>

        <template #item.url="{ item }">
            <div style="max-width: 300px; overflow-wrap: break-word; white-space: normal;">
                <a :href="item.url" target="_blank">{{ item.url }}</a>
            </div>
        </template>

<!--        <template #item.active="{ item, index }">-->
<!--            <v-switch-->
<!--                v-model="item.active"-->
<!--                :loading="loading"-->
<!--                :true-icon="mdiCheck"-->
<!--                :false-icon="mdiClose"-->
<!--                color="success"-->
<!--                size="x-large"-->
<!--                @update:model-value="handleDialogActivator('active', companyPageStore.items[index])"-->
<!--            />-->
<!--        </template>-->
<!---->
<!--        <template #item.action-grab="{ item, index }">-->
<!--            <v-card-actions>-->
<!--                <v-btn-->
<!--                    :disabled="!item.active"-->
<!--                    text="Grab"-->
<!--                    class="ml-1"-->
<!--                    color="secondary"-->
<!--                    variant="tonal"-->
<!--                    @click.prevent="handleDialogActivator('grab', companyPageStore.items[index])"-->
<!--                />-->
<!--            </v-card-actions>-->
<!--        </template>-->

        <template #item.created_at="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

<!--        <template #item.actions="{ item, index }">-->
<!--            <v-card-actions>-->
<!--                <v-btn-->
<!--                    text="Edit"-->
<!--                    class="ml-1"-->
<!--                    color="primary"-->
<!--                    variant="tonal"-->
<!--                    @click.prevent="handleDialogActivator('edit', companyPageStore.items[index])"-->
<!--                />-->

<!--                <v-btn-->
<!--                    text="Delete"-->
<!--                    class="ml-1"-->
<!--                    color="error"-->
<!--                    variant="tonal"-->
<!--                    :prepend-icon="mdiDelete"-->
<!--                    @click.prevent="handleDialogActivator('delete', item)"-->
<!--                />-->
<!--            </v-card-actions>-->
<!--        </template>-->

    </v-data-table-server>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useCompanyPageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import CompanyPageFilters from "./CompanyPageFilters.vue";

    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";

    import {mdiCheck, mdiClose, mdiDelete} from "@mdi/js";
    import {formatDateTime} from "../../../utils/DateUtils";
    import CompanyInfo from "../../../model/company/CompanyInfo";
    import CompanyInfoCollectionFactory from "../../../model-factory/company/CompanyInfoCollectionFactory";
    import CompanyInfoCollection from "../../../model/company/CompanyInfoCollection";


    const companyPageStore = useCompanyPageStore();
    const loading = computed(() => companyPageStore.loading);
    const flashStore = useFlashStore();

    const headers = computed(() => [
        {title: 'Name', key: 'name', sortable: companyPageStore.hasSortBy('name'), width: '200px'},
        {title: 'URL', key: 'url', sortable: companyPageStore.hasSortBy('url')},
        // {title: 'Active', key: 'active', sortable: companyPageStore.hasSortBy('active'), width: '100px'},
        // {title: 'Grab', key: 'action-grab', sortable: false, width: '100px'},

        {title: 'Created At', key: 'created_at', sortable: companyPageStore.hasSortBy('created_at'), width: '100px'},
        // {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const item = ref(null as CompanyInfo | null);
    const items = ref([] as CompanyInfo[]);
    // made a copy, items could be changed separated from original version
    const cloneItems = () => _cloneDeep(companyPageStore.items).map(item => ({...item, edit: false}));
    const loadCollection = _debounce(async () => {
        clearFormErrors(companyPageStore.errors);
        await processApiResponse(companyPageStore.loadCollection(), companyPageStore.errors, new CompanyInfoCollectionFactory())
            .then((data: CompanyInfoCollection) => {
                // update the originally loaded items
                companyPageStore.items = data.items
                companyPageStore.paginator = data.paginator;
                companyPageStore.availableSortBy = data.filters?.availableSortBy || [];
                companyPageStore.availableFilters = {...companyPageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
                // update sort
                companyPageStore.filters.sort = data.filters?.sort ?? null;
                tableSortBy.value = companyPageStore.filters.sortBy;
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => companyPageStore.items,
        () => items.value = cloneItems(),
        {deep: true}
    );

    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        companyPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        companyPageStore.filters.page = 1;
        companyPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const tableSortBy = ref(companyPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        companyPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        companyPageStore.filters.pushUrlParams();
        loadCollection();
    };

</script>
