export interface SortByInterface {
    key: string;
    order: 'asc' | 'desc';
}

export default interface BaseCollectionTableOptions {
    page: number;
    itemsPerPage: number;
    sortBy: SortByInterface[];
}
