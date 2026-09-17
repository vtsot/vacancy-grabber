<template>

    <br>loading: {{ loading }}

    <source-list-filters
        :model-value="filters"
        @update:filters="onUpdateFilters"
        @submit="onUpdateFilters"
    />

    <v-data-table-server
        v-model:page="filters.page"
        v-model:items-per-page="filters.perPage"
        v-model:items-length="itemsTotal"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="batchItems"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onUpdateOptions"
    >
        <template #item.name="{ item }">
            {{ item.name }}
        </template>

        <template #item.url="{ item }">
            <a :href="item.url" target="_blank">{{ item.url }}</a>
        </template>

        <template #item.active="{ item }">
            <v-switch
                v-model="item.active"
                :color="item.active ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="onUpdateActive(item)"
            />
        </template>

        <template #item.action="{ item }">
            <v-btn class="ml-1" color="primary" text="Grab"/>
            <v-btn class="ml-1" color="secondary" text="Edit"/>
            <v-btn class="ml-1" color="error" text="Delete"/>
            <v-btn class="ml-1" color="warning" text="Delete"/>
        </template>
    </v-data-table-server>

</template>

<script>
    import {defineComponent} from "vue";
    import {mapStores, mapState, mapActions} from "pinia";
    import {useSourceListPageStore} from '../store';
    import SourceListFilters from "./SourceListFilters.vue";
    import SourceInfoFilters from "../../../model/source/SourceInfoFilters";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";

    export default defineComponent({
        components: {SourceListFilters},

        computed: {
            ...mapState(useSourceListPageStore, ['loading', 'items', 'paginator',]),

            headers() {
                return [
                    {title: 'Name', key: 'name', sortable: true},
                    {title: 'URL', key: 'url', sortable: true},
                    {title: 'Active', key: 'active', sortable: true},
                    {title: 'Action', key: 'action', sortable: false},
                ];
            },
        },


        watch: {
            'paginator.total': {
                deep: true,
                handler(v) {
                    this.itemsTotal = v;
                },
            },

            items: {
                deep: true,
                handler(v) {
                    this.batchItems = v;
                },
            },
        },

        methods: {
            onUpdateFilters(filters) {
                this.sourceListPageStore.loadSourceCollection(filters);
            },

            /**
             * @param {BaseCollectionTableOptions} options
             */
            onUpdateOptions(options) {
                this.filters.page = options.page;
                this.filters.perPage = options.itemsPerPage;

                const [sortBy] = options.sortBy || [];
                this.filters.sort = sortBy?.key && sortBy?.order
                    ? `${sortBy.key} ${sortBy.order}`
                    : null;

                this.sourceListPageStore.loadSourceCollection(this.filters);
            },

            onUpdateActive(item) {
                this.sourceListPageStore.saveActive(item);
            },
        },


        setup() {
            const sourceListPageStore = useSourceListPageStore();

            return {
                itemsPerPageOptions,
                sourceListPageStore,
            };
        },

        mounted() {
            this.sourceListPageStore.loadSourceCollection(this.filters);
        },


        data() {
            return {
                filters: new SourceInfoFilters(),
                itemsTotal: 0,
                batchItems: [],
            };
        },
    });

    // import { ref } from 'vue'
    //
    // export default {
    //     setup() {
    //         const loading = ref(false);
    //         const headers = ref([
    //             { title: 'ID', key: 'id', sortable: false },
    //             { title: 'Customer', key: 'customer', sortable: false },
    //             { title: 'Cards', key: 'cards', sortable: false },
    //             { title: 'Vehicles', key: 'vehicles', sortable: false },
    //             { title: 'Created at', key: 'created_at', sortable: true },
    //         ]);
    //     },
    //
    //     created() {
    //
    //     }
    // }
    //
    // // const desserts = [
    // //     {
    // //         name: 'Frozen Yogurt',
    // //         calories: 159,
    // //         fat: 6.0,
    // //         carbs: 24,
    // //         protein: 4.0,
    // //         iron: '1',
    // //     },
    // //     {
    // //         name: 'Jelly bean',
    // //         calories: 375,
    // //         fat: 0.0,
    // //         carbs: 94,
    // //         protein: 0.0,
    // //         iron: '0',
    // //     },
    // //     {
    // //         name: 'KitKat',
    // //         calories: 518,
    // //         fat: 26.0,
    // //         carbs: 65,
    // //         protein: 7,
    // //         iron: '6',
    // //     },
    // //     {
    // //         name: 'Eclair',
    // //         calories: 262,
    // //         fat: 16.0,
    // //         carbs: 23,
    // //         protein: 6.0,
    // //         iron: '7',
    // //     },
    // //     {
    // //         name: 'Gingerbread',
    // //         calories: 356,
    // //         fat: 16.0,
    // //         carbs: 49,
    // //         protein: 3.9,
    // //         iron: '16',
    // //     },
    // //     {
    // //         name: 'Ice cream sandwich',
    // //         calories: 237,
    // //         fat: 9.0,
    // //         carbs: 37,
    // //         protein: 4.3,
    // //         iron: '1',
    // //     },
    // //     {
    // //         name: 'Lollipop',
    // //         calories: 392,
    // //         fat: 0.2,
    // //         carbs: 98,
    // //         protein: 0,
    // //         iron: '2',
    // //     },
    // //     {
    // //         name: 'Cupcake',
    // //         calories: 305,
    // //         fat: 3.7,
    // //         carbs: 67,
    // //         protein: 4.3,
    // //         iron: '8',
    // //     },
    // //     {
    // //         name: 'Honeycomb',
    // //         calories: 408,
    // //         fat: 3.2,
    // //         carbs: 87,
    // //         protein: 6.5,
    // //         iron: '45',
    // //     },
    // //     {
    // //         name: 'Donut',
    // //         calories: 452,
    // //         fat: 25.0,
    // //         carbs: 51,
    // //         protein: 4.9,
    // //         iron: '22',
    // //     },
    // // ]
    //
    // // const FakeAPI = {
    // //     async fetch({page, itemsPerPage, sortBy}) {
    // //         return new Promise(resolve => {
    // //             setTimeout(() => {
    // //                 const start = (page - 1) * itemsPerPage
    // //                 const end = start + itemsPerPage
    // //                 const items = desserts.slice()
    // //
    // //                 if (sortBy.length) {
    // //                     const sortKey = sortBy[0].key
    // //                     const sortOrder = sortBy[0].order
    // //                     items.sort((a, b) => {
    // //                         const aValue = a[sortKey]
    // //                         const bValue = b[sortKey]
    // //                         return sortOrder === 'desc' ? bValue - aValue : aValue - bValue
    // //                     })
    // //                 }
    // //
    // //                 const paginated = items.slice(start, end)
    // //
    // //                 resolve({items: paginated, total: items.length})
    // //             }, 500)
    // //         })
    // //     },
    // // }
    // //
    // // export default {
    // //     data: () => ({
    // //         itemsPerPage: 5,
    // //         headers: [
    // //             {
    // //                 title: 'Dessert (100g serving)',
    // //                 align: 'start',
    // //                 sortable: false,
    // //                 key: 'name',
    // //             },
    // //             {title: 'Calories', key: 'calories', align: 'end'},
    // //             {title: 'Fat (g)', key: 'fat', align: 'end'},
    // //             {title: 'Carbs (g)', key: 'carbs', align: 'end'},
    // //             {title: 'Protein (g)', key: 'protein', align: 'end'},
    // //             {title: 'Iron (%)', key: 'iron', align: 'end'},
    // //         ],
    // //         search: '',
    // //         serverItems: [],
    // //         loading: true,
    // //         totalItems: 0,
    // //     }),
    // //     methods: {
    // //         loadItems({page, itemsPerPage, sortBy}) {
    // //             this.loading = true
    // //             FakeAPI.fetch({page, itemsPerPage, sortBy}).then(({items, total}) => {
    // //                 this.serverItems = items
    // //                 this.totalItems = total
    // //                 this.loading = false
    // //             })
    // //         },
    // //     },
    // // }
</script>
